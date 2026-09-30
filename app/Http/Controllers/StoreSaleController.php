<?php

namespace App\Http\Controllers;

use App\Models\CashClosing;
use App\Models\NightSession;
use App\Models\Product;
use App\Models\QrPayment;
use App\Models\StoreSale;
use App\Services\NightSessionService;
use Illuminate\Http\Request;

class StoreSaleController extends Controller
{
    protected NightSessionService $sessionService;

    public function __construct(NightSessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'night_session_id' => 'required|exists:night_sessions,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'cash_amount' => 'required|numeric|min:0',
            'qr_amount' => 'required|numeric|min:0',
            'cobrante_name' => 'required|string|max:100',
            'bank_app' => 'nullable|in:YASTA,YAPE',
            'sync_qr' => 'nullable',
        ]);

        $quantity = (int)$validated['quantity'];
        $unitPrice = (float)$validated['unit_price'];
        $totalPrice = $quantity * $unitPrice;
        $cashAmount = (float)$validated['cash_amount'];
        $qrAmount = (float)$validated['qr_amount'];
        $cobrante = trim($validated['cobrante_name']);
        $session = NightSession::findOrFail($validated['night_session_id']);
        if (!$session->isOpen()) {
            return back()->with('error', 'No se pueden registrar despachos en una noche cerrada.');
        }

        $storeSale = StoreSale::create([
            'night_session_id' => $validated['night_session_id'],
            'product_id' => $validated['product_id'],
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'cash_amount' => $cashAmount,
            'qr_amount' => $qrAmount,
            'cobrante_name' => $cobrante,
        ]);

        // Si se pagó una parte o todo en QR y está marcado sincronizar, se anota automáticamente en QRs Tienda
        if ($qrAmount > 0 && $request->has('sync_qr')) {
            QrPayment::create([
                'night_session_id' => $validated['night_session_id'],
                'point_of_sale' => 'Tienda',
                'operator_name' => $cobrante,
                'bank_app' => $request->input('bank_app', 'YASTA'),
                'amount' => $qrAmount,
                'reference_code' => null,
                'is_confirmed' => true,
            ]);
        }

        $this->sessionService->recalculateClosing($session);

        return back()->with('success', "Pedido de Tienda registrado ({$quantity} combo(s) por {$cobrante}).");
    }

    public function destroy(StoreSale $storeSale)
    {
        $session = $storeSale->nightSession;
        if ($session && !$session->isOpen()) {
            return back()->with('error', 'No se pueden eliminar despachos de una noche cerrada.');
        }

        $storeSale->delete();

        if ($session) {
            $this->sessionService->recalculateClosing($session);
        }

        return back()->with('success', 'Pedido de Tienda eliminado.');
    }

    public function updateExtras(Request $request)
    {
        $validated = $request->validate([
            'night_session_id' => 'required|exists:night_sessions,id',
            'guardarropa_amount' => 'nullable|numeric|min:0',
            'snacks_amount' => 'nullable|numeric|min:0',
        ]);

        $session = NightSession::findOrFail($validated['night_session_id']);
        if (!$session->isOpen()) {
            return back()->with('error', 'No se pueden modificar ingresos en una noche cerrada.');
        }

        $closing = CashClosing::firstOrCreate(['night_session_id' => $session->id]);

        $closing->update([
            'total_guardarropa' => (float)($validated['guardarropa_amount'] ?? 0),
            'total_snacks' => (float)($validated['snacks_amount'] ?? 0),
        ]);

        $this->sessionService->recalculateClosing($session);

        return back()->with('success', 'Ingresos de Guardarropa y Snacks actualizados correctamente.');
    }
}
