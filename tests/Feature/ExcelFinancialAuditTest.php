<?php

namespace Tests\Feature;

use App\Models\BeerPayment;
use App\Models\CashClosing;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\NightSession;
use App\Models\Product;
use App\Models\QrPayment;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\User;
use App\Services\NightSessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ExcelFinancialAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected NightSessionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->user = User::where('email', 'DonLudo@gmail.com')->first();
        $this->service = app(NightSessionService::class);
    }

    /**
     * Test de auditoría financiera basado en la noche real del Viernes 23/10/2026:
     * - Ventas de barras: 5,380.00 Bs (Kelly 5,290 + Ariel 90)
     * - Tarjetas (POS): 783.00 Bs (Bruto, sin descuento diario en el cierre)
     * - QR recaudado: 4,830.00 Bs (Yape 2,780 + Yasta 2,050)
     * - Cerveza pagada vía QR: 1,085.00 Bs
     * - QR restante disponible en banco: 3,745.00 Bs (4,830 - 1,085 = 3,745 Bs)
     * - Personal liquidado esa noche: 900.00 Bs en efectivo (de 1,300 Bs de planilla total)
     * - Efectivo restante: NUNCA negativo (debe ser >= 0, en este caso 0.00 Bs)
     * - Total Rendido y Cuadre: 783 + 3,745 + 900 = 5,428.00 Bs -> Sobrante de +48.00 Bs
     */
    public function test_friday_night_financial_closing_aligns_with_client_excel(): void
    {
        $session = NightSession::create([
            'session_date' => '2026-10-23',
            'day_name' => 'Viernes',
            'status' => 'closed',
            'created_by' => $this->user->id,
        ]);

        // 1. Ventas registradas en barras (Barra Kelly y Barra Ariel)
        DB::table('bar_sales')->insert([
            [
                'night_session_id' => $session->id,
                'bar_name' => 'Barra Kelly (Principal)',
                'product_id' => 1,
                'vendido' => 1,
                'unit_price' => 5290.00,
                'subtotal' => 5290.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'night_session_id' => $session->id,
                'bar_name' => 'Barra Ariel (Subte)',
                'product_id' => 1,
                'vendido' => 1,
                'unit_price' => 90.00,
                'subtotal' => 90.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 2. Facturación con Tarjetas (Bruto = 783.00 Bs, Neto con fee = 772.82 Bs)
        Invoice::create([
            'night_session_id' => $session->id,
            'correlative_num' => 1,
            'bar_name' => 'Barra Kelly (Principal)',
            'amount' => 783.00,
            'commission_rate' => 0.0130,
            'commission_amount' => 10.18,
            'net_amount' => 772.82,
            'payment_method' => 'tarjeta',
        ]);

        // 3. Pagos QR (4,830.00 Bs en total: YAPE 2,780 + YASTA 2,050)
        QrPayment::create([
            'night_session_id' => $session->id,
            'bank_app' => 'YAPE',
            'point_of_sale' => 'Barra Kelly (Principal)',
            'operator_name' => 'Kelly',
            'amount' => 2780.00,
            'transaction_code' => 'YAPE-001',
        ]);
        QrPayment::create([
            'night_session_id' => $session->id,
            'bank_app' => 'YASTA',
            'point_of_sale' => 'Barra Kelly (Principal)',
            'operator_name' => 'Kelly',
            'amount' => 2050.00,
            'transaction_code' => 'YASTA-001',
        ]);

        // 4. Pago de Cerveza pagado directamente vía QR YAPE (1,085.00 Bs)
        BeerPayment::create([
            'night_session_id' => $session->id,
            'amount' => 1085.00,
            'payment_method' => 'qr',
            'qr_provider' => 'YAPE',
            'description' => 'Pago proveedor cerveza Huari',
        ]);

        // 5. Personal: 1,300.00 Bs devengado, pero solo 900.00 Bs pagado en efectivo esa noche
        $staff1 = Staff::create(['name' => 'Trabajador 1', 'role' => 'Bartender', 'base_salary' => 900]);
        $staff2 = Staff::create(['name' => 'Trabajador 2', 'role' => 'Seguridad', 'base_salary' => 400]);

        StaffAttendance::create([
            'night_session_id' => $session->id,
            'staff_id' => $staff1->id,
            'pay_amount' => 900.00,
            'is_paid' => true,
        ]);
        StaffAttendance::create([
            'night_session_id' => $session->id,
            'staff_id' => $staff2->id,
            'pay_amount' => 400.00,
            'is_paid' => false,
        ]);

        // Ejecutamos recálculo de cierre
        $closing = $this->service->recalculateClosing($session);

        // --- VERIFICACIÓN DE REGLAS CRÍTICAS ---

        // A. Tarjetas en cierre debe ser el MONTO BRUTO (783.00 Bs), sin descontar comisión diaria
        $this->assertEquals(783.00, (float) $closing->total_card, 'El monto de tarjetas debe ser el bruto nominal');

        // B. Total QR recaudado debe ser 4,830.00 Bs
        $this->assertEquals(4830.00, (float) $closing->total_qr, 'El QR total recaudado debe ser 4830.00 Bs');

        // C. Efectivo en cierre NUNCA debe ser negativo (queja del cliente: -448 Bs)
        $this->assertGreaterThanOrEqual(0, (float) $closing->total_cash_remaining, 'El efectivo restante nunca puede ser negativo');
        $this->assertEquals(0.00, (float) $closing->total_cash_remaining, 'El efectivo restante físico debe ser 0.00 Bs, no -448');

        // D. Verificación de métricas del Dashboard
        $metrics = $this->service->getDashboardMetrics($session, $closing);

        $this->assertEquals(5380.00, $metrics['totalSales'], 'Ventas totales deben ser 5380.00 Bs');
        $this->assertEquals(783.00, $metrics['posGross'], 'Tarjetas brutas deben ser 783.00 Bs');
        $this->assertEquals(4830.00, $metrics['qrTotal'], 'QR recaudado bruto debe ser 4830.00 Bs');
        $this->assertEquals(1085.00, $metrics['beerPaymentQrTotal'], 'Cerveza pagada por QR debe ser 1085.00 Bs');
        $this->assertEquals(3745.00, $metrics['qrRemaining'], 'QR restante en banco debe ser 3745.00 Bs (Kelly QR en Excel)');
        $this->assertEquals(900.00, $metrics['staffAlreadyPaid'], 'Personal pagado en efectivo debe ser 900.00 Bs');
        $this->assertEquals(0.00, $metrics['cashRemaining'], 'Efectivo restante debe ser 0.00 Bs');

        // Total Rendido = 783 (POS) + 3745 (QR Kelly) + 900 (Personal) + 0 (Efectivo) = 5,428.00 Bs
        $this->assertEquals(5428.00, $metrics['cashComparisonTotal'], 'Total rendido debe ser 5428.00 Bs');

        // Diferencia: 5428 rendido - 5380 ventas = +48.00 Bs (Sobrante)
        $this->assertEquals(48.00, $metrics['cashDifference'], 'Diferencia debe ser un sobrante de +48.00 Bs');
    }

    /**
     * Test para verificar que el resumen consolidado calcula la comisión bancaria del 1.3%
     * al finalizar el período (fin de semana completo de 3 días) y no noche por noche.
     */
    public function test_consolidated_summary_calculates_weekend_pos_commission_at_end_of_period(): void
    {
        // 3 jornadas de fin de semana (Viernes, Sábado, Domingo)
        $friday = NightSession::create(['session_date' => '2026-10-23', 'day_name' => 'Viernes', 'status' => 'closed', 'created_by' => $this->user->id]);
        $saturday = NightSession::create(['session_date' => '2026-10-24', 'day_name' => 'Sábado', 'status' => 'closed', 'created_by' => $this->user->id]);
        $sunday = NightSession::create(['session_date' => '2026-10-25', 'day_name' => 'Domingo', 'status' => 'closed', 'created_by' => $this->user->id]);

        // Facturas de tarjetas para cada día
        Invoice::create(['night_session_id' => $friday->id, 'correlative_num' => 1, 'bar_name' => 'Barra Principal', 'amount' => 783.00, 'commission_rate' => 0.0130, 'commission_amount' => 10.18, 'net_amount' => 772.82, 'payment_method' => 'tarjeta']);
        Invoice::create(['night_session_id' => $saturday->id, 'correlative_num' => 2, 'bar_name' => 'Barra Principal', 'amount' => 3065.00, 'commission_rate' => 0.0130, 'commission_amount' => 39.85, 'net_amount' => 3025.15, 'payment_method' => 'tarjeta']);
        Invoice::create(['night_session_id' => $sunday->id, 'correlative_num' => 3, 'bar_name' => 'Barra Principal', 'amount' => 2000.00, 'commission_rate' => 0.0130, 'commission_amount' => 26.00, 'net_amount' => 1974.00, 'payment_method' => 'tarjeta']);

        // Suma fin de semana = 783 + 3065 + 2000 = 5,848.00 Bs
        $sessionIds = [$friday->id, $saturday->id, $sunday->id];
        $consolidated = $this->service->getConsolidatedSummary($sessionIds);

        $this->assertEquals(5848.00, $consolidated['totals']['card_gross'], 'Tarjetas brutas del fin de semana deben ser 5,848.00 Bs');
        
        // Comisión calculada sobre el total a 1.3% (0.013) como en FACT. EMITIDAS .xls: 5848 * 0.013 = 76.02 Bs
        $expectedCommission = round(5848.00 * 0.013, 2);
        $this->assertEquals(76.02, $expectedCommission);
        $this->assertEquals(76.02, $consolidated['totals']['card_commission'], 'Comisión fin de semana debe ser 76.02 Bs');
        $this->assertEquals(5771.98, $consolidated['totals']['card_net'], 'Tarjetas netas acreditadas deben ser 5,771.98 Bs');
    }

    /**
     * Test de la vista de Cierre de Caja y Dashboard en modo Estilo Claro y con datos consistentes.
     */
    public function test_closing_and_dashboard_render_without_errors(): void
    {
        $session = NightSession::create([
            'session_date' => '2026-10-23',
            'day_name' => 'Viernes',
            'status' => 'closed',
            'created_by' => $this->user->id,
        ]);

        $this->service->recalculateClosing($session);

        $responseDashboard = $this->actingAs($this->user)->get(route('dashboard'));
        $responseDashboard->assertStatus(200);
        $responseDashboard->assertSee('Venta en Barras');
        $responseDashboard->assertSee('Cuadre Final de Caja');
        // No debe tener el texto de efectivo negativo
        $responseDashboard->assertDontSee('-448');

        $responseClosing = $this->actingAs($this->user)->get(route('closing.index'));
        $responseClosing->assertStatus(200);
        $responseClosing->assertSee('Cierre de Caja');
        $responseClosing->assertDontSee('-448');
    }
}
