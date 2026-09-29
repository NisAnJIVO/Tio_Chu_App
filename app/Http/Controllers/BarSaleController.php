<?php

namespace App\Http\Controllers;

use App\Models\BarSale;
use App\Models\CashClosing;
use App\Models\NightSession;
use App\Models\Product;
use App\Models\StoreSale;
use App\Services\NightSessionService;
use Illuminate\Http\Request;
use App\Models\CategoryMixerOption;

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
                    $previousSession = NightSession::where('session_date', '<', $session->session_date)
                        ->orderByDesc('session_date')
                        ->first() ?? NightSession::where('id', '<', $session->id)->orderByDesc('id')->first();

                    $products = Product::where('is_active', true)->get();
                    foreach ($products as $prod) {
                        $unitsPerPkg = $prod->units_per_package > 0 ? (int)$prod->units_per_package : 1;
                        $initPkg = 0;
                        $initUnits = 0;
                        $totalInit = 0;

                        if ($previousSession) {
                            $prevSale = BarSale::where('night_session_id', $previousSession->id)
                                ->where('bar_name', $selectedBar)
                                ->where('product_id', $prod->id)
                                ->first();

                            if ($prevSale && (int)$prevSale->saldo > 0) {
                                $initPkg = intdiv((int)$prevSale->saldo, $unitsPerPkg);
                                $initUnits = (int)$prevSale->saldo % $unitsPerPkg;
                                $totalInit = (int)$prevSale->saldo;
                            }
                        }

                        BarSale::firstOrCreate(
                            [
                                'night_session_id' => $session->id,
                                'product_id' => $prod->id,
                                'bar_name' => $selectedBar,
                            ],
                            [
                                'initial_packages' => $initPkg,
                                'initial_units' => $initUnits,
                                'added_packages' => 0,
                                'added_units' => 0,
                                'packages' => $initPkg,
                                'units' => $initUnits,
                                'total_initial' => $totalInit,
                                'saldo' => $totalInit,
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

                // Calcular cuántos mixers se cubrieron por los combos vendidos de licores (con deducción de especiales)
                $combosPerMixer = [];
                foreach ($liquorSales as $liq) {
                    $specialMixers = [];
                    if (!empty($liq->selected_special_mixer)) {
                        $decoded = is_string($liq->selected_special_mixer) ? json_decode($liq->selected_special_mixer, true) : $liq->selected_special_mixer;
                        if (is_array($decoded)) {
                            $specialMixers = $decoded;
                        }
                    }

                    $vendido = (int)$liq->vendido;
                    $specialCount = 0;

                    foreach ($specialMixers as $specialMixerId => $qty) {
                        $qty = (int)$qty;
                        if ($qty > 0 && $specialMixerId) {
                            $specialMixerId = (int)$specialMixerId;
                            $combosPerMixer[$specialMixerId] = ($combosPerMixer[$specialMixerId] ?? 0) + $qty;
                            $specialCount += $qty;
                        }
                    }

                    $mixerInfo = $mapping[$liq->product_id] ?? null;
                    if ($mixerInfo) {
                        $defaultMixerId = (int)$mixerInfo['mixer_id'];
                        $ratio = $mixerInfo['ratio'] ?? 1;
                        $remainingCombos = max(0, $vendido - $specialCount);
                        $combosPerMixer[$defaultMixerId] = ($combosPerMixer[$defaultMixerId] ?? 0) + ($remainingCombos * $ratio);
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

                // Obtener mixers especiales y categorías de tragos
                $specialMixerOptions = CategoryMixerOption::all()->groupBy('category');
                $categories = Product::getDrinkSubcategories();
                $allMixerProducts = Product::where('category', 'Mixers')->where('is_active', true)->get();

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
            'totalStoreQr',
            'specialMixerOptions',
            'categories',
            'allMixerProducts'
        ));

    }
    public function storeSpecialMixer(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'mixer_name' => 'required|string',
            'product_id' => 'nullable|exists:products,id',
        ]);

        CategoryMixerOption::firstOrCreate([
            'category' => $request->category,
            'mixer_name' => trim($request->mixer_name),
        ], [
            'product_id' => $request->product_id,
        ]);

        return back()->with('success', 'Variante especial añadida para ' . $request->category);
    }


    public function updateBulk(Request $request)
    {
        $rows = $request->input('sales', []);
        $mapping = Product::getMixerMapping();
        $sessionId = $request->input('night_session_id');
        $barName = $request->input('bar_name', 'Barra Kelly (Principal)');

        // 1ra pasada: Actualizar inventario de licores y calcular subtotal de combos
        $updatedSales = [];
        $combosPerMixer = [];


        foreach ($rows as $id => $data) {
            $barSale = BarSale::with('product')->find($id);
            if (!$barSale) continue;

            if (isset($data['selected_special_mixer'])) {
                $specialVal = $data['selected_special_mixer'];
                if (is_string($specialVal) && (str_starts_with($specialVal, '{') || str_starts_with($specialVal, '['))) {
                    $barSale->selected_special_mixer = $specialVal;
                } elseif (is_array($specialVal)) {
                    $barSale->selected_special_mixer = json_encode($specialVal);
                } else {
                    $barSale->selected_special_mixer = $specialVal;
                }
            }

            $packages = (int)($data['packages'] ?? 0);
            $units = (int)($data['units'] ?? 0);
            $saldo = (int)($data['saldo'] ?? 0);
            $unitPrice = (float)($data['unit_price'] ?? $barSale->unit_price);

            $unitsPerPkg = $barSale->product ? (int)($barSale->product->units_per_package ?? 1) : 1;
            if ($unitsPerPkg < 1) $unitsPerPkg = 1;

            // Recalcular total inicial y vendido desde packages/units/saldo
            $totalInitial = ($packages * $unitsPerPkg) + $units;
            $vendido = max(0, $totalInitial - $saldo);

            $barSale->packages = $packages;
            $barSale->units = $units;
            $barSale->total_initial = $totalInitial;
            $barSale->saldo = $saldo;
            $barSale->vendido = $vendido;
            $barSale->unit_price = $unitPrice;

            if ($barSale->product && $barSale->product->category !== 'Mixers') {
                $barSale->subtotal = $vendido * $unitPrice;
                $barSale->save();

                // Acumular mixers incluidos en combos (con especiales)
                $specialMixers = [];
                if (!empty($barSale->selected_special_mixer)) {
                    $decoded = is_string($barSale->selected_special_mixer)
                        ? json_decode($barSale->selected_special_mixer, true)
                        : $barSale->selected_special_mixer;
                    if (is_array($decoded)) $specialMixers = $decoded;
                }

                $specialCount = 0;
                foreach ($specialMixers as $specialMixerId => $qty) {
                    $qty = (int)$qty;
                    if ($qty > 0 && $specialMixerId) {
                        $specialMixerId = (int)$specialMixerId;
                        $combosPerMixer[$specialMixerId] = ($combosPerMixer[$specialMixerId] ?? 0) + $qty;
                        $specialCount += $qty;
                    }
                }

                $mixerInfo = $mapping[$barSale->product_id] ?? null;
                if ($mixerInfo) {
                    $defaultMixerId = (int)$mixerInfo['mixer_id'];
                    $ratio = $mixerInfo['ratio'] ?? 1;
                    $remainingCombos = max(0, $vendido - $specialCount);
                    $combosPerMixer[$defaultMixerId] = ($combosPerMixer[$defaultMixerId] ?? 0) + ($remainingCombos * $ratio);
                }
            } else {
                // Mixer — guardar pendiente para 2da pasada
                $updatedSales[$id] = $barSale;
            }
        }

        // 2da pasada: Guardar mixers con descuento de combos incluidos
        foreach ($updatedSales as $id => $mixerSale) {
            $includedInCombos = $combosPerMixer[$mixerSale->product_id] ?? 0;
            $extras = max(0, $mixerSale->vendido - $includedInCombos);
            $mixerSale->subtotal = $extras * $mixerSale->unit_price;
            $mixerSale->save();
        }

        // Actualizar guardarropa/snacks si los hay
        if ($sessionId) {
            $session = NightSession::find($sessionId);
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

        // Redirigir de vuelta con los parámetros de sesión y barra para que se vean los cambios
        return redirect()->route('sales.index', [
            'session_id' => $sessionId,
            'bar' => $barName,
        ])->with('success', 'Ventas de ' . $barName . ' guardadas correctamente.');
    }
}
