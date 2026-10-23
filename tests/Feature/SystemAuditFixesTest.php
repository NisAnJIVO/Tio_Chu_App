<?php

namespace Tests\Feature;

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

class SystemAuditFixesTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->user = User::where('email', 'DonLudo@gmail.com')->first();
    }

    public function test_store_sale_records_card_payment_and_creates_linked_invoice(): void
    {
        $session = NightSession::where('status', 'open')->first() ?? NightSession::first();
        $product = Product::first();
        $product->update(['stock_warehouse' => 50]);

        $response = $this->actingAs($this->user)->post(route('sales.storeSales.store'), [
            'night_session_id' => $session->id,
            'orders' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_price' => 150.00,
                ],
            ],
            'cash_amount' => 0,
            'qr_amount' => 0,
            'card_amount' => 300.00,
            'cobrante_name' => 'Cajero Test',
        ]);

        $response->assertRedirect();

        $sale = StoreSale::where('cobrante_name', 'Cajero Test')->first();
        $this->assertNotNull($sale);
        $this->assertEquals(300.00, $sale->total_price);
        $this->assertEquals(300.00, $sale->card_amount);
        $this->assertEquals(0.00, $sale->cash_amount);
        $this->assertEquals(0.00, $sale->qr_amount);
        $this->assertNotNull($sale->invoice_id);

        $invoice = Invoice::find($sale->invoice_id);
        $this->assertNotNull($invoice);
        $this->assertEquals(300.00, $invoice->amount);
        $this->assertEquals('tarjeta', $invoice->payment_method);
    }

    public function test_store_sale_destruction_cleans_up_linked_invoice_and_qr(): void
    {
        $session = NightSession::where('status', 'open')->first() ?? NightSession::first();
        $product = Product::first();
        $product->update(['stock_warehouse' => 50]);
        $initialStock = $product->fresh()->stock_warehouse;

        // Venta por QR
        $this->actingAs($this->user)->post(route('sales.storeSales.store'), [
            'night_session_id' => $session->id,
            'orders' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 3,
                    'unit_price' => 100.00,
                ],
            ],
            'cash_amount' => 0,
            'qr_amount' => 300.00,
            'card_amount' => 0,
            'cobrante_name' => 'Cajero QR Revert',
            'bank_app' => 'YASTA',
        ]);

        $sale = StoreSale::where('cobrante_name', 'Cajero QR Revert')->first();
        $this->assertNotNull($sale);
        $qrId = $sale->qr_payment_id;
        $this->assertNotNull($qrId);
        $this->assertNotNull(QrPayment::find($qrId));

        // El stock debió bajar en 3
        $this->assertEquals($initialStock - 3, $product->fresh()->stock_warehouse);

        // Eliminamos la venta
        $deleteResponse = $this->actingAs($this->user)->delete(route('sales.storeSales.destroy', $sale));
        $deleteResponse->assertRedirect();

        // 1. La venta fue eliminada
        $this->assertNull(StoreSale::find($sale->id));

        // 2. El pago QR vinculado fue eliminado limpiamente
        $this->assertNull(QrPayment::find($qrId));

        // 3. El stock de almacén fue repuesto
        $this->assertEquals($initialStock, $product->fresh()->stock_warehouse);
    }

    public function test_cannot_pay_staff_attendance_for_closed_night_session(): void
    {
        // Creamos o buscamos una sesión cerrada
        $session = NightSession::first();
        $session->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        $attendance = StaffAttendance::where('night_session_id', $session->id)->first();
        if (!$attendance) {
            $staff = Staff::first();
            $attendance = StaffAttendance::create([
                'night_session_id' => $session->id,
                'staff_id' => $staff->id,
                'attended' => true,
                'pay_amount' => 100.00,
                'is_paid' => false,
            ]);
        } else {
            $attendance->update(['is_paid' => false, 'pay_amount' => 100.00]);
        }

        // Intento de pago vía JSON
        $response = $this->actingAs($this->user)->postJson(route('paymentHistory.paySingle', $attendance), [
            'amount' => 100.00,
            'notes' => 'Intento de pago en noche cerrada',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);

        // Verificamos que la asistencia NO fue alterada
        $this->assertFalse((bool) $attendance->fresh()->is_paid);
    }

    public function test_night_session_service_metrics_computes_accurately(): void
    {
        $session = NightSession::where('status', 'open')->first() ?? NightSession::first();
        $service = app(NightSessionService::class);
        $metrics = $service->getDashboardMetrics($session, null);

        $this->assertIsArray($metrics);
        $this->assertArrayHasKey('totalSales', $metrics);
        $this->assertArrayHasKey('totalExpenses', $metrics);
        $this->assertArrayHasKey('staffPaid', $metrics);
        $this->assertArrayHasKey('barPayments', $metrics);
        $this->assertArrayHasKey('qrTotal', $metrics);
        $this->assertArrayHasKey('totalQrYasta', $metrics);
        $this->assertArrayHasKey('totalQrYape', $metrics);
        $this->assertArrayHasKey('posGross', $metrics);
        $this->assertArrayHasKey('posNet', $metrics);
        $this->assertArrayHasKey('posCommission', $metrics);
        $this->assertArrayHasKey('barTotal', $metrics);
        $this->assertArrayHasKey('netCash', $metrics);
        $this->assertArrayHasKey('totalIncome', $metrics);
    }
}
