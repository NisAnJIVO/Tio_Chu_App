@extends('layouts.app')

@section('title', 'Ventas por Barra & Combos')

@section('content')
<div class="space-y-6">

    <!-- Cabecera de la Vista -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Inventario y Ventas por Punto de Venta</h2>
            <p class="text-xs text-zinc-600">Control de licores por combo con deducción automática de sodas y registro directo de Tienda.</p>
        </div>

        <!-- Selector de Noche -->
        <div class="flex flex-wrap items-center gap-3">
            @if($allSessions->isNotEmpty())
                <form method="GET" action="{{ route('sales.index') }}" class="flex items-center gap-2">
                    <input type="hidden" name="bar" value="{{ $selectedBar }}">
                    <label for="session_id" class="text-xs font-medium text-zinc-700">Noche:</label>
                    <select name="session_id" id="session_id" onchange="this.form.submit()" 
                            class="text-xs border border-zinc-300 rounded bg-white px-2.5 py-1.5 text-zinc-800 focus:outline-none focus:ring-1 focus:ring-zinc-900">
                        @foreach($allSessions as $s)
                            <option value="{{ $s->id }}" {{ $session && $session->id === $s->id ? 'selected' : '' }}>
                                {{ $s->day_name }} {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>
    </div>

    @if(!$session)
        <div class="bg-white border border-zinc-200 rounded p-8 text-center">
            <h3 class="text-sm font-semibold text-zinc-800">No hay noche seleccionada</h3>
            <p class="text-xs text-zinc-600 mt-1 mb-4">Crea una noche para registrar las ventas.</p>
            <a href="{{ route('sessions.create') }}" class="inline-flex items-center px-3 py-1.5 bg-zinc-900 text-white text-xs font-medium rounded hover:bg-zinc-800">
                + Crear Noche
            </a>
        </div>
    @else

        <!-- Selector de Puntos de Venta (3 Pestañas: Barra Kelly, Barra Ariel, Tienda) -->
        <div class="flex border-b border-zinc-200 gap-1 text-xs">
            @foreach($availableBars as $bar)
                <a href="{{ route('sales.index', ['session_id' => $session->id, 'bar' => $bar]) }}" 
                   class="px-4 py-2 border-b-2 font-medium transition-colors {{ $selectedBar === $bar ? 'border-zinc-900 text-zinc-900 font-bold bg-white' : 'border-transparent text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                    {{ $bar }}
                </a>
            @endforeach
        </div>

        @if($selectedBar === 'Tienda')
            <!-- ========================================== -->
            <!-- VISTA EXCLUSIVA PARA TIENDA (DESPACHO DIRECTO) -->
            <!-- ========================================== -->

            <!-- Resumen de Totales de Tienda -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="bg-white border border-zinc-200 rounded p-4">
                    <span class="text-xs font-medium text-zinc-600">Combos Despachados</span>
                    <div class="mt-1 text-2xl font-bold tracking-tight text-zinc-900 font-mono">
                        {{ $totalStoreCombos }} combos
                    </div>
                    <p class="text-[10px] text-zinc-500 mt-1">Salida directa de almacén</p>
                </div>

                <div class="bg-white border border-zinc-200 rounded p-4">
                    <span class="text-xs font-medium text-zinc-600">Total Recaudado Tienda</span>
                    <div class="mt-1 text-2xl font-bold tracking-tight text-zinc-900 font-mono">
                        Bs. {{ number_format($totalStoreRevenue, 2) }}
                    </div>
                    <p class="text-[10px] text-zinc-500 mt-1">Efectivo + QR</p>
                </div>

                <div class="bg-white border border-zinc-200 rounded p-4">
                    <span class="text-xs font-medium text-zinc-600">En Efectivo</span>
                    <div class="mt-1 text-2xl font-bold tracking-tight text-emerald-800 font-mono">
                        Bs. {{ number_format($totalStoreCash, 2) }}
                    </div>
                    <p class="text-[10px] text-zinc-500 mt-1">Pagado físicamente</p>
                </div>

                <div class="bg-white border border-zinc-200 rounded p-4">
                    <span class="text-xs font-medium text-zinc-600">En QR</span>
                    <div class="mt-1 text-2xl font-bold tracking-tight text-blue-800 font-mono">
                        Bs. {{ number_format($totalStoreQr, 2) }}
                    </div>
                    <p class="text-[10px] text-zinc-500 mt-1">Transferencias bancarias</p>
                </div>
            </div>

            <!-- Formulario de Despacho Rápido de Combo en Tienda -->
            <div class="bg-white border border-zinc-200 rounded p-5">
                <div class="border-b border-zinc-200 pb-3 mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">
                            Despachar Combo en Tienda
                        </h3>
                        <p class="text-[11px] text-zinc-600">
                            El mesero pide el combo, paga o muestra comprobante de QR, y la tienda despacha directo con desglose de pago.
                        </p>
                    </div>
                    <span class="text-[11px] px-2 py-0.5 rounded bg-zinc-100 text-zinc-800 font-medium border border-zinc-200">
                        Sin inventario previo
                    </span>
                </div>

                <form method="POST" action="{{ route('sales.storeSales.store') }}" id="tienda-order-form" class="space-y-4">
                    @csrf
                    <input type="hidden" name="night_session_id" value="{{ $session->id }}">

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        
                        <!-- Producto / Combo -->
                        <div class="sm:col-span-2">
                            <label for="product_id" class="block text-xs font-semibold text-zinc-700 mb-1">
                                Combo / Producto Solicitado
                            </label>
                            <select name="product_id" id="product_id" required 
                                    class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 font-medium focus:outline-none focus:ring-1 focus:ring-zinc-900">
                                <option value="" disabled selected>-- Seleccionar Combo / Bebida --</option>
                                @foreach($storeProducts as $prod)
                                    <option value="{{ $prod->id }}" data-price="{{ $prod->sale_price }}">
                                        {{ $prod->name }} (Bs. {{ number_format($prod->sale_price, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Cantidad -->
                        <div>
                            <label for="quantity" class="block text-xs font-semibold text-zinc-700 mb-1">
                                Cantidad
                            </label>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" required 
                                   class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 font-mono font-bold focus:outline-none focus:ring-1 focus:ring-zinc-900">
                        </div>

                        <!-- Precio Unitario -->
                        <div>
                            <label for="unit_price" class="block text-xs font-semibold text-zinc-700 mb-1">
                                Precio Unitario (Bs.)
                            </label>
                            <input type="number" step="0.5" name="unit_price" id="unit_price" value="0.00" min="0" required 
                                   class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 font-mono font-bold focus:outline-none focus:ring-1 focus:ring-zinc-900">
                        </div>

                    </div>

                    <!-- Fila de Cobrante y Desglose de Pago (Efectivo / QR) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 pt-2 border-t border-zinc-100 items-end">
                        
                        <!-- Nombre del Cobrante / Mesero -->
                        <div class="md:col-span-2">
                            <label for="cobrante_name" class="block text-xs font-semibold text-zinc-700 mb-1">
                                Nombre del Cobrante (Mesero)
                            </label>
                            <input type="text" name="cobrante_name" id="cobrante_name" required placeholder="Ej. Juan, Mau, Ari..."
                                   class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 uppercase focus:outline-none focus:ring-1 focus:ring-zinc-900">
                        </div>

                        <!-- Total Calculado -->
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 mb-1">
                                Total Pedido (Bs.)
                            </label>
                            <div class="px-3 py-2 bg-zinc-100 border border-zinc-300 rounded text-zinc-900 font-mono font-bold text-sm" id="tienda-total-display">
                                Bs. 0.00
                            </div>
                        </div>

                        <!-- Monto en Efectivo -->
                        <div>
                            <label for="cash_amount" class="block text-xs font-semibold text-zinc-700 mb-1">
                                Monto Efectivo (Bs.)
                            </label>
                            <input type="number" step="0.5" name="cash_amount" id="cash_amount" value="0.00" min="0" required 
                                   class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 font-mono font-bold focus:outline-none focus:ring-1 focus:ring-zinc-900">
                        </div>

                        <!-- Monto en QR -->
                        <div>
                            <label for="qr_amount" class="block text-xs font-semibold text-zinc-700 mb-1">
                                Monto QR (Bs.)
                            </label>
                            <input type="number" step="0.5" name="qr_amount" id="qr_amount" value="0.00" min="0" required 
                                   class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 font-mono font-bold focus:outline-none focus:ring-1 focus:ring-zinc-900">
                        </div>

                    </div>

                    <!-- Botones de ayuda rápida para el pago y sincronización con QRs -->
                    <div class="flex flex-wrap items-center justify-between gap-3 pt-2 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="text-zinc-600 font-medium text-[11px]">Llenado Rápido:</span>
                            <button type="button" id="btn-all-cash" class="px-2 py-1 bg-zinc-100 hover:bg-zinc-200 border border-zinc-300 rounded text-[11px] font-medium text-zinc-800">
                                Todo Efectivo
                            </button>
                            <button type="button" id="btn-all-qr" class="px-2 py-1 bg-zinc-100 hover:bg-zinc-200 border border-zinc-300 rounded text-[11px] font-medium text-zinc-800">
                                Todo QR
                            </button>
                            <button type="button" id="btn-split-50" class="px-2 py-1 bg-zinc-100 hover:bg-zinc-200 border border-zinc-300 rounded text-[11px] font-medium text-zinc-800">
                                50% Efectivo / 50% QR
                            </button>
                        </div>

                        <!-- Opciones de QR -->
                        <div class="flex items-center gap-4" id="qr-options-box">
                            <div class="flex items-center gap-1.5">
                                <label for="bank_app" class="text-[11px] font-semibold text-zinc-700">Banco QR:</label>
                                <select name="bank_app" id="bank_app" 
                                        class="text-xs border border-zinc-300 rounded px-2 py-1 text-zinc-900 bg-white focus:outline-none focus:ring-1 focus:ring-zinc-900">
                                    <option value="YASTA">YASTA (Unión)</option>
                                    <option value="YAPE">YAPE (BCP)</option>
                                </select>
                            </div>

                            <label class="inline-flex items-center gap-1.5 text-[11px] text-zinc-700 cursor-pointer">
                                <input type="checkbox" name="sync_qr" value="1" checked class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900">
                                <span>Anotar en lista de QRs (Tienda)</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-zinc-200 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-zinc-900 text-white text-xs font-semibold rounded hover:bg-zinc-800 transition-colors">
                            + Registrar Pedido en Tienda
                        </button>
                    </div>

                </form>
            </div>

            <!-- Tabla de Combos Despachados en Tienda -->
            <div class="bg-white border border-zinc-200 rounded overflow-hidden">
                <div class="px-4 py-3 bg-zinc-50 border-b border-zinc-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">
                            Registro de Combos Despachados en Tienda ({{ $storeSales->count() }})
                        </h3>
                        <p class="text-[11px] text-zinc-600">Lista cronológica de pedidos atendidos en tienda durante la noche.</p>
                    </div>
                    <span class="text-xs font-mono font-bold text-zinc-900">
                        Total: Bs. {{ number_format($totalStoreRevenue, 2) }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-zinc-100/70 border-b border-zinc-200 text-zinc-600 font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="px-3 py-2.5 w-12 text-center">N°</th>
                                <th class="px-3 py-2.5">Combo / Bebida</th>
                                <th class="px-3 py-2.5 w-20 text-center">Cantidad</th>
                                <th class="px-3 py-2.5 w-24 text-right">P. Unitario</th>
                                <th class="px-3 py-2.5 w-28 text-right font-bold text-zinc-900">Total (Bs.)</th>
                                <th class="px-3 py-2.5 w-28 text-right text-emerald-800">Efectivo</th>
                                <th class="px-3 py-2.5 w-28 text-right text-blue-800">QR</th>
                                <th class="px-3 py-2.5">Nombre del Cobrante</th>
                                <th class="px-3 py-2.5 w-16 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200">
                            @forelse($storeSales as $index => $item)
                                <tr class="hover:bg-zinc-50/50">
                                    <td class="px-3 py-2 text-center font-mono text-zinc-600">{{ $index + 1 }}</td>
                                    <td class="px-3 py-2 font-medium text-zinc-900">{{ $item->product->name ?? '—' }}</td>
                                    <td class="px-3 py-2 text-center font-mono font-semibold">{{ $item->quantity }}</td>
                                    <td class="px-3 py-2 text-right font-mono">Bs. {{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-3 py-2 text-right font-mono font-bold text-zinc-900">Bs. {{ number_format($item->total_price, 2) }}</td>
                                    <td class="px-3 py-2 text-right font-mono text-emerald-700">Bs. {{ number_format($item->cash_amount, 2) }}</td>
                                    <td class="px-3 py-2 text-right font-mono text-blue-700">Bs. {{ number_format($item->qr_amount, 2) }}</td>
                                    <td class="px-3 py-2 uppercase font-medium text-zinc-800">{{ $item->cobrante_name }}</td>
                                    <td class="px-3 py-2 text-right">
                                        <form method="POST" action="{{ route('sales.storeSales.destroy', $item) }}" onsubmit="return confirm('¿Eliminar este pedido de Tienda?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-semibold text-[11px]">Borrar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-zinc-500">
                                        No hay combos registrados en Tienda para esta noche.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-zinc-100 border-t-2 border-zinc-300 font-bold text-xs text-zinc-900">
                            <tr>
                                <td colspan="2" class="px-3 py-2.5 text-right uppercase tracking-wider">Totales Tienda:</td>
                                <td class="px-3 py-2.5 text-center font-mono">{{ $totalStoreCombos }}</td>
                                <td></td>
                                <td class="px-3 py-2.5 text-right font-mono">Bs. {{ number_format($totalStoreRevenue, 2) }}</td>
                                <td class="px-3 py-2.5 text-right font-mono text-emerald-800">Bs. {{ number_format($totalStoreCash, 2) }}</td>
                                <td class="px-3 py-2.5 text-right font-mono text-blue-800">Bs. {{ number_format($totalStoreQr, 2) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Guardarropa y Snacks -->
            <div class="bg-white border border-zinc-200 rounded p-5">
                <div class="border-b border-zinc-200 pb-3 mb-4">
                    <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">
                        Guardarropa y Mini Tienda (Snacks / Dulces)
                    </h3>
                    <p class="text-[11px] text-zinc-600">
                        Ingresos adicionales gestionados desde Tienda para el cierre general de caja.
                    </p>
                </div>

                <form method="POST" action="{{ route('sales.tiendaExtras.update') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                    @csrf
                    <input type="hidden" name="night_session_id" value="{{ $session->id }}">

                    <div>
                        <label for="guardarropa_amount" class="block text-xs font-semibold text-zinc-700 mb-1">
                            Ingresos Guardarropa (Bs.)
                        </label>
                        <input type="number" step="0.5" name="guardarropa_amount" id="guardarropa_amount" 
                               value="{{ $closing->total_guardarropa ?? 0 }}" min="0"
                               class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 font-mono font-bold focus:outline-none focus:ring-1 focus:ring-zinc-900">
                    </div>

                    <div>
                        <label for="snacks_amount" class="block text-xs font-semibold text-zinc-700 mb-1">
                            Ingresos Snacks / Dulces (Bs.)
                        </label>
                        <input type="number" step="0.5" name="snacks_amount" id="snacks_amount" 
                               value="{{ $closing->total_snacks ?? 0 }}" min="0"
                               class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 font-mono font-bold focus:outline-none focus:ring-1 focus:ring-zinc-900">
                    </div>

                    <div>
                        <button type="submit" class="w-full py-2 bg-zinc-800 text-white text-xs font-semibold rounded hover:bg-zinc-700 transition-colors">
                            Guardar Guardarropa & Snacks
                        </button>
                    </div>
                </form>
            </div>

            <!-- Script para Tienda -->
            <script>
            document.addEventListener('DOMContentLoaded', function () {
                const productSelect = document.getElementById('product_id');
                const quantityInput = document.getElementById('quantity');
                const unitPriceInput = document.getElementById('unit_price');
                const cashInput = document.getElementById('cash_amount');
                const qrInput = document.getElementById('qr_amount');
                const totalDisplay = document.getElementById('tienda-total-display');

                function updateCalculations() {
                    const qty = parseInt(quantityInput.value) || 0;
                    const price = parseFloat(unitPriceInput.value) || 0;
                    const total = qty * price;
                    totalDisplay.textContent = 'Bs. ' + total.toFixed(2);
                    return total;
                }

                if (productSelect) {
                    productSelect.addEventListener('change', function () {
                        const selected = this.options[this.selectedIndex];
                        const price = parseFloat(selected.getAttribute('data-price')) || 0;
                        unitPriceInput.value = price.toFixed(2);
                        const total = updateCalculations();
                        // Por defecto, asignar todo a efectivo
                        cashInput.value = total.toFixed(2);
                        qrInput.value = '0.00';
                    });
                }

                if (quantityInput) {
                    quantityInput.addEventListener('input', function () {
                        const total = updateCalculations();
                        cashInput.value = total.toFixed(2);
                        qrInput.value = '0.00';
                    });
                }

                if (unitPriceInput) {
                    unitPriceInput.addEventListener('input', function () {
                        const total = updateCalculations();
                        cashInput.value = total.toFixed(2);
                        qrInput.value = '0.00';
                    });
                }

                document.getElementById('btn-all-cash')?.addEventListener('click', function () {
                    const total = updateCalculations();
                    cashInput.value = total.toFixed(2);
                    qrInput.value = '0.00';
                });

                document.getElementById('btn-all-qr')?.addEventListener('click', function () {
                    const total = updateCalculations();
                    qrInput.value = total.toFixed(2);
                    cashInput.value = '0.00';
                });

                document.getElementById('btn-split-50')?.addEventListener('click', function () {
                    const total = updateCalculations();
                    const half = (total / 2).toFixed(2);
                    cashInput.value = half;
                    qrInput.value = (total - parseFloat(half)).toFixed(2);
                });
            });
            </script>

        @else
            <!-- ======================================================== -->
            <!-- VISTA DE BARRAS TRADICIONALES (BARRA PRINCIPAL Y SUBTE) -->
            <!-- ======================================================== -->

            <!-- Resumen de Totales del Punto de Venta Actual -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white border border-zinc-200 rounded p-4">
                    <span class="text-xs font-medium text-zinc-600">Combos Vendidos (Licores)</span>
                    <div class="mt-1 text-2xl font-bold tracking-tight text-zinc-900" id="header-total-combos">
                        {{ $totalLiquorCombos }} combos
                    </div>
                    <p class="text-[11px] text-zinc-600 mt-1 font-mono" id="header-subtotal-combos">
                        Subtotal: Bs. {{ number_format($subtotalLiquors, 2) }}
                    </p>
                </div>

                <div class="bg-white border border-zinc-200 rounded p-4">
                    <span class="text-xs font-medium text-zinc-600">Sodas / Mixers Extras</span>
                    <div class="mt-1 text-2xl font-bold tracking-tight text-zinc-900 font-mono" id="header-total-extras">
                        {{ $totalMixerExtras }} extras
                    </div>
                    <p class="text-[11px] text-zinc-600 mt-1 font-mono" id="header-subtotal-extras">
                        Subtotal: Bs. {{ number_format($subtotalMixers, 2) }}
                    </p>
                </div>

                <div class="bg-white border border-zinc-200 rounded p-4">
                    <span class="text-xs font-medium text-zinc-600">Total Bebidas en {{ $selectedBar }}</span>
                    <div class="mt-1 text-2xl font-bold tracking-tight text-zinc-900 font-mono" id="header-grand-total">
                        Bs. {{ number_format($grandTotalBar, 2) }}
                    </div>
                    <p class="text-[11px] text-zinc-600 mt-1">Combos + Sodas adicionales</p>
                </div>
            </div>

            <!-- Formulario Tabular de Inventario y Ventas para Barras -->
            <form method="POST" action="{{ route('sales.updateBulk') }}" id="sales-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="night_session_id" value="{{ $session->id }}">
                <input type="hidden" name="bar_name" value="{{ $selectedBar }}">

                <div class="space-y-6">

                    <!-- 1. TABLA DE LICORES (COMBOS) -->
                    <div class="bg-white border border-zinc-200 rounded overflow-hidden">
                        <div class="px-4 py-3 bg-zinc-50 border-b border-zinc-200 flex items-center justify-between">
                            <div>
                                <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">1. Licores & Combos</h3>
                                <p class="text-[11px] text-zinc-600">
                                    Cada combo incluye su soda (Gins incluyen <strong>2 Aguas Tónicas</strong>).
                                </p>
                            </div>
                            <span class="text-xs font-mono font-semibold text-zinc-800" id="badge-liquor-subtotal">
                                Subtotal: Bs. {{ number_format($subtotalLiquors, 2) }}
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left" id="table-liquors">
                                <thead class="bg-zinc-100/70 border-b border-zinc-200 text-zinc-600 font-semibold uppercase tracking-wider">
                                    <tr>
                                        <th class="px-3 py-2.5 w-10 text-center">N°</th>
                                        <th class="px-3 py-2.5">Licor</th>
                                        <th class="px-3 py-2.5">Mixer Incluido</th>
                                        <th class="px-2 py-2.5 w-20 text-center">Paquete</th>
                                        <th class="px-2 py-2.5 w-20 text-center">Unidad</th>
                                        <th class="px-3 py-2.5 w-20 text-center">Total Inicial</th>
                                        <th class="px-2 py-2.5 w-20 text-center">Saldo</th>
                                        <th class="px-3 py-2.5 w-24 text-center font-bold text-zinc-900">Combos Vendidos</th>
                                        <th class="px-3 py-2.5 w-28 text-right">Precio Combo (Bs.)</th>
                                        <th class="px-3 py-2.5 w-32 text-right">Subtotal (Bs.)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200">
                                    @forelse($liquorSales as $index => $sale)
                                        @php
                                            $mixerInfo = $mapping[$sale->product_id] ?? null;
                                            $mixerId = $mixerInfo['mixer_id'] ?? 0;
                                            $mixerRatio = $mixerInfo['ratio'] ?? 1;
                                            $mixerName = 'Solo';
                                            if ($mixerId == 23) $mixerName = '+ 1 Ginger Ale 2L';
                                            elseif ($mixerId == 24) $mixerName = '+ 1 Coca Cola 2L';
                                            elseif ($mixerId == 25) $mixerName = '+ 1 Agua Vital 2L';
                                            elseif ($mixerId == 26) $mixerName = '+ 2 Aguas Tónicas 1L';
                                        @endphp
                                        <tr class="hover:bg-zinc-50/50 liquor-row" 
                                            data-row-id="{{ $sale->id }}" 
                                            data-product-id="{{ $sale->product_id }}"
                                            data-mixer-id="{{ $mixerId }}"
                                            data-mixer-ratio="{{ $mixerRatio }}">
                                            <td class="px-3 py-2 text-center font-mono text-zinc-600">{{ $index + 1 }}</td>
                                            <td class="px-3 py-2 font-medium text-zinc-900">
                                                {{ $sale->product->name }}
                                            </td>
                                            <td class="px-3 py-2 text-zinc-600 font-mono text-[11px]">
                                                <span class="inline-flex px-1.5 py-0.5 rounded {{ $mixerRatio == 2 ? 'bg-amber-50 text-amber-800 border border-amber-200' : '' }}">
                                                    {{ $mixerName }}
                                                </span>
                                            </td>
                                            <td class="px-2 py-1 text-center">
                                                <input type="number" name="sales[{{ $sale->id }}][packages]" value="{{ $sale->packages }}" min="0"
                                                       class="w-16 text-center border border-zinc-300 rounded px-2 py-1 text-xs font-mono text-zinc-900 input-packages focus:outline-none focus:ring-1 focus:ring-zinc-900">
                                            </td>
                                            <td class="px-2 py-1 text-center">
                                                <input type="number" name="sales[{{ $sale->id }}][units]" value="{{ $sale->units }}" min="0"
                                                       class="w-16 text-center border border-zinc-300 rounded px-2 py-1 text-xs font-mono text-zinc-900 input-units focus:outline-none focus:ring-1 focus:ring-zinc-900">
                                            </td>
                                            <td class="px-3 py-2 text-center font-mono font-semibold text-zinc-800 cell-total-initial">
                                                {{ $sale->total_initial }}
                                            </td>
                                            <td class="px-2 py-1 text-center">
                                                <input type="number" name="sales[{{ $sale->id }}][saldo]" value="{{ $sale->saldo }}" min="0"
                                                       class="w-16 text-center border border-zinc-300 rounded px-2 py-1 text-xs font-mono text-zinc-900 input-saldo focus:outline-none focus:ring-1 focus:ring-zinc-900">
                                            </td>
                                            <td class="px-3 py-2 text-center font-mono font-bold text-zinc-900 cell-vendido">
                                                {{ $sale->vendido }}
                                            </td>
                                            <td class="px-2 py-1 text-right">
                                                <input type="number" step="0.5" name="sales[{{ $sale->id }}][unit_price]" value="{{ $sale->unit_price }}" min="0"
                                                       class="w-24 text-right border border-zinc-300 rounded px-2 py-1 text-xs font-mono text-zinc-900 input-price focus:outline-none focus:ring-1 focus:ring-zinc-900">
                                            </td>
                                            <td class="px-3 py-2 text-right font-mono font-bold text-zinc-900 cell-subtotal">
                                                Bs. {{ number_format($sale->subtotal, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="px-4 py-6 text-center text-zinc-600">No hay licores registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-zinc-100 border-t-2 border-zinc-300 font-bold text-xs text-zinc-900">
                                    <tr>
                                        <td colspan="7" class="px-4 py-2.5 text-right uppercase tracking-wider">Subtotal Combos:</td>
                                        <td class="px-3 py-2.5 text-center font-mono" id="tfoot-liquor-vendido">{{ $totalLiquorCombos }}</td>
                                        <td></td>
                                        <td class="px-3 py-2.5 text-right font-mono" id="tfoot-liquor-subtotal">Bs. {{ number_format($subtotalLiquors, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- 2. TABLA DE MIXERS / SODAS (EXTRAS) -->
                    <div class="bg-white border border-zinc-200 rounded overflow-hidden">
                        <div class="px-4 py-3 bg-zinc-50 border-b border-zinc-200 flex items-center justify-between">
                            <div>
                                <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">2. Mixers, Sodas & Aguas (Control de Extras)</h3>
                                <p class="text-[11px] text-zinc-600">
                                    Las sodas consumidas que pertenecen a los combos se descuentan automáticamente. Solo las adicionales (extras) se cobran.
                                </p>
                            </div>
                            <span class="text-xs font-mono font-semibold text-zinc-800" id="badge-mixer-subtotal">
                                Subtotal Extras: Bs. {{ number_format($subtotalMixers, 2) }}
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left" id="table-mixers">
                                <thead class="bg-zinc-100/70 border-b border-zinc-200 text-zinc-600 font-semibold uppercase tracking-wider">
                                    <tr>
                                        <th class="px-3 py-2.5 w-10 text-center">N°</th>
                                        <th class="px-3 py-2.5">Mixer / Soda</th>
                                        <th class="px-2 py-2.5 w-20 text-center">Paquete</th>
                                        <th class="px-2 py-2.5 w-20 text-center">Unidad</th>
                                        <th class="px-3 py-2.5 w-20 text-center">Total Inicial</th>
                                        <th class="px-2 py-2.5 w-20 text-center">Saldo</th>
                                        <th class="px-3 py-2.5 w-20 text-center">Consumidas</th>
                                        <th class="px-3 py-2.5 w-28 text-center text-zinc-600">En Combos</th>
                                        <th class="px-3 py-2.5 w-24 text-center font-bold text-zinc-900">Extras</th>
                                        <th class="px-3 py-2.5 w-28 text-right">Precio Extra (Bs.)</th>
                                        <th class="px-3 py-2.5 w-32 text-right">Total Extras (Bs.)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200">
                                    @forelse($mixerSales as $index => $sale)
                                        <tr class="hover:bg-zinc-50/50 mixer-row" 
                                            data-row-id="{{ $sale->id }}" 
                                            data-product-id="{{ $sale->product_id }}">
                                            <td class="px-3 py-2 text-center font-mono text-zinc-600">{{ $index + 1 }}</td>
                                            <td class="px-3 py-2 font-medium text-zinc-900">
                                                {{ $sale->product->name }}
                                            </td>
                                            <td class="px-2 py-1 text-center">
                                                <input type="number" name="sales[{{ $sale->id }}][packages]" value="{{ $sale->packages }}" min="0"
                                                       class="w-16 text-center border border-zinc-300 rounded px-2 py-1 text-xs font-mono text-zinc-900 input-packages focus:outline-none focus:ring-1 focus:ring-zinc-900">
                                            </td>
                                            <td class="px-2 py-1 text-center">
                                                <input type="number" name="sales[{{ $sale->id }}][units]" value="{{ $sale->units }}" min="0"
                                                       class="w-16 text-center border border-zinc-300 rounded px-2 py-1 text-xs font-mono text-zinc-900 input-units focus:outline-none focus:ring-1 focus:ring-zinc-900">
                                            </td>
                                            <td class="px-3 py-2 text-center font-mono font-semibold text-zinc-800 cell-total-initial">
                                                {{ $sale->total_initial }}
                                            </td>
                                            <td class="px-2 py-1 text-center">
                                                <input type="number" name="sales[{{ $sale->id }}][saldo]" value="{{ $sale->saldo }}" min="0"
                                                       class="w-16 text-center border border-zinc-300 rounded px-2 py-1 text-xs font-mono text-zinc-900 input-saldo focus:outline-none focus:ring-1 focus:ring-zinc-900">
                                            </td>
                                            <td class="px-3 py-2 text-center font-mono text-zinc-800 cell-consumido">
                                                {{ $sale->vendido }}
                                            </td>
                                            <td class="px-3 py-2 text-center font-mono text-zinc-600 cell-included">
                                                {{ $sale->included_in_combos ?? 0 }}
                                            </td>
                                            <td class="px-3 py-2 text-center font-mono font-bold text-zinc-900 cell-extras">
                                                {{ $sale->extras ?? 0 }}
                                            </td>
                                            <td class="px-2 py-1 text-right">
                                                <input type="number" step="0.5" name="sales[{{ $sale->id }}][unit_price]" value="{{ $sale->unit_price }}" min="0"
                                                       class="w-24 text-right border border-zinc-300 rounded px-2 py-1 text-xs font-mono text-zinc-900 input-price focus:outline-none focus:ring-1 focus:ring-zinc-900">
                                            </td>
                                            <td class="px-3 py-2 text-right font-mono font-bold text-zinc-900 cell-subtotal">
                                                Bs. {{ number_format($sale->subtotal, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="px-4 py-6 text-center text-zinc-600">No hay mixers registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-zinc-100 border-t-2 border-zinc-300 font-bold text-xs text-zinc-900">
                                    <tr>
                                        <td colspan="6" class="px-4 py-2.5 text-right uppercase tracking-wider">Totales Mixers:</td>
                                        <td class="px-3 py-2.5 text-center font-mono" id="tfoot-mixer-consumido">{{ $totalMixerConsumed }}</td>
                                        <td></td>
                                        <td class="px-3 py-2.5 text-center font-mono" id="tfoot-mixer-extras">{{ $totalMixerExtras }}</td>
                                        <td></td>
                                        <td class="px-3 py-2.5 text-right font-mono" id="tfoot-mixer-subtotal">Bs. {{ number_format($subtotalMixers, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Barra de Acciones y Guardado -->
                    <div class="p-4 bg-zinc-50 border border-zinc-200 rounded flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="text-xs text-zinc-700">
                            <span class="font-bold">Total Final de Bebidas en {{ $selectedBar }}:</span>
                            <span class="font-mono text-base font-bold text-zinc-900 ml-2" id="footer-grand-total">
                                Bs. {{ number_format($grandTotalBar, 2) }}
                            </span>
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-zinc-900 text-white text-xs font-semibold rounded hover:bg-zinc-800 transition-colors">
                            Guardar Cambios de {{ $selectedBar }}
                        </button>
                    </div>

                </div>
            </form>

            <!-- Script Reactivo para Combos, Deducción Automática con Ratio y Extras -->
            <script>
            document.addEventListener('DOMContentLoaded', function () {
                const liquorRows = document.querySelectorAll('.liquor-row');
                const mixerRows = document.querySelectorAll('.mixer-row');

                if (!liquorRows.length && !mixerRows.length) return;

                function recalculate() {
                    let combosPerMixer = {};
                    let totalLiquorCombos = 0;
                    let subtotalLiquors = 0;

                    // 1. Calcular Licores considerando el ratio de cada mixer (ej: 2 aguas tónicas por gin)
                    liquorRows.forEach(row => {
                        const pkg = parseInt(row.querySelector('.input-packages').value) || 0;
                        const units = parseInt(row.querySelector('.input-units').value) || 0;
                        const saldo = parseInt(row.querySelector('.input-saldo').value) || 0;
                        const price = parseFloat(row.querySelector('.input-price').value) || 0;
                        const mixerId = parseInt(row.getAttribute('data-mixer-id')) || 0;
                        const mixerRatio = parseInt(row.getAttribute('data-mixer-ratio')) || 1;

                        const totalInitial = pkg + units;
                        const vendido = Math.max(0, totalInitial - saldo);
                        const subtotal = vendido * price;

                        row.querySelector('.cell-total-initial').textContent = totalInitial;
                        row.querySelector('.cell-vendido').textContent = vendido;
                        row.querySelector('.cell-subtotal').textContent = 'Bs. ' + subtotal.toFixed(2);

                        totalLiquorCombos += vendido;
                        subtotalLiquors += subtotal;

                        if (mixerId > 0) {
                            combosPerMixer[mixerId] = (combosPerMixer[mixerId] || 0) + (vendido * mixerRatio);
                        }
                    });

                    // 2. Calcular Mixers (descontando los cubiertos por combos)
                    let totalMixerConsumed = 0;
                    let totalMixerExtras = 0;
                    let subtotalMixers = 0;

                    mixerRows.forEach(row => {
                        const pkg = parseInt(row.querySelector('.input-packages').value) || 0;
                        const units = parseInt(row.querySelector('.input-units').value) || 0;
                        const saldo = parseInt(row.querySelector('.input-saldo').value) || 0;
                        const price = parseFloat(row.querySelector('.input-price').value) || 0;
                        const productId = parseInt(row.getAttribute('data-product-id')) || 0;

                        const totalInitial = pkg + units;
                        const consumido = Math.max(0, totalInitial - saldo);
                        const included = combosPerMixer[productId] || 0;
                        const extras = Math.max(0, consumido - included);
                        const subtotal = extras * price;

                        row.querySelector('.cell-total-initial').textContent = totalInitial;
                        row.querySelector('.cell-consumido').textContent = consumido;
                        row.querySelector('.cell-included').textContent = included;
                        row.querySelector('.cell-extras').textContent = extras;
                        row.querySelector('.cell-subtotal').textContent = 'Bs. ' + subtotal.toFixed(2);

                        totalMixerConsumed += consumido;
                        totalMixerExtras += extras;
                        subtotalMixers += subtotal;
                    });

                    const grandTotal = subtotalLiquors + subtotalMixers;

                    // Actualizar Cabecera y Pies
                    document.getElementById('header-total-combos').textContent = totalLiquorCombos + ' combos';
                    document.getElementById('header-subtotal-combos').textContent = 'Subtotal: Bs. ' + subtotalLiquors.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    document.getElementById('badge-liquor-subtotal').textContent = 'Subtotal: Bs. ' + subtotalLiquors.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    document.getElementById('tfoot-liquor-vendido').textContent = totalLiquorCombos;
                    document.getElementById('tfoot-liquor-subtotal').textContent = 'Bs. ' + subtotalLiquors.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                    document.getElementById('header-total-extras').textContent = totalMixerExtras + ' extras';
                    document.getElementById('header-subtotal-extras').textContent = 'Subtotal: Bs. ' + subtotalMixers.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    document.getElementById('badge-mixer-subtotal').textContent = 'Subtotal Extras: Bs. ' + subtotalMixers.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    document.getElementById('tfoot-mixer-consumido').textContent = totalMixerConsumed;
                    document.getElementById('tfoot-mixer-extras').textContent = totalMixerExtras;
                    document.getElementById('tfoot-mixer-subtotal').textContent = 'Bs. ' + subtotalMixers.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                    document.getElementById('header-grand-total').textContent = 'Bs. ' + grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    document.getElementById('footer-grand-total').textContent = 'Bs. ' + grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }

                document.querySelectorAll('#sales-form input').forEach(input => {
                    input.addEventListener('input', recalculate);
                });
            });
            </script>
        @endif

    @endif

</div>
@endsection
