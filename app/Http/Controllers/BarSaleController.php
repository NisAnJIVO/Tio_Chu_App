<?php

namespace App\Http\Controllers;

use App\Models\BarSale;
use App\Models\CashClosing;
use App\Models\NightSession;
use App\Models\Product;
use App\Models\StoreSale;
use App\Services\NightSessionService;
use Illuminate\Http\Request;

class BarSaleController extends Controller
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

        $selectedBar = $request->get('bar', 'Barra Kelly (Principal)');
        $availableBars = ['Barra Kelly (Principal)', 'Barra Ariel (Subte)', 'Tienda'];

        $liquorSales = collect();
        $mixerSales = collect();
        $totalLiquorCombos = 0;
        $subtotalLiquors = 0;
        $totalMixerConsumed = 0;
        $totalMixerExtras = 0;
        $subtotalMixers = 0;
        $grandTotalBar = 0;
        $closing = null;
        $mapping = Product::getMixerMapping();

        // Variables para Tienda (ventas directas sin inventario de barras)
        $storeSales = collect();
        $storeProducts = collect();
        $totalStoreCombos = 0;
        $totalStoreRevenue = 0;
        $totalStoreCash = 0;
        $totalStoreQr = 0;

        if ($session) {
            $closing = CashClosing::firstOrCreate(['night_session_id' => $session->id]);

            if ($selectedBar === 'Tienda') {
                // Tienda: Despacho directo de combos desde almacén con desglose efectivo/QR
                $storeProducts = Product::where('is_active', true)->orderBy('category')->orderBy('name')->get();
                $storeSales = StoreSale::with('product')
                    ->where('night_session_id', $session->id)
                    ->orderByDesc('id')
                    ->get();

                $totalStoreCombos = $storeSales->sum('quantity');
                $totalStoreRevenue = $storeSales->sum('total_price');
                $totalStoreCash = $storeSales->sum('cash_amount');
                $totalStoreQr = $storeSales->sum('qr_amount');
            } else {
                // Barras tradicionales (Principal y Subte): Apertura, Unidades, Saldo y deducción de sodas
                $existingCount = BarSale::where('night_session_id', $session->id)
                    ->where('bar_name', $selectedBar)
                    ->count();

                if ($existingCount === 0) {
                    $products = Product::where('is_active', true)->get();
                    foreach ($products as $prod) {
                        BarSale::firstOrCreate(
                            [
                                'night_session_id' => $session->id,
                                'product_id' => $prod->id,
                                'bar_name' => $selectedBar,
                            ],
                            [
                                'packages' => 0,
                                'units' => 0,
                                'total_initial' => 0,
                                'saldo' => 0,
                                'vendido' => 0,
                                'unit_price' => $prod->sale_price,
                                'subtotal' => 0,
                            ]
                        );
                    }
                }

                $allBarSales = BarSale::with('product')
                    ->where('night_session_id', $session->id)
                    ->where('bar_name', $selectedBar)
                    ->get();

                $liquorSales = $allBarSales->filter(fn($s) => $s->product && $s->product->category !== 'Mixers');
                $mixerSales = $allBarSales->filter(fn($s) => $s->product && $s->product->category === 'Mixers');

                // Calcular cuántos mixers se cubrieron por los combos vendidos de licores (con ratio)
                $combosPerMixer = [];
                foreach ($liquorSales as $liq) {
                    $mixerInfo = $mapping[$liq->product_id] ?? null;
                    if ($mixerInfo) {
                        $mixerId = $mixerInfo['mixer_id'];
                        $ratio = $mixerInfo['ratio'] ?? 1;
                        $combosPerMixer[$mixerId] = ($combosPerMixer[$mixerId] ?? 0) + ((int)$liq->vendido * $ratio);
                    }
                }

                // Calcular extras para cada mixer
                foreach ($mixerSales as $mix) {
                    $mix->included_in_combos = $combosPerMixer[$mix->product_id] ?? 0;
                    $mix->extras = max(0, (int)$mix->vendido - $mix->included_in_combos);
                    $mix->subtotal = $mix->extras * (float)$mix->unit_price;
                }

                $totalLiquorCombos = $liquorSales->sum('vendido');
                $subtotalLiquors = $liquorSales->sum('subtotal');
                $totalMixerConsumed = $mixerSales->sum('vendido');
                $totalMixerExtras = $mixerSales->sum('extras');
                $subtotalMixers = $mixerSales->sum('subtotal');
                $grandTotalBar = $subtotalLiquors + $subtotalMixers;
            }
        }

        return view('sales.index', compact(
            'session',
            'allSessions',
            'selectedBar',
            'availableBars',
            'liquorSales',
            'mixerSales',
            'mapping',
            'totalLiquorCombos',
            'subtotalLiquors',
            'totalMixerConsumed',
            'totalMixerExtras',
            'subtotalMixers',
            'grandTotalBar',
            'closing',
            'storeSales',
            'storeProducts',
            'totalStoreCombos',
            'totalStoreRevenue',
            'totalStoreCash',
            'totalStoreQr'
        ));
    }

    public function updateBulk(Request $request)
    {
        $rows = $request->input('sales', []);
        $mapping = Product::getMixerMapping();

        // 1ra pasada: Actualizar inventario de licores y calcular subtotal de combos
        $updatedSales = [];
        $combosPerMixer = [];

        foreach ($rows as $id => $data) {
            $barSale = BarSale::with('product')->find($id);
            if ($barSale) {
                $packages = (int)($data['packages'] ?? 0);
                $units = (int)($data['units'] ?? 0);
                $saldo = (int)($data['saldo'] ?? 0);
                $unitPrice = (float)($data['unit_price'] ?? $barSale->unit_price);

                // Si viene de Tienda directa o normal
                $totalInitial = $packages + $units;
                $vendido = max(0, $totalInitial - $saldo);

                // Si es Tienda y se ingresó directamente "vendido"
                if (isset($data['direct_vendido'])) {
                    $vendido = (int)$data['direct_vendido'];
                    $totalInitial = $vendido;
                    $saldo = 0;
                }

                $barSale->packages = $packages;
                $barSale->units = $units;
                $barSale->total_initial = $totalInitial;
                $barSale->saldo = $saldo;
                $barSale->vendido = $vendido;
                $barSale->unit_price = $unitPrice;

                if ($barSale->product && $barSale->product->category !== 'Mixers') {
                    // Licor: subtotal = combos vendidos * precio del combo
                    $barSale->subtotal = $vendido * $unitPrice;
                    $barSale->save();

                    // Acumular mixers incluidos considerando ratio (ej. 2 para agua tónica)
                    $mixerInfo = $mapping[$barSale->product_id] ?? null;
                    if ($mixerInfo) {
                        $mixerId = $mixerInfo['mixer_id'];
                        $ratio = $mixerInfo['ratio'] ?? 1;
                        $combosPerMixer[$mixerId] = ($combosPerMixer[$mixerId] ?? 0) + ($vendido * $ratio);
                    }
                } else {
                    $updatedSales[$id] = $barSale;
                }
            }
        }

        // 2da pasada: Actualizar mixers descontando los incluidos en combos
        foreach ($updatedSales as $id => $mixerSale) {
            $includedInCombos = $combosPerMixer[$mixerSale->product_id] ?? 0;
            $extras = max(0, $mixerSale->vendido - $includedInCombos);
            // Solo se cobran las botellas EXTRAS (que superan los combos)
            $mixerSale->subtotal = $extras * $mixerSale->unit_price;
            $mixerSale->save();
        }

        // Si se enviaron ingresos de Guardarropa o Snacks de Tienda
        if ($request->has('night_session_id')) {
            $session = NightSession::find($request->input('night_session_id'));
            if ($session) {
                $closing = CashClosing::firstOrCreate(['night_session_id' => $session->id]);
                if ($request->has('guardarropa_amount')) {
                    $closing->total_guardarropa = (float)$request->input('guardarropa_amount', 0);
                }
                if ($request->has('snacks_amount')) {
                    $closing->total_snacks = (float)$request->input('snacks_amount', 0);
                }
                $closing->save();

                $this->sessionService->recalculateClosing($session);
            }
        }

        return back()->with('success', 'Ventas de ' . ($request->input('bar_name', 'la barra')) . ' actualizadas.');
    }
}
