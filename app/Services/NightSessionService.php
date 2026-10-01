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

        // Facturas (tarjetas — de cualquier barra)
        $cardInvoices = $session->invoices()->where('payment_method', 'tarjeta')->get();
        $cashInvoices = $session->invoices()->where('payment_method', 'efectivo')->get();

        $totalCard           = $cardInvoices->sum('amount');
        $totalCardCommission = $cardInvoices->sum('commission_amount');
        $totalCardNet        = $cardInvoices->sum('net_amount');
        $totalCashInvoices   = $cashInvoices->sum('amount');

        // Pagos QR globales
        $totalQrYasta = (float) $session->qrPayments()->where('bank_app', 'YASTA')->sum('amount');
        $totalQrYape  = (float) $session->qrPayments()->where('bank_app', 'YAPE')->sum('amount');

        // 1. Efectivo en Barra Kelly (Principal): Ventas Kelly - QR Kelly - Tarjeta Neto Kelly
        $salesKelly = (float) $session->barSales()->whereIn('bar_name', ['Barra Kelly (Principal)', 'Principal', 'Kelly'])->sum('subtotal');
        $qrKelly = (float) $session->qrPayments()->whereIn('point_of_sale', ['Barra Principal', 'Principal', 'Barra Kelly (Principal)', 'Kelly'])->sum('amount');
        $cardNetKelly = (float) $session->invoices()->whereIn('bar_name', ['Principal', 'Barra Kelly (Principal)', 'Kelly'])->where('payment_method', 'tarjeta')->sum('net_amount');
        $cashKelly = max(0, $salesKelly - $qrKelly - $cardNetKelly);

        // 2. Efectivo en Barra Ariel (Subte): Ventas Subte - QR Subte - Tarjeta Neto Subte
        $salesSubte = (float) $session->barSales()->whereIn('bar_name', ['Barra Ariel (Subte)', 'Subterráneo', 'Subterraneo', 'Subte', 'Ariel'])->sum('subtotal');
        $qrSubte = (float) $session->qrPayments()->whereIn('point_of_sale', ['Subte', 'Barra Subte', 'Subterráneo', 'Subterraneo', 'Barra Ariel (Subte)', 'Ariel'])->sum('amount');
        $cardNetSubte = (float) $session->invoices()->whereIn('bar_name', ['Subterráneo', 'Subterraneo', 'Subte', 'Barra Ariel (Subte)', 'Ariel'])->where('payment_method', 'tarjeta')->sum('net_amount');
        $cashSubte = max(0, $salesSubte - $qrSubte - $cardNetSubte);

        // Efectivo total en Cierre de Caja = Suma de efectivos de las barras
        $totalEfectivo = $cashKelly + $cashSubte;

        // Ventas totales de referencia (barras + tienda)
        $totalBarSales = (float) $session->barSales()->where('bar_name', '!=', 'Tienda')->sum('subtotal')
            + (float) $session->storeSales()->sum('total_price');

        // Personal
        $totalStaffPaid = (float) $session->staffAttendances()->where('is_paid', true)->sum('pay_amount');

        // Gastos
        $totalExpenses = (float) $session->expenses()->sum('amount');

        // Ingresos extras de Tienda (Guardarropa y Snacks) — se mantienen guardados pero no se suman al cuadre
        $totalGuardarropa = (float)($closing->total_guardarropa ?? 0);
        $totalSnacks      = (float)($closing->total_snacks ?? 0);

        // Total Ingresos = Tarjeta Neto + QR YASTA + QR YAPE + Efectivo Barras
        $totalIngresos = $totalCardNet + $totalQrYasta + $totalQrYape + $totalEfectivo;

        // Total Egresos (Personal + Gastos)
        $totalEgresos = $totalStaffPaid + $totalExpenses;

        // Saldo Neto en Caja
        $netCashClosing = $totalIngresos - $totalEgresos;

        $closing->update([
            'total_card'            => $totalCard,
            'total_card_commission' => $totalCardCommission,
            'total_card_net'        => $totalCardNet,
            'total_cash_invoices'   => $totalEfectivo,  // reutilizamos este campo para el efectivo calculado
            'total_qr_yasta'        => $totalQrYasta,
            'total_qr_yape'         => $totalQrYape,
            'total_bar_sales'       => $totalBarSales,
            'total_guardarropa'     => $totalGuardarropa,
            'total_snacks'          => $totalSnacks,
            'total_staff_paid'      => $totalStaffPaid,
            'total_expenses'        => $totalExpenses,
            'net_cash_closing'      => $netCashClosing,
        ]);

        return $closing;
    }
}
