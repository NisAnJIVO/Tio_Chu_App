<?php

namespace App\Http\Controllers;

use App\Models\BarSale;
use App\Models\CashClosing;
use App\Models\NightSession;
use App\Models\Product;
use App\Models\Staff;
use App\Models\StaffAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NightSessionController extends Controller
{
    public function index()
    {
        $sessions = NightSession::orderByDesc('session_date')->get();
        return view('sessions.index', compact('sessions'));
    }

    public function create()
    {
        return view('sessions.create');
    }

    public function store(Request $request)
    {
        $today = Carbon::now()->format('Y-m-d');

        $validated = $request->validate([
            'session_date' => 'required|date|unique:night_sessions,session_date|after_or_equal:' . $today,
            'pos_commission_rate' => 'required|numeric|min:0|max:1',
            'notes' => 'nullable|string',
        ], [
            'session_date.after_or_equal' => 'No puedes programar una noche en una fecha que ya pasó (ayer o anterior).',
            'session_date.unique' => 'Ya existe una noche registrada para esta fecha.',
        ]);

        // Determinar automáticamente el día de la semana en español
        $diasEspañol = [
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
        ];
        $parsedDate = Carbon::parse($validated['session_date']);
        $validated['day_name'] = $diasEspañol[$parsedDate->dayOfWeek] ?? 'Viernes';

        $session = NightSession::create($validated);

        // Inicializar resumen de cierre
        CashClosing::create(['night_session_id' => $session->id]);

        // Cargar personal activo según el día de la semana (Sábado viene más personal que Viernes y Domingo)
        $dayName = $validated['day_name'];
        $staff = Staff::where('is_active', true)->get();
        foreach ($staff as $member) {
            if ($member->worksOnDay($dayName)) {
                StaffAttendance::create([
                    'night_session_id' => $session->id,
                    'staff_id' => $member->id,
                    'pay_amount' => $member->getPayForDay($dayName),
                    'is_paid' => false,
                ]);
            }
        }

        // Cargar productos en los 3 puntos de venta (Barra Kelly, Barra Ariel, Tienda)
        $products = Product::where('is_active', true)->get();
        foreach (['Barra Kelly (Principal)', 'Barra Ariel (Subte)', 'Tienda'] as $bar) {
            foreach ($products as $prod) {
                BarSale::create([
                    'night_session_id' => $session->id,
                    'product_id' => $prod->id,
                    'bar_name' => $bar,
                    'packages' => 0,
                    'units' => 0,
                    'total_initial' => 0,
                    'saldo' => 0,
                    'vendido' => 0,
                    'unit_price' => $prod->sale_price,
                    'subtotal' => 0,
                ]);
            }
        }

        return redirect()->route('sessions.index')->with('success', 'Noche creada para el ' . $validated['day_name'] . ' ' . $parsedDate->format('d/m/Y') . ' con inventarios inicializados.');
    }

    public function updateStatus(Request $request, NightSession $session)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,closed',
            'pos_commission_rate' => 'nullable|numeric|min:0|max:1',
        ]);

        $session->update($validated);

        return back()->with('success', 'Estado de la noche actualizado.');
    }

    public function destroy(NightSession $session)
    {
        $dateFormatted = Carbon::parse($session->session_date)->format('d/m/Y');
        $session->delete();

        return redirect()->route('sessions.index')->with('success', 'La noche del ' . $dateFormatted . ' ha sido eliminada correctamente.');
    }
}
