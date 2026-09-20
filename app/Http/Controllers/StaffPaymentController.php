<?php

namespace App\Http\Controllers;

use App\Models\NightSession;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Services\NightSessionService;
use Illuminate\Http\Request;

class StaffPaymentController extends Controller
{
    protected NightSessionService $sessionService;

    public function __construct(NightSessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function index(Request $request)
    {
        $sessionId = $request->get('session_id');
        $session = $sessionId 
            ? NightSession::find($sessionId) 
            : NightSession::orderByDesc('session_date')->first();

        $allSessions = NightSession::orderByDesc('session_date')->get();

        $attendances = collect();
        $availableStaff = collect();
        $totalPlanilla = 0;
        $totalPagado = 0;
        $totalPendiente = 0;

        // Desglose por tipo de personal
        $summaryByType = [
            'meseros' => ['count' => 0, 'total' => 0, 'paid' => 0],
            'bartenders' => ['count' => 0, 'total' => 0, 'paid' => 0],
            'seguridad' => ['count' => 0, 'total' => 0, 'paid' => 0],
            'otros' => ['count' => 0, 'total' => 0, 'paid' => 0],
        ];

        if ($session) {
            $attendances = StaffAttendance::with('staff')
                ->where('night_session_id', $session->id)
                ->get();

            // Si la noche no tiene personal cargado, cargar según el día de la semana
            if ($attendances->isEmpty()) {
                $dayName = $session->day_name;
                $allStaff = Staff::where('is_active', true)->get();
                foreach ($allStaff as $member) {
                    if ($member->worksOnDay($dayName)) {
                        StaffAttendance::create([
                            'night_session_id' => $session->id,
                            'staff_id' => $member->id,
                            'pay_amount' => $member->getPayForDay($dayName),
                            'is_paid' => false,
                        ]);
                    }
                }
                $attendances = StaffAttendance::with('staff')
                    ->where('night_session_id', $session->id)
                    ->get();
            }

            foreach ($attendances as $att) {
                $amount = (float)$att->pay_amount;
                $isPaid = (bool)$att->is_paid;
                $role = mb_strtolower($att->staff->role ?? '');

                $totalPlanilla += $amount;
                if ($isPaid) {
                    $totalPagado += $amount;
                } else {
                    $totalPendiente += $amount;
                }

                if (str_contains($role, 'seguridad') || str_contains($att->staff->name, '(s)')) {
                    $type = 'seguridad';
                } elseif (str_contains($role, 'bartender')) {
                    $type = 'bartenders';
                } elseif (str_contains($role, 'mozo') || str_contains($role, 'meser') || str_contains($role, 'limpieza')) {
                    $type = 'meseros';
                } else {
                    $type = 'otros';
                }

                $summaryByType[$type]['count']++;
                $summaryByType[$type]['total'] += $amount;
                if ($isPaid) {
                    $summaryByType[$type]['paid'] += $amount;
                }
            }

            $assignedStaffIds = $attendances->pluck('staff_id')->toArray();
            $availableStaff = Staff::where('is_active', true)
                ->whereNotIn('id', $assignedStaffIds)
                ->orderBy('name')
                ->get();
        }

        return view('staff.payments', compact(
            'session',
            'allSessions',
            'attendances',
            'availableStaff',
            'totalPlanilla',
            'totalPagado',
            'totalPendiente',
            'summaryByType'
        ));
    }

    public function update(Request $request)
    {
        $attendancesData = $request->input('attendances', []);

        foreach ($attendancesData as $id => $data) {
            $att = StaffAttendance::find($id);
            if ($att) {
                $att->update([
                    'pay_amount' => (float)($data['pay_amount'] ?? 0),
                    'is_paid' => isset($data['is_paid']) && $data['is_paid'] == '1',
                ]);
            }
        }

        if ($request->has('night_session_id')) {
            $session = NightSession::find($request->input('night_session_id'));
            if ($session) {
                $this->sessionService->recalculateClosing($session);
            }
        }

        return back()->with('success', 'Planilla de pagos actualizada.');
    }

    public function markAllPaid(Request $request, NightSession $session)
    {
        StaffAttendance::where('night_session_id', $session->id)->update(['is_paid' => true]);
        $this->sessionService->recalculateClosing($session);

        return back()->with('success', 'Todo el personal ha sido marcado como pagado.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'night_session_id' => 'required|exists:night_sessions,id',
            'staff_id' => 'required|exists:staff,id',
            'pay_amount' => 'nullable|numeric|min:0',
        ]);

        $session = NightSession::findOrFail($validated['night_session_id']);
        $staff = Staff::findOrFail($validated['staff_id']);

        $payAmount = isset($validated['pay_amount']) && $validated['pay_amount'] > 0
            ? (float)$validated['pay_amount']
            : $staff->getPayForDay($session->day_name);

        StaffAttendance::firstOrCreate(
            [
                'night_session_id' => $session->id,
                'staff_id' => $staff->id,
            ],
            [
                'pay_amount' => $payAmount,
                'is_paid' => false,
            ]
        );

        $this->sessionService->recalculateClosing($session);

        return back()->with('success', "Personal {$staff->name} agregado a la planilla.");
    }

    public function destroy(StaffAttendance $attendance)
    {
        $session = $attendance->nightSession;
        $name = $attendance->staff->name ?? 'Personal';
        $attendance->delete();

        if ($session) {
            $this->sessionService->recalculateClosing($session);
        }

        return back()->with('success', "{$name} eliminado de la planilla de esta noche.");
    }
}
