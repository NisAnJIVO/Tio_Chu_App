<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\NightSession;
use App\Services\NightSessionService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected NightSessionService $sessionService;

    public function __construct(NightSessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function index(Request $request)
    {
        $session = $this->sessionService->resolveSession($request->get('session_id'));
        $allSessions = NightSession::orderByDesc('session_date')->get();

        $invoices = collect();
        $totalTarjeta = 0;
        $totalTarjetaComision = 0;
        $totalTarjetaNeto = 0;
        $totalEfectivo = 0;
        $totalGeneral = 0;
        $nextCorrelative = 1;

        if ($session) {
            $invoices = Invoice::where('night_session_id', $session->id)
                ->orderBy('correlative_num')
                ->get();

            $nextCorrelative = ($invoices->max('correlative_num') ?? 0) + 1;

            $cardInvoices = $invoices->where('payment_method', 'tarjeta');
            $cashInvoices = $invoices->where('payment_method', 'efectivo');

            $totalTarjeta = $cardInvoices->sum('amount');
            $totalTarjetaComision = $cardInvoices->sum('commission_amount');
            $totalTarjetaNeto = $cardInvoices->sum('net_amount');
            $totalEfectivo = $cashInvoices->sum('amount');
            $totalGeneral = $totalTarjeta + $totalEfectivo;
        }

        return view('invoices.index', compact(
            'session',
            'allSessions',
            'invoices',
            'nextCorrelative',
            'totalTarjeta',
            'totalTarjetaComision',
            'totalTarjetaNeto',
            'totalEfectivo',
            'totalGeneral'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'night_session_id' => 'required|exists:night_sessions,id',
            'correlative_num' => 'required|integer|min:1',
            'payment_method' => 'required|in:tarjeta,efectivo',
            'bar_name' => 'nullable|string|in:Principal,Subterraneo,Tienda,subterraneo,tienda,principal',
            'amount' => 'required|numeric|min:0.01',
            'commission_rate' => 'nullable|numeric|min:0|max:1',
            'notes' => 'nullable|string|max:255',
        ]);

        $session = NightSession::findOrFail($validated['night_session_id']);
        if (!$session->isOpen()) {
            return back()->with('error', 'No se pueden registrar facturas en una noche cerrada.');
        }

        $commissionRate = $validated['commission_rate'] ?? $session->pos_commission_rate;

        // Normalizar nombre de barra
        $barName = match(mb_strtolower(trim($validated['bar_name'] ?? 'Principal'))) {
            'subterraneo', 'subte' => 'Subterráneo',
            'tienda' => 'Tienda',
            default => 'Principal',
        };

        $invoice = new Invoice();
        $invoice->night_session_id = $session->id;
        $invoice->correlative_num = $validated['correlative_num'];
        $invoice->payment_method = $validated['payment_method'];
        $invoice->bar_name = $barName;
        $invoice->amount = $validated['amount'];
        $invoice->commission_rate = $commissionRate;
        $invoice->notes = $validated['notes'] ?? null;
        $invoice->calculateNet();
        $invoice->save();

        // Recalcular el cierre de caja
        $this->sessionService->recalculateClosing($session);

        return back()->with('success', 'Factura #' . $invoice->correlative_num . ' registrada.');
    }

    public function destroy(Invoice $invoice)
    {
        $session = $invoice->nightSession;
        if ($session && !$session->isOpen()) {
            return back()->with('error', 'No se pueden eliminar facturas de una noche cerrada.');
        }

        $invoice->delete();

        if ($session) {
            $this->sessionService->recalculateClosing($session);
        }

        return back()->with('success', 'Factura eliminada.');
    }
}
