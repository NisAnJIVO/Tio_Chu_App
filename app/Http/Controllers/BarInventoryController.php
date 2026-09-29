<?php

namespace App\Http\Controllers;

use App\Models\BarSale;
use App\Models\NightSession;
use App\Models\Product;
use App\Services\NightSessionService;
use Illuminate\Http\Request;

class BarInventoryController extends Controller
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
        $availableBars = ['Barra Kelly (Principal)', 'Barra Ariel (Subte)'];

        $liquorSales = collect();
        $mixerSales = collect();
        $previousSession = null;

        if ($session) {
            $dateStr = \Carbon\Carbon::parse($session->session_date)->format('Y-m-d');
            // Buscar la noche anterior real excluyendo la sesión actual
            $previousSession = NightSession::where('id', '!=', $session->id)
                ->whereDate('session_date', '<', $dateStr)
                ->orderByDesc('session_date')
                ->first() ?? NightSession::where('id', '<', $session->id)->orderByDesc('id')->first();

            $existingCount = BarSale::where('night_session_id', $session->id)
                ->where('bar_name', $selectedBar)
                ->count();

            if ($existingCount === 0) {
                $products = Product::where('is_active', true)->get();
                foreach ($products as $prod) {
                    $unitsPerPkg = $prod->units_per_package > 0 ? $prod->units_per_package : 1;
                    $initialPackages = 0;
                    $initialUnits = 0;
                    $totalInitial = 0;

                    // Si hay sesión anterior, jalar el saldo sobrante al cierre
                    if ($previousSession) {
                        $prevSale = BarSale::where('night_session_id', $previousSession->id)
                            ->where('bar_name', $selectedBar)
                            ->where('product_id', $prod->id)
                            ->first();

                        if ($prevSale && (int)$prevSale->saldo > 0) {
                            $initialPackages = intdiv((int)$prevSale->saldo, $unitsPerPkg);
                            $initialUnits = (int)$prevSale->saldo % $unitsPerPkg;
                            $totalInitial = (int)$prevSale->saldo;
                        }
                    }

                    BarSale::create([
                        'night_session_id' => $session->id,
                        'product_id' => $prod->id,
                        'bar_name' => $selectedBar,
                        'initial_packages' => $initialPackages,
                        'initial_units' => $initialUnits,
                        'added_packages' => 0,
                        'added_units' => 0,
                        'packages' => $initialPackages,
                        'units' => $initialUnits,
                        'total_initial' => $totalInitial,
                        'saldo' => $totalInitial,
                        'vendido' => 0,
                        'unit_price' => $prod->sale_price,
                        'subtotal' => 0,
                    ]);
                }
            }

            $allBarSales = BarSale::with('product')
                ->where('night_session_id', $session->id)
                ->where('bar_name', $selectedBar)
                ->get();

            $liquorSales = $allBarSales->filter(fn($s) => $s->product && $s->product->category !== 'Mixers');
            $mixerSales = $allBarSales->filter(fn($s) => $s->product && $s->product->category === 'Mixers');
        }

        return view('bar_inventory.index', compact(
            'session',
            'allSessions',
            'selectedBar',
            'availableBars',
            'liquorSales',
            'mixerSales',
            'previousSession'
        ));
    }

    public function updateBulk(Request $request)
    {
        $rows = $request->input('inventory', []);
        $selectedBar = $request->input('bar_name', 'Barra Kelly (Principal)');
        $sessionId = $request->input('night_session_id');

        foreach ($rows as $id => $data) {
            $barSale = BarSale::with('product')->find($id);
            if ($barSale && $barSale->product) {
                $unitsPerPkg = $barSale->product->units_per_package > 0 ? (int)$barSale->product->units_per_package : 1;

                $initPkg = (int)($data['initial_packages'] ?? 0);
                $initUnits = (int)($data['initial_units'] ?? 0);
                $addPkg = (int)($data['added_packages'] ?? 0);
                $addUnits = (int)($data['added_units'] ?? 0);

                // Consolidar totales de apertura
                $totalPkg = $initPkg + $addPkg;
                $totalUnits = $initUnits + $addUnits;
                $totalInitial = ($totalPkg * $unitsPerPkg) + $totalUnits;

                $barSale->initial_packages = $initPkg;
                $barSale->initial_units = $initUnits;
                $barSale->added_packages = $addPkg;
                $barSale->added_units = $addUnits;
                $barSale->packages = $totalPkg;
                $barSale->units = $totalUnits;
                $barSale->total_initial = $totalInitial;

                // Ajustar saldo y vendido consistentemente
                $vendido = max(0, $totalInitial - (int)$barSale->saldo);
                $barSale->vendido = $vendido;

                if ($barSale->product->category !== 'Mixers') {
                    $barSale->subtotal = $vendido * (float)$barSale->unit_price;
                }

                $barSale->save();
            }
        }

        return redirect()->route('barInventory.index', [
            'session_id' => $sessionId,
            'bar' => $selectedBar,
        ])->with('success', 'Inventario y aperturas de ' . $selectedBar . ' guardados correctamente.');
    }

    /**
     * Sincronizar saldos de la noche anterior (en caso de que se haya reabierto o cambiado)
     */
    public function syncFromPreviousNight(Request $request)
    {
        $sessionId = $request->input('session_id');
        $selectedBar = $request->input('bar_name');

        $session = NightSession::findOrFail($sessionId);
        $dateStr = \Carbon\Carbon::parse($session->session_date)->format('Y-m-d');
        $previousSession = NightSession::where('id', '!=', $session->id)
            ->whereDate('session_date', '<', $dateStr)
            ->orderByDesc('session_date')
            ->first() ?? NightSession::where('id', '<', $session->id)->orderByDesc('id')->first();

        if (!$previousSession) {
            return back()->with('error', 'No se encontró una noche anterior para sincronizar saldos.');
        }

        $allBarSales = BarSale::with('product')
            ->where('night_session_id', $session->id)
            ->where('bar_name', $selectedBar)
            ->get();

        foreach ($allBarSales as $barSale) {
            $prevSale = BarSale::where('night_session_id', $previousSession->id)
                ->where('bar_name', $selectedBar)
                ->where('product_id', $barSale->product_id)
                ->first();

            $prevSaldo = $prevSale ? (int)$prevSale->saldo : 0;
            $unitsPerPkg = $barSale->product->units_per_package > 0 ? (int)$barSale->product->units_per_package : 1;

            $initPkg = intdiv($prevSaldo, $unitsPerPkg);
            $initUnits = $prevSaldo % $unitsPerPkg;

            $barSale->initial_packages = $initPkg;
            $barSale->initial_units = $initUnits;
            $barSale->packages = $initPkg + (int)$barSale->added_packages;
            $barSale->units = $initUnits + (int)$barSale->added_units;
            $barSale->total_initial = ($barSale->packages * $unitsPerPkg) + $barSale->units;
            // Si el saldo no se había editado, ajustarlo al total inicial
            if ((int)$barSale->vendido === 0) {
                $barSale->saldo = $barSale->total_initial;
            }
            $barSale->vendido = max(0, $barSale->total_initial - (int)$barSale->saldo);
            if ($barSale->product->category !== 'Mixers') {
                $barSale->subtotal = $barSale->vendido * (float)$barSale->unit_price;
            }
            $barSale->save();
        }

        return back()->with('success', 'Saldos de la noche anterior sincronizados correctamente para ' . $selectedBar . '.');
    }
}
