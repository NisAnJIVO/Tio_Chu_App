<?php

namespace App\Services;

use App\Models\CashClosing;
use App\Models\NightSession;

class NightSessionService
{
    /**
     * Resuelve la sesión activa o seleccionada, recordando la selección en la sesión HTTP
     */
    public function resolveSession(?int $sessionId = null): ?NightSession
    {
        if ($sessionId) {
            $session = NightSession::find($sessionId);
            if ($session) {
                session(['active_night_session_id' => $session->id]);
                return $session;
            }
        }

        $sessionFromStorage = session('active_night_session_id');
        if ($sessionFromStorage) {
            $session = NightSession::find($sessionFromStorage);
            if ($session) {
                return $session;
            }
        }

        $session = NightSession::orderByDesc('session_date')->first();
        if ($session) {
            session(['active_night_session_id' => $session->id]);
        }

        return $session;
    }

    /**
     * Recalcula y sincroniza el cuadre de caja de una noche
     */
    public function recalculateClosing(NightSession $session): CashClosing
    {
        $closing = CashClosing::firstOrCreate(['night_session_id' => $session->id]);

        // Facturas
        $cardInvoices = $session->invoices()->where('payment_method', 'tarjeta')->get();
        $cashInvoices = $session->invoices()->where('payment_method', 'efectivo')->get();

        $totalCard = $cardInvoices->sum('amount');
        $totalCardCommission = $cardInvoices->sum('commission_amount');
        $totalCardNet = $cardInvoices->sum('net_amount');
        $totalCashInvoices = $cashInvoices->sum('amount');

        // Pagos QR
        $totalQrYasta = $session->qrPayments()->where('bank_app', 'YASTA')->sum('amount');
        $totalQrYape = $session->qrPayments()->where('bank_app', 'YAPE')->sum('amount');

        // Ventas por Barra y Tienda
        $totalBarSales = $session->barSales()->where('bar_name', '!=', 'Tienda')->sum('subtotal')
            + $session->storeSales()->sum('total_price');

        // Personal
        $totalStaffPaid = $session->staffAttendances()->where('is_paid', true)->sum('pay_amount');

        // Gastos
        $totalExpenses = $session->expenses()->sum('amount');

        // Ingresos extras de Tienda (Guardarropa y Snacks)
        $totalGuardarropa = (float)($closing->total_guardarropa ?? 0);
        $totalSnacks = (float)($closing->total_snacks ?? 0);

        // Total Ingresos Registrados (Tarjeta Neto + QRs + Efectivo + Guardarropa + Snacks)
        $totalIngresos = $totalCardNet + $totalQrYasta + $totalQrYape + $totalCashInvoices + $totalGuardarropa + $totalSnacks;

        // Total Egresos (Personal + Gastos)
        $totalEgresos = $totalStaffPaid + $totalExpenses;

        // Saldo Neto en Caja
        $netCashClosing = $totalIngresos - $totalEgresos;

        $closing->update([
            'total_card' => $totalCard,
            'total_card_commission' => $totalCardCommission,
            'total_card_net' => $totalCardNet,
            'total_cash_invoices' => $totalCashInvoices,
            'total_qr_yasta' => $totalQrYasta,
            'total_qr_yape' => $totalQrYape,
            'total_bar_sales' => $totalBarSales,
            'total_guardarropa' => $totalGuardarropa,
            'total_snacks' => $totalSnacks,
            'total_staff_paid' => $totalStaffPaid,
            'total_expenses' => $totalExpenses,
            'net_cash_closing' => $netCashClosing,
        ]);

        return $closing;
    }
}
