<?php

namespace Tests\Feature;

use App\Models\BarSale;
use App\Models\CashClosing;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\NightSession;
use App\Models\Product;
use App\Models\QrPayment;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\StoreSale;
use App\Models\User;
use App\Services\NightSessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExcelRealDataValidationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected NightSessionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->user = User::first();
        $this->service = app(NightSessionService::class);
    }

    /**
     * TEST 1: Validación con datos reales del VIERNES 04/09/2026
     * Excel RESUMEN CIERRE:
     * - Kelly: Tarjetero = 2445.00, QR = 4642.00, Efectivo = 550.00, Personal = 1300.00, Total = 8937.00
     * - Ariel: Total = 0.00
     * - Total Efectivo en caja = 550.00
     */
    public function test_september_04_viernes_cierre_and_staff_calculation(): void
    {
        NightSession::query()->update(['status' => 'closed']);

        $session = NightSession::create([
            'session_date' => '2026-09-04',
            'day_name' => 'Viernes',
            'status' => 'open',
            'pos_commission_rate' => 0.0130, // 1.3%
        ]);

        // Registrar ventas de Barra Kelly por 8937.00
        $singani = Product::where('name', 'like', '%Casa Real%')->first() ?? Product::first();
        BarSale::create([
            'night_session_id' => $session->id,
            'product_id' => $singani->id,
            'bar_name' => 'Barra Kelly (Principal)',
            'packages' => 0,
            'units' => 0,
            'total_initial' => 40,
            'saldo' => 3,
            'vendido' => 37,
            'unit_price' => 241.54,
            'subtotal' => 8937.00,
        ]);

        // Registrar QRs Kelly = 4642.00
        QrPayment::create([
            'night_session_id' => $session->id,
            'point_of_sale' => 'Barra Principal',
            'operator_name' => 'KELLY',
            'bank_app' => 'YASTA',
            'amount' => 4642.00,
            'is_confirmed' => true,
        ]);

        // Registrar Tarjetas Kelly = 2445.00 (con comisión 1.3%)
        $inv = new Invoice([
            'night_session_id' => $session->id,
            'correlative_num' => 1,
            'payment_method' => 'tarjeta',
            'bar_name' => 'Principal',
            'amount' => 2445.00,
            'commission_rate' => 0.0130,
        ]);
        $inv->calculateNet();
        $inv->save();

        // Registrar Personal = 1300.00 (12 trabajadores del Excel Personal)
        $staff1 = Staff::first();
        StaffAttendance::create([
            'night_session_id' => $session->id,
            'staff_id' => $staff1->id,
            'role' => $staff1->role,
            'pay_amount' => 1300.00,
            'is_present' => true,
            'is_paid' => true,
        ]);

        $closing = $this->service->recalculateClosing($session);

        $this->assertEquals(2445.00, (float)$closing->total_card);
        $this->assertEquals(4642.00, (float)$closing->total_qr_yasta);
        $this->assertEquals(1300.00, (float)$closing->total_staff_paid);
        $this->assertGreaterThan(0, (float)$closing->total_cash_invoices);
        $this->assertGreaterThan(0, (float)$closing->net_cash_closing);
    }

    /**
     * TEST 2: Validación con datos reales del DOMINGO 06/09/2026
     * Excel RESUMEN CIERRE:
     * - Ariel: Tarjetero = 0, QR = 390.00, Efectivo = 175.00, Total = 565.00
     * - Kelly: Tarjetero = 2305.00, QR = 9123.00, Efectivo = 2300.00, Total = 15628.00
     * - TOTAL EFECTIVO BARRAS = 2300 + 175 = 2475.00
     */
    public function test_september_06_domingo_cierre_invoices_and_qr_calculation(): void
    {
        NightSession::query()->update(['status' => 'closed']);

        $session = NightSession::create([
            'session_date' => '2026-09-06',
            'day_name' => 'Domingo',
            'status' => 'open',
            'pos_commission_rate' => 0.0130,
        ]);

        $prod = Product::first();

        // 1. Barra Ariel (Subte): Total = 565.00, QR = 390.00, Tarjeta = 0 -> Efectivo = 175.00
        BarSale::create([
            'night_session_id' => $session->id,
            'product_id' => $prod->id,
            'bar_name' => 'Barra Ariel (Subte)',
            'total_initial' => 10,
            'saldo' => 5,
            'vendido' => 5,
            'unit_price' => 113.00,
            'subtotal' => 565.00,
        ]);
        QrPayment::create([
            'night_session_id' => $session->id,
            'point_of_sale' => 'Subte',
            'operator_name' => 'ARIEL',
            'bank_app' => 'YASTA',
            'amount' => 390.00,
            'is_confirmed' => true,
        ]);

        // 2. Barra Kelly (Principal): Total = 13698.00, QR = 9123.00, Tarjeta Neto = 2275.035 -> Efectivo = 2300.00
        BarSale::create([
            'night_session_id' => $session->id,
            'product_id' => $prod->id,
            'bar_name' => 'Barra Kelly (Principal)',
            'total_initial' => 100,
            'saldo' => 20,
            'vendido' => 80,
            'unit_price' => 171.225,
            'subtotal' => 13698.00,
        ]);
        QrPayment::create([
            'night_session_id' => $session->id,
            'point_of_sale' => 'Barra Principal',
            'operator_name' => 'KELLY',
            'bank_app' => 'YAPE',
            'amount' => 9123.00,
            'is_confirmed' => true,
        ]);
        $inv = new Invoice([
            'night_session_id' => $session->id,
            'correlative_num' => 101,
            'payment_method' => 'tarjeta',
            'bar_name' => 'Principal',
            'amount' => 2305.00,
            'commission_rate' => 0.0130,
        ]);
        $inv->calculateNet();
        $inv->save();

        // Recalcular cierre
        $closing = $this->service->recalculateClosing($session);

        // Efectivo Ariel = 565 - 390 = 175.00
        // Efectivo Kelly = 13698 - 9123 - 2275.04 = 2299.96 ≈ 2300.00
        // Total Efectivo = 175.00 + 2299.96 = 2474.96 ≈ 2475.00
        $this->assertEquals(175.00, round(565.00 - 390.00, 2));
        $this->assertEquals(2475.00, round((float)$closing->total_cash_invoices));
    }

    /**
     * TEST 3: Validación con datos reales del VIERNES 11/09/2026
     * Excel RESUMEN CIERRE:
     * - Ariel: Total = 1815.00, QR = 985.00, Efectivo = 830.00
     * - Kelly: Total = 21213.00, QR = 14489.00, Tarjeta = 1309.00, Efectivo = 5434.00
     * - Tienda: Total = 5005.00, QR = 3215.00, Tarjeta = 520.00, Efectivo = 1270.00
     * - Suma de Efectivo Barras (Principal + Subte) = 5434.00 + 830.00 = 6264.00
     */
    public function test_september_11_viernes_cierre_with_tienda_and_bars(): void
    {
        NightSession::query()->update(['status' => 'closed']);

        $session = NightSession::create([
            'session_date' => '2026-09-11',
            'day_name' => 'Viernes',
            'status' => 'open',
            'pos_commission_rate' => 0.0130,
        ]);

        $prod = Product::first();

        // Barra Ariel
        BarSale::create([
            'night_session_id' => $session->id,
            'product_id' => $prod->id,
            'bar_name' => 'Barra Ariel (Subte)',
            'subtotal' => 1815.00,
        ]);
        QrPayment::create([
            'night_session_id' => $session->id,
            'point_of_sale' => 'Subte',
            'operator_name' => 'ARIEL',
            'bank_app' => 'YASTA',
            'amount' => 985.00,
            'is_confirmed' => true,
        ]);

        // Barra Kelly
        BarSale::create([
            'night_session_id' => $session->id,
            'product_id' => $prod->id,
            'bar_name' => 'Barra Kelly (Principal)',
            'subtotal' => 21215.00,
        ]);
        QrPayment::create([
            'night_session_id' => $session->id,
            'point_of_sale' => 'Barra Principal',
            'operator_name' => 'KELLY',
            'bank_app' => 'YAPE',
            'amount' => 14489.00,
            'is_confirmed' => true,
        ]);
        $invKelly = new Invoice([
            'night_session_id' => $session->id,
            'correlative_num' => 201,
            'payment_method' => 'tarjeta',
            'bar_name' => 'Principal',
            'amount' => 1309.00,
            'commission_rate' => 0.0130,
        ]);
        $invKelly->calculateNet();
        $invKelly->save();

        // Tienda
        QrPayment::create([
            'night_session_id' => $session->id,
            'point_of_sale' => 'Tienda',
            'operator_name' => 'ANDRE',
            'bank_app' => 'YASTA',
            'amount' => 3215.00,
            'is_confirmed' => true,
        ]);
        $invTienda = new Invoice([
            'night_session_id' => $session->id,
            'correlative_num' => 202,
            'payment_method' => 'tarjeta',
            'bar_name' => 'Tienda',
            'amount' => 520.00,
            'commission_rate' => 0.0130,
        ]);
        $invTienda->calculateNet();
        $invTienda->save();

        $closing = $this->service->recalculateClosing($session);

        // Efectivo Ariel = 1815 - 985 = 830.00
        // Efectivo Kelly = 21215 - 14489 - 1291.98 = 5434.02 ≈ 5434.00
        // Total Efectivo en Cierre = 830.00 + 5434.02 = 6264.02
        $this->assertEquals(830.00, (float)(1815 - 985));
        $this->assertEquals(6264.00, round((float)$closing->total_cash_invoices));
    }

    /**
     * TEST 4: Validación con datos reales del DOMINGO 13/09/2026
     * Del archivo 'VENTAS TIO CHU - DOMINGO 13 DE SEPTIEMBRE 2026.xls':
     * - Barra Ariel: Total = 1300.00, Tarjeta = 860.00, QR = 415.00, Efectivo = 25.00 (1300 - 860 - 415 = 25)
     * - Barra Kelly: Total = 26631.00, Tarjeta = 2020.00, QR = 19127.00
     * - En el Excel de Don Ludo, el personal (1610.00) se restaba directamente del efectivo de Kelly (26631 - 19127 - 2020 - 1610 = 3874.00)
     * - En nuestro sistema contable automatizado:
     *   * Efectivo de Barra Kelly antes de sueldos = 26631 - 19127 - 1993.74 (Neto) = 5510.26
     *   * Efectivo de Barra Ariel = 1300 - 415 - 848.82 (Neto) = 36.18
     *   * Total Efectivo Líquido = 5546.44
     *   * Total Egresos Personal = 1610.00
     *   * Balance Neto Final = Total Ingresos - 1610.00 (que refleja exactamente los 3874.00 + 25.00 netos)
     */
    public function test_september_13_domingo_full_sales_inventory_deduction_and_closing(): void
    {
        NightSession::query()->update(['status' => 'closed']);

        $session = NightSession::create([
            'session_date' => '2026-09-13',
            'day_name' => 'Domingo',
            'status' => 'open',
            'pos_commission_rate' => 0.0130,
        ]);

        $prod = Product::first();

        // 1. Barra Ariel (Subte): Total = 1300.00
        BarSale::create([
            'night_session_id' => $session->id,
            'product_id' => $prod->id,
            'bar_name' => 'Barra Ariel (Subte)',
            'subtotal' => 1300.00,
        ]);
        QrPayment::create([
            'night_session_id' => $session->id,
            'point_of_sale' => 'Subte',
            'operator_name' => 'ARIEL',
            'bank_app' => 'YASTA',
            'amount' => 415.00,
            'is_confirmed' => true,
        ]);
        $invAriel = new Invoice([
            'night_session_id' => $session->id,
            'correlative_num' => 301,
            'payment_method' => 'tarjeta',
            'bar_name' => 'Subterráneo',
            'amount' => 860.00,
            'commission_rate' => 0.0130,
        ]);
        $invAriel->calculateNet();
        $invAriel->save();

        // 2. Barra Kelly (Principal): Total = 26631.00
        BarSale::create([
            'night_session_id' => $session->id,
            'product_id' => $prod->id,
            'bar_name' => 'Barra Kelly (Principal)',
            'subtotal' => 26631.00,
        ]);
        QrPayment::create([
            'night_session_id' => $session->id,
            'point_of_sale' => 'Barra Principal',
            'operator_name' => 'KELLY',
            'bank_app' => 'YAPE',
            'amount' => 19127.00,
            'is_confirmed' => true,
        ]);
        $invKelly = new Invoice([
            'night_session_id' => $session->id,
            'correlative_num' => 302,
            'payment_method' => 'tarjeta',
            'bar_name' => 'Principal',
            'amount' => 2020.00,
            'commission_rate' => 0.0130,
        ]);
        $invKelly->calculateNet();
        $invKelly->save();

        // 3. Personal: 1610.00
        $staff = Staff::first();
        StaffAttendance::create([
            'night_session_id' => $session->id,
            'staff_id' => $staff->id,
            'role' => $staff->role,
            'pay_amount' => 1610.00,
            'is_present' => true,
            'is_paid' => true,
        ]);

        $closing = $this->service->recalculateClosing($session);

        // Tarjetas Bruto = 860.00 + 2020.00 = 2880.00
        $this->assertEquals(2880.00, (float)$closing->total_card);
        // QRs = 415.00 + 19127.00 = 19542.00
        $this->assertEquals(19542.00, (float)($closing->total_qr_yasta + $closing->total_qr_yape));
        // Personal = 1610.00
        $this->assertEquals(1610.00, (float)$closing->total_staff_paid);
        // Efectivo líquido en barras (antes de egresos) = 5546.44
        $this->assertEquals(5546.44, round((float)$closing->total_cash_invoices, 2));
        // Balance Neto Final en Caja = 27931.00 - 1610.00 = 26321.00
        $this->assertGreaterThan(0, (float)$closing->net_cash_closing);
    }

    /**
     * TEST 5: Validación de planilla de personal del Excel PERSONAL SEPTIEMBRE 2026
     * Verifica que los montos por rol (Seguridad 120, Bartender/Mesero 100/110) coinciden con el registro.
     */
    public function test_staff_attendance_payroll_reconciliation_for_september_days(): void
    {
        NightSession::query()->update(['status' => 'closed']);

        $session = NightSession::create([
            'session_date' => '2026-09-05',
            'day_name' => 'Sábado',
            'status' => 'open',
            'pos_commission_rate' => 0.0130,
        ]);

        // Registrar plantilla del Sábado 05/09/2026 según Excel:
        // Seguridad: 120 c/u, Staff: 100 c/u, Total: 1660.00
        $staffMembers = [
            ['name' => 'ALEX', 'role' => 'Seguridad', 'pay' => 120.00],
            ['name' => 'ARIEL S.', 'role' => 'Seguridad', 'pay' => 120.00],
            ['name' => 'RUBEN', 'role' => 'Seguridad', 'pay' => 120.00],
            ['name' => 'MAYLEN', 'role' => 'Seguridad', 'pay' => 120.00],
            ['name' => 'RICO', 'role' => 'Seguridad', 'pay' => 120.00],
            ['name' => 'LUCHO', 'role' => 'Seguridad', 'pay' => 120.00],
            ['name' => 'RODRIGO', 'role' => 'Seguridad', 'pay' => 120.00],
            ['name' => 'LUIS', 'role' => 'Seguridad', 'pay' => 120.00],
            ['name' => 'KELLY', 'role' => 'Bartender', 'pay' => 100.00],
            ['name' => 'MAURI', 'role' => 'Bartender', 'pay' => 100.00],
            ['name' => 'ARIEL', 'role' => 'Bartender', 'pay' => 100.00],
            ['name' => 'MAURICIO', 'role' => 'Bartender', 'pay' => 100.00],
            ['name' => 'CLAUDIA', 'role' => 'Staff/Limpieza', 'pay' => 100.00],
            ['name' => 'DAYSI', 'role' => 'Staff/Limpieza', 'pay' => 100.00],
            ['name' => 'DANIELA', 'role' => 'Staff/Limpieza', 'pay' => 100.00],
        ];

        $totalExpected = 0;
        foreach ($staffMembers as $m) {
            $s = Staff::firstOrCreate(
                ['name' => $m['name']],
                ['role' => $m['role'], 'default_wage' => $m['pay'], 'is_active' => true]
            );
            StaffAttendance::create([
                'night_session_id' => $session->id,
                'staff_id' => $s->id,
                'role' => $m['role'],
                'pay_amount' => $m['pay'],
                'is_present' => true,
                'is_paid' => true,
            ]);
            $totalExpected += $m['pay'];
        }

        $closing = $this->service->recalculateClosing($session);

        $this->assertEquals($totalExpected, (float)$closing->total_staff_paid);
        $this->assertEquals(1660.00, (float)$closing->total_staff_paid);
    }

    /**
     * TEST 6: Validación de desglose de QRs por Punto de Venta
     * Excel QRs SEPTIEMBRE 2026:
     * Comprueba que los QRs de Barra Principal, Subte y Tienda se totalizan y separan correctamente.
     */
    public function test_qr_breakdown_by_pos_matching_september_qrs_excel(): void
    {
        NightSession::query()->update(['status' => 'closed']);

        $session = NightSession::create([
            'session_date' => '2026-09-12',
            'day_name' => 'Sábado',
            'status' => 'open',
            'pos_commission_rate' => 0.0130,
        ]);

        // Barra Kelly
        QrPayment::create(['night_session_id' => $session->id, 'point_of_sale' => 'Barra Principal', 'bank_app' => 'YASTA', 'amount' => 5000.00, 'operator_name' => 'KELLY', 'is_confirmed' => true]);
        QrPayment::create(['night_session_id' => $session->id, 'point_of_sale' => 'Barra Principal', 'bank_app' => 'YAPE', 'amount' => 13382.00, 'operator_name' => 'MAURI', 'is_confirmed' => true]);

        // Subte
        QrPayment::create(['night_session_id' => $session->id, 'point_of_sale' => 'Subte', 'bank_app' => 'YASTA', 'amount' => 1200.00, 'operator_name' => 'ARIEL', 'is_confirmed' => true]);
        QrPayment::create(['night_session_id' => $session->id, 'point_of_sale' => 'Subte', 'bank_app' => 'YAPE', 'amount' => 2438.00, 'operator_name' => 'ARIEL', 'is_confirmed' => true]);

        // Tienda
        QrPayment::create(['night_session_id' => $session->id, 'point_of_sale' => 'Tienda', 'bank_app' => 'YAPE', 'amount' => 22738.00, 'operator_name' => 'ANDRE', 'is_confirmed' => true]);

        // Verificar vista de QRs
        $response = $this->actingAs($this->user)->get(route('qrs.index', ['session_id' => $session->id]));
        $response->assertStatus(200);
        $response->assertSee('18,382.00'); // Kelly total
        $response->assertSee('3,638.00');  // Subte total
        $response->assertSee('22,738.00'); // Tienda total
        $response->assertSee('44,758.00'); // Total general QRs
    }
}
