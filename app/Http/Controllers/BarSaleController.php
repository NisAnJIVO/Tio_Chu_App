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
        $session = $this->sessionService->resolveSession($request->get('session_id'));
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

        // Variables de barras (solo usadas cuando no es Tienda) — inicializadas por defecto
        $specialMixerOptions = collect();
        $categories = [];
        $allMixerProducts = collect();

        if ($session) {
            $closing = CashClosing::firstOrCreate(['night_session_id' => $session->id]);

            // Pagos QR y Facturas/Tarjetas vinculados al punto de venta actual
            if ($selectedBar === 'Tienda') {
                $qrPosNames = ['Tienda', 'tienda'];
                $invoiceBarNames = ['Tienda', 'tienda'];
            } elseif (str_contains($selectedBar, 'Subte') || str_contains($selectedBar, 'Ariel')) {
                $qrPosNames = ['Subte', 'Barra Subte', 'Subterráneo', 'Subterraneo', 'Barra Ariel (Subte)', 'Ariel'];
                $invoiceBarNames = ['Subterráneo', 'Subterraneo', 'Subte', 'Barra Ariel (Subte)', 'Ariel'];
            } else {
                // Barra Kelly (Principal)
                $qrPosNames = ['Barra Principal', 'Principal', 'Barra Kelly (Principal)', 'Kelly'];
                $invoiceBarNames = ['Principal', 'Barra Kelly (Principal)', 'Kelly'];
            }

            $barQrPayments = $session->qrPayments()
                ->whereIn('point_of_sale', $qrPosNames)
                ->orderByDesc('id')
                ->get();

            $barTotalQrYasta = (float)$barQrPayments->where('bank_app', 'YASTA')->sum('amount');
            $barTotalQrYape  = (float)$barQrPayments->where('bank_app', 'YAPE')->sum('amount');
            $barTotalQr      = (float)$barQrPayments->sum('amount');

            $barInvoices = $session->invoices()
                ->whereIn('bar_name', $invoiceBarNames)
                ->orderBy('correlative_num')
                ->get();

            $barCardInvoices = $barInvoices->where('payment_method', 'tarjeta');
            $barTotalCard           = (float)$barCardInvoices->sum('amount');
            $barTotalCardCommission = (float)$barCardInvoices->sum('commission_amount');
            $barTotalCardNet        = (float)$barCardInvoices->sum('net_amount');

            if ($selectedBar === 'Tienda') {
                // Tienda: Despacho directo de combos desde almacén con desglose efectivo/QR
                $storeProducts = Product::where('is_active', true)->orderBy('category')->orderBy('name')->get();
                $storeSales = StoreSale::with('product')
                    ->where('night_session_id', $session->id)
                    ->orderByDesc('id')
                    ->get();

                $totalStoreCombos = $storeSales->sum('quantity');
                $totalStoreCash = (float)$storeSales->sum('cash_amount');
                $storeSalesQr = (float)$storeSales->sum('qr_amount');
                // En QR Tienda se toma el total de cobros QR registrados para Tienda
                $totalStoreQr = $barTotalQr > 0 ? $barTotalQr : $storeSalesQr;
                $totalStoreRevenue = $totalStoreCash + $totalStoreQr;
                $barCashRemaining = $totalStoreCash;
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

                $barCashRemaining = max(0, $grandTotalBar - $barTotalQr - $barTotalCardNet);
            }
        } else {
            $barQrPayments = collect();
            $barTotalQr = 0;
            $barTotalQrYasta = 0;
            $barTotalQrYape = 0;
            $barInvoices = collect();
            $barCardInvoices = collect();
            $barTotalCard = 0;
            $barTotalCardCommission = 0;
            $barTotalCardNet = 0;
            $barCashRemaining = 0;
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
            'allMixerProducts',
            'barQrPayments',
            'barTotalQr',
            'barTotalQrYasta',
            'barTotalQrYape',
            'barInvoices',
            'barCardInvoices',
            'barTotalCard',
            'barTotalCardCommission',
            'barTotalCardNet',
            'barCashRemaining'
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
        $sessionId = $request->input('night_session_id');
        $session = NightSession::find($sessionId);
        if ($session && !$session->isOpen()) {
            return back()->with('error', 'La noche está cerrada. Para modificar ventas o combos, reabre la noche desde Cierre de Caja.');
        }

        $rows = $request->input('sales', []);
        $mapping = Product::getMixerMapping();
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
            $this->syncProductStock($barSale);

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

        // Propagar saldos a la siguiente noche si existe (para mantener el inventario sincronizado)
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

                // Buscar la siguiente noche inmediata
                $dateStr = \Carbon\Carbon::parse($session->session_date)->format('Y-m-d');
                $nextSession = NightSession::where('id', '!=', $session->id)
                    ->whereDate('session_date', '>', $dateStr)
                    ->orderBy('session_date')
                    ->first() ?? NightSession::where('id', '>', $session->id)->orderBy('id')->first();

                if ($nextSession) {
                    $nextSales = BarSale::with('product')
                        ->where('night_session_id', $nextSession->id)
                        ->where('bar_name', $barName)
                        ->get();

                    foreach ($nextSales as $nextSale) {
                        if (!$nextSale->product) continue;
                        $curSale = BarSale::where('night_session_id', $session->id)
                            ->where('bar_name', $barName)
                            ->where('product_id', $nextSale->product_id)
                            ->first();

                        $curSaldo = $curSale ? (int)$curSale->saldo : 0;
                        $unitsPerPkg = $nextSale->product->units_per_package > 0 ? (int)$nextSale->product->units_per_package : 1;

                        $nextInitPkg = intdiv($curSaldo, $unitsPerPkg);
                        $nextInitUnits = $curSaldo % $unitsPerPkg;

                        $nextSale->initial_packages = $nextInitPkg;
                        $nextSale->initial_units = $nextInitUnits;

                        $totNightPkg = $nextInitPkg + (int)$nextSale->added_packages + (int)$nextSale->night_packages;
                        $totNightUnits = $nextInitUnits + (int)$nextSale->added_units + (int)$nextSale->night_units;
                        $totNightBot = ($totNightPkg * $unitsPerPkg) + $totNightUnits;

                        $nextSale->packages = $totNightPkg;
                        $nextSale->units = $totNightUnits;
                        $nextSale->total_initial = $totNightBot;

                        if ((int)$nextSale->vendido === 0) {
                            $nextSale->saldo = $totNightBot;
                        } else {
                            $nextSale->vendido = max(0, $totNightBot - (int)$nextSale->saldo);
                        }

                        $this->syncProductStock($nextSale);

                        if ($nextSale->product->category !== 'Mixers') {
                            $nextSale->subtotal = $nextSale->vendido * (float)$nextSale->unit_price;
                        }

                        $nextSale->save();
                    }
                }
            }
        }

        // Redirigir de vuelta con los parámetros de sesión y barra para que se vean los cambios
        return redirect()->route('sales.index', [
            'session_id' => $sessionId,
            'bar' => $barName,
        ])->with('success', 'Ventas de ' . $barName . ' guardadas correctamente.');
    }

    private function syncProductStock(BarSale $barSale): void
    {
        if (!$barSale->product) {
            return;
        }

        $previouslySynced = (int) $barSale->stock_synced_vendido;
        $currentSold = max(0, (int) $barSale->vendido);
        $difference = $currentSold - $previouslySynced;

        if ($difference !== 0) {
            $product = $barSale->product;
            $product->stock_warehouse = max(0, (int) $product->stock_warehouse - $difference);
            $unitsPerPackage = max(1, (int) $product->units_per_package);
            $product->stock_packages = intdiv($product->stock_warehouse, $unitsPerPackage);
            $product->stock_units = $product->stock_warehouse % $unitsPerPackage;
            $product->save();
        }

        $barSale->stock_synced_vendido = $currentSold;
    }
}
