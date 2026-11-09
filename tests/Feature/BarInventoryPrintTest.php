<?php

namespace Tests\Feature;

use App\Models\NightSession;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarInventoryPrintTest extends TestCase
{
    use RefreshDatabase;

    public function test_bar_inventory_screen_renders_print_planillia_correctly(): void
    {
        $user = User::factory()->create();
        $session = NightSession::create([
            'session_date' => '2026-10-30',
            'day_name' => 'Viernes',
            'status' => 'open',
            'total_sales' => 0,
            'total_expenses' => 0,
            'net_profit' => 0,
        ]);

        $product = Product::create([
            'name' => 'Singani Casa Real Negro',
            'category' => 'Licores',
            'sale_price' => 240.00,
            'unit' => 'Botella',
            'units_per_package' => 6,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('barInventory.index', [
            'session_id' => $session->id,
            'bar' => 'Barra Principal',
        ]));

        $response->assertStatus(200);
        $response->assertSee('INVENTARIO BEBIDAS');
        $response->assertSee('Imprimir Planilla');
        $response->assertSee('SINGANI CASA REAL NEGRO');
    }
}
