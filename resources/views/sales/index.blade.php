@extends('layouts.app')

@section('title', 'Ventas por Barra & Combos')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">

    <!-- ==========================================
         CABECERA DE LA VISTA (LIQUID GLASS)
         ========================================== -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-2 border-b border-white/10">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider bg-amber-400/10 text-amber-300 border border-amber-400/30">
                    Módulo 6 — Liquidación de Barras
                </span>
                <span class="text-zinc-600 text-xs">•</span>
                <span class="text-xs text-zinc-400 font-mono">Consumo de Botellas & Cálculo de Combos</span>
            </div>
            <h2 class="text-2xl font-black tracking-tight text-white">
                Ventas por Barra & Combos
            </h2>
            <p class="text-xs text-zinc-400 mt-1">
                Control de licores por combo con deducción automática de sodas y despacho directo de Tienda.
            </p>
        </div>

        <!-- Selector de Noche -->
        <div class="flex flex-wrap items-center gap-3">
            @if($allSessions->isNotEmpty())
                <form method="GET" action="{{ route('sales.index') }}" class="glass-panel p-2 rounded-2xl border border-white/10 flex items-center gap-2">
                    <input type="hidden" name="bar" value="{{ $selectedBar }}">
                    <label for="session_id" class="text-xs text-zinc-400 font-medium pl-1">Noche:</label>
                    <select name="session_id" id="session_id" onchange="this.form.submit()" 
                            class="glass-input text-xs font-mono font-bold rounded-xl px-2.5 py-1.5 text-zinc-100 cursor-pointer">
                        @foreach($allSessions as $s)
                            <option value="{{ $s->id }}" {{ $session && $session->id === $s->id ? 'selected' : '' }} class="bg-[#12141c] text-zinc-100">
                                {{ $s->day_name }} {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>
    </div>

    @if(!$session)
        <div class="glass-panel border border-white/10 rounded-3xl p-12 text-center shadow-2xl">
            <h3 class="text-base font-bold text-white">No hay noche seleccionada</h3>
            <p class="text-xs text-zinc-400 mt-1 mb-6">Crea una noche para registrar las ventas.</p>
            <a href="{{ route('sessions.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-400 to-amber-500 text-zinc-950 text-xs font-bold rounded-xl hover:brightness-110 transition-all shadow-md shadow-amber-500/20">
                + Crear Noche
            </a>
        </div>
    @else

        <!-- Selector de Puntos de Venta (Liquid Glass Pills: Barra Kelly, Barra Ariel, Tienda) -->
        <div class="flex items-center gap-2 glass-panel p-2 rounded-2xl border border-white/10 text-xs font-mono">
            @foreach($availableBars as $bar)
                <a href="{{ route('sales.index', ['session_id' => $session->id, 'bar' => $bar]) }}" 
                   class="px-5 py-2 rounded-xl font-bold transition-all {{ $selectedBar === $bar ? 'bg-amber-400 text-zinc-950 shadow-md shadow-amber-400/20' : 'text-zinc-300 hover:text-white hover:bg-white/5' }}">
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
                <div class="glass-card rounded-2xl p-6">
                    <span class="text-xs font-mono font-semibold text-zinc-400 uppercase tracking-wider">Combos Despachados</span>
                    <div class="mt-2 text-3xl font-black font-mono text-white tracking-tight">
                        {{ $totalStoreCombos }} <span class="text-xs font-sans text-zinc-400 font-normal">combos</span>
                    </div>
                    <p class="text-[11px] text-zinc-500 mt-1 font-mono">Salida directa de almacén</p>
                </div>

                <div class="glass-panel-elevated rounded-2xl p-6 border border-amber-400/30">
                    <span class="text-xs font-mono font-bold text-amber-400 uppercase tracking-wider">Total Recaudado Tienda</span>
                    <div class="mt-2 text-3xl font-black font-mono text-white tracking-tight">
                        <span class="text-amber-400 text-lg mr-0.5 font-sans">Bs.</span>{{ number_format($totalStoreRevenue, 2) }}
                    </div>
                    <p class="text-[11px] text-zinc-400 mt-1 font-mono">Efectivo + QR</p>
                </div>

                <div class="glass-card rounded-2xl p-6">
                    <span class="text-xs font-mono font-semibold text-zinc-400 uppercase tracking-wider">En Efectivo</span>
                    <div class="mt-2 text-3xl font-black font-mono text-emerald-400 tracking-tight">
                        <span class="text-lg mr-0.5 font-sans">Bs.</span>{{ number_format($totalStoreCash, 2) }}
                    </div>
                    <p class="text-[11px] text-zinc-500 mt-1 font-mono">Cobrado físicamente</p>
                </div>

                <div class="glass-card rounded-2xl p-6">
                    <span class="text-xs font-mono font-semibold text-zinc-400 uppercase tracking-wider">En QR</span>
                    <div class="mt-2 text-3xl font-black font-mono text-blue-400 tracking-tight">
                        <span class="text-lg mr-0.5 font-sans">Bs.</span>{{ number_format($totalStoreQr, 2) }}
                    </div>
                    <p class="text-[11px] text-zinc-500 mt-1 font-mono">Transferencias bancarias</p>
                </div>
            </div>

            <!-- Formulario de Despacho Rápido de Combo en Tienda -->
            <div class="glass-panel rounded-2xl p-6 shadow-2xl">
                <div class="border-b border-white/10 pb-3 mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-bold text-zinc-100 uppercase tracking-wider font-mono">
                            Despachar Combo en Tienda
                        </h3>
                        <p class="text-xs text-zinc-400 mt-0.5">
                            El mesero pide el combo, paga o muestra comprobante de QR, y la tienda despacha directo con desglose de pago.
                        </p>
                    </div>
                    <span class="text-[10px] px-2.5 py-1 rounded-full bg-white/10 text-zinc-300 font-mono border border-white/10">
                        Despacho Inmediato
                    </span>
                </div>

                <form method="POST" action="{{ route('sales.storeSales.store') }}" id="tienda-order-form" class="space-y-4">
                    @csrf
                    <input type="hidden" name="night_session_id" value="{{ $session->id }}">

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        
                        <!-- Producto / Combo -->
                        <div class="sm:col-span-2">
                            <label for="product_id" class="block text-xs font-medium text-zinc-300 mb-1">
                                Combo / Bebida Solicitada
                            </label>
                            <select name="product_id" id="product_id" required 
                                    class="glass-input w-full text-xs font-bold rounded-xl px-3 py-2.5 cursor-pointer">
                                <option value="" disabled selected class="bg-[#12141c]">-- Seleccionar Combo / Bebida --</option>
                                @foreach($storeProducts as $prod)
                                    <option value="{{ $prod->id }}" data-price="{{ $prod->sale_price }}" class="bg-[#12141c]">
                                        {{ $prod->name }} (Bs. {{ number_format($prod->sale_price, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Cantidad -->
                        <div>
                            <label for="quantity" class="block text-xs font-medium text-zinc-300 mb-1">
                                Cantidad
                            </label>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" required 
                                   class="glass-input w-full text-xs font-mono font-bold rounded-xl px-3 py-2.5">
                        </div>

                        <!-- Precio Unitario -->
                        <div>
                            <label for="unit_price" class="block text-xs font-medium text-zinc-300 mb-1">
                                Precio Unitario (Bs.)
                            </label>
                            <input type="number" step="0.5" name="unit_price" id="unit_price" value="0.00" min="0" required 
                                   class="glass-input w-full text-xs font-mono font-bold rounded-xl px-3 py-2.5">
                        </div>

                    </div>

                    <!-- Fila de Cobrante y Desglose de Pago -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 pt-3 border-t border-white/10 items-end">
                        
                        <!-- Nombre del Cobrante / Mesero -->
                        <div class="md:col-span-2">
                            <label for="cobrante_name" class="block text-xs font-medium text-zinc-300 mb-1">
                                Nombre del Mesero / Cobrante
                            </label>
                            <input type="text" name="cobrante_name" id="cobrante_name" required placeholder="Ej. Juan, Mau, Ari..."
                                   class="glass-input w-full text-xs uppercase rounded-xl px-3 py-2.5 placeholder-zinc-500">
                        </div>

                        <!-- Total Calculado -->
                        <div>
                            <label class="block text-xs font-medium text-zinc-300 mb-1">
                                Total Pedido (Bs.)
                            </label>
                            <div class="px-3 py-2.5 glass-panel rounded-xl text-amber-400 font-mono font-black text-sm" id="tienda-total-display">
                                Bs. 0.00
                            </div>
                        </div>

                        <!-- Monto en Efectivo -->
                        <div>
                            <label for="cash_amount" class="block text-xs font-medium text-zinc-300 mb-1">
                                Monto Efectivo (Bs.)
                            </label>
                            <input type="number" step="0.5" name="cash_amount" id="cash_amount" value="0.00" min="0" required 
                                   class="glass-input w-full text-xs font-mono font-bold rounded-xl px-3 py-2.5 text-emerald-400">
                        </div>

                        <!-- Monto en QR -->
                        <div>
                            <label for="qr_amount" class="block text-xs font-medium text-zinc-300 mb-1">
                                Monto QR (Bs.)
                            </label>
                            <input type="number" step="0.5" name="qr_amount" id="qr_amount" value="0.00" min="0" required 
                                   class="glass-input w-full text-xs font-mono font-bold rounded-xl px-3 py-2.5 text-blue-400">
                        </div>

                    </div>

                    <!-- Botones de ayuda rápida -->
                    <div class="flex flex-wrap items-center justify-between gap-3 pt-2 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="text-zinc-400 font-medium text-[11px]">Llenado Rápido:</span>
                            <button type="button" id="btn-all-cash" class="px-3 py-1.5 glass-card rounded-lg text-xs font-bold text-emerald-400 cursor-pointer">
                                Todo Efectivo
                            </button>
                            <button type="button" id="btn-all-qr" class="px-3 py-1.5 glass-card rounded-lg text-xs font-bold text-blue-400 cursor-pointer">
                                Todo QR
                            </button>
                            <button type="button" id="btn-split-50" class="px-3 py-1.5 glass-card rounded-lg text-xs font-bold text-amber-300 cursor-pointer">
                                50% Efectivo / 50% QR
                            </button>
                        </div>

                        <!-- Opciones de QR -->
                        <div class="flex items-center gap-4" id="qr-options-box">
                            <div class="flex items-center gap-1.5">
                                <label for="bank_app" class="text-[11px] font-semibold text-zinc-300">Banco QR:</label>
                                <select name="bank_app" id="bank_app" 
                                        class="glass-input text-xs rounded-lg px-2.5 py-1 text-zinc-100 cursor-pointer">
                                    <option value="YASTA" class="bg-[#12141c]">YASTA (Unión)</option>
                                    <option value="YAPE" class="bg-[#12141c]">YAPE (BCP)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-amber-400 to-amber-500 text-zinc-950 font-bold text-xs rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-amber-500/20 cursor-pointer">
                            + Despachar y Registrar Venta
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabla de Despachos Registrados en Tienda -->
            <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl">
                <div class="px-6 py-4 border-b border-white/10 bg-white/[0.02] flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-200 font-mono">
                        Historial de Combos Despachados en Tienda ({{ $storeSales->count() }})
                    </h3>
                    <span class="text-xs font-mono font-bold text-amber-400">
                        Total Tienda: Bs. {{ number_format($totalStoreRevenue, 2) }}
                    </span>
                </div>

                <div class="overflow-x-auto max-h-[500px]">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-white/[0.04] border-b border-white/10 text-zinc-400 font-mono uppercase text-[10px] tracking-wider sticky top-0">
                            <tr>
                                <th class="px-4 py-3">Hora</th>
                                <th class="px-4 py-3">Combo / Producto</th>
                                <th class="px-3 py-3 text-center">Cant.</th>
                                <th class="px-4 py-3 text-right">Unitario</th>
                                <th class="px-4 py-3 text-right">Total (Bs.)</th>
                                <th class="px-4 py-3 text-right">Efectivo</th>
                                <th class="px-4 py-3 text-right">QR</th>
                                <th class="px-4 py-3">Cobrante</th>
                                <th class="px-3 py-3 text-right w-16">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 font-mono">
                            @forelse($storeSales as $ss)
                                <tr class="hover:bg-white/[0.02]">
                                    <td class="px-4 py-2.5 text-zinc-400">{{ \Carbon\Carbon::parse($ss->created_at)->format('H:i') }}</td>
                                    <td class="px-4 py-2.5 font-bold text-zinc-100 font-sans">{{ $ss->product->name }}</td>
                                    <td class="px-3 py-2.5 text-center font-bold text-amber-400">{{ $ss->quantity }}</td>
                                    <td class="px-4 py-2.5 text-right text-zinc-300">Bs. {{ number_format($ss->unit_price, 2) }}</td>
                                    <td class="px-4 py-2.5 text-right font-black text-white">Bs. {{ number_format($ss->total_price, 2) }}</td>
                                    <td class="px-4 py-2.5 text-right text-emerald-400 font-bold">Bs. {{ number_format($ss->cash_amount, 2) }}</td>
                                    <td class="px-4 py-2.5 text-right text-blue-400 font-bold">Bs. {{ number_format($ss->qr_amount, 2) }}</td>
                                    <td class="px-4 py-2.5 text-zinc-300 font-sans uppercase font-medium">{{ $ss->cobrante_name }}</td>
                                    <td class="px-3 py-2.5 text-right">
                                        <form method="POST" action="{{ route('sales.storeSales.destroy', $ss) }}" onsubmit="return confirm('¿Eliminar despacho?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-400 hover:text-rose-300 font-bold text-xs cursor-pointer">Borrar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-zinc-500 font-sans">
                                        No hay despachos registrados en Tienda en esta noche.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Scripts de Cálculo para Tienda -->
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
                <div class="glass-card rounded-2xl p-6">
                    <span class="text-xs font-mono font-semibold text-zinc-400 uppercase tracking-wider">Combos Vendidos (Licores)</span>
                    <div class="mt-2 text-3xl font-black font-mono text-white" id="header-total-combos">
                        {{ $totalLiquorCombos }} <span class="text-xs font-sans text-zinc-400 font-normal">combos</span>
                    </div>
                    <p class="text-xs text-amber-400 mt-1 font-mono font-bold" id="header-subtotal-combos">
                        Subtotal: Bs. {{ number_format($subtotalLiquors, 2) }}
                    </p>
                </div>

                <div class="glass-card rounded-2xl p-6">
                    <span class="text-xs font-mono font-semibold text-zinc-400 uppercase tracking-wider">Sodas / Mixers Extras</span>
                    <div class="mt-2 text-3xl font-black font-mono text-white" id="header-total-extras">
                        {{ $totalMixerExtras }} <span class="text-xs font-sans text-zinc-400 font-normal">extras</span>
                    </div>
                    <p class="text-xs text-amber-400 mt-1 font-mono font-bold" id="header-subtotal-extras">
                        Subtotal: Bs. {{ number_format($subtotalMixers, 2) }}
                    </p>
                </div>

                <div class="glass-panel-elevated rounded-2xl p-6 border border-amber-400/30">
                    <span class="text-xs font-mono font-bold text-amber-400 uppercase tracking-wider">Total Bebidas en {{ $selectedBar }}</span>
                    <div class="mt-2 text-3xl font-black font-mono text-white" id="header-grand-total">
                        <span class="text-amber-400 text-lg mr-0.5 font-sans">Bs.</span>{{ number_format($grandTotalBar, 2) }}
                    </div>
                    <p class="text-xs text-zinc-400 mt-1 font-mono">Combos + Sodas adicionales</p>
                </div>
            </div>

            <!-- Formulario Tabular de Inventario y Ventas para Barras -->
            <form method="POST" action="{{ route('sales.updateBulk') }}" id="sales-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="night_session_id" value="{{ $session->id }}">
                <input type="hidden" name="bar_name" value="{{ $selectedBar }}">

                <div class="space-y-6">

                <!-- Barra de acciones: Botón Agregar Especial -->
                <div class="flex justify-end mb-3">
                    <button type="button" onclick="document.getElementById('modal-add-special').classList.remove('hidden')" 
                            class="px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/10 rounded-xl text-xs font-bold text-amber-300 flex items-center gap-2 cursor-pointer transition-all">
                        <span>+ Nueva Variante / Soda por Categoría</span>
                    </button>
                </div>

                <!-- Modal Registrar Variante / Especial en Catálogo -->
                <div id="modal-add-special" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
                    <div class="glass-panel p-6 rounded-2xl border border-white/20 max-w-md w-full shadow-2xl">
                        <h3 class="text-sm font-black text-white uppercase tracking-wider mb-2">Registrar Variante Especial</h3>
                        <p class="text-xs text-zinc-400 mb-4">Define una soda/mixer especial para una categoría de tragos (ej: Sprite o Aquarius para Rones/Singanis).</p>
                        
                        <form method="POST" action="{{ route('sales.specialMixers.store') }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-medium text-zinc-300 mb-1">Categoría del Trago</label>
                                <select name="category" required class="glass-input w-full text-xs rounded-xl px-3 py-2">
                                    <option value="" disabled selected class="bg-[#12141c]">-- Seleccionar Categoría --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" class="bg-[#12141c]">{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-zinc-300 mb-1">Nombre de la Variante / Soda</label>
                                <input type="text" name="mixer_name" placeholder="Ej: Sprite 2.0L, Aquarius Pera" required 
                                    class="glass-input w-full text-xs rounded-xl px-3 py-2 text-white">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-zinc-300 mb-1">Producto Mixer para Descuento en Barra</label>
                                <select name="product_id" required class="glass-input w-full text-xs rounded-xl px-3 py-2">
                                    <option value="" disabled selected class="bg-[#12141c]">-- Seleccionar Soda / Mixer en Almacén --</option>
                                    @foreach($allMixerProducts as $mix)
                                        <option value="{{ $mix->id }}" class="bg-[#12141c]">{{ $mix->name }} (Bs. {{ number_format($mix->sale_price, 2) }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex justify-end gap-2 pt-3">
                                <button type="button" onclick="document.getElementById('modal-add-special').classList.add('hidden')" 
                                        class="px-4 py-2 glass-card rounded-xl text-xs text-zinc-400 font-bold">Cancelar</button>
                                <button type="submit" class="px-5 py-2 bg-amber-400 text-zinc-950 font-bold text-xs rounded-xl hover:brightness-110">Guardar en Catálogo</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal Asignar Mixers Especiales a la Fila de Venta -->
                <div id="modal-assign-special" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
                    <div class="glass-panel p-6 rounded-2xl border border-white/20 max-w-lg w-full shadow-2xl space-y-4">
                        <div class="flex items-center justify-between border-b border-white/10 pb-3">
                            <div>
                                <span class="text-[10px] font-mono uppercase tracking-wider text-amber-400 font-bold">Combos Especiales</span>
                                <h3 class="text-base font-black text-white" id="modal-liquor-title">Licor</h3>
                            </div>
                            <button type="button" onclick="closeSpecialModal()" class="text-zinc-400 hover:text-white text-lg font-bold">✕</button>
                        </div>

                        <div class="bg-white/5 rounded-xl p-3 border border-white/5 flex items-center justify-between text-xs font-mono">
                            <div>
                                <span class="text-zinc-400">Total Combos Vendidos:</span>
                                <span class="font-bold text-white ml-1 text-sm" id="modal-total-combos">0</span>
                            </div>
                            <div>
                                <span class="text-zinc-400">Mixer por Defecto:</span>
                                <span class="font-bold text-amber-300 ml-1" id="modal-default-mixer-name">-</span>
                            </div>
                        </div>

                        <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
                            <p class="text-[11px] text-zinc-400">
                                Asigna cuántos combos se despacharon con un mixer alternativo (se restarán automáticamente del mixer por defecto):
                            </p>
                            
                            <div class="space-y-2">
                                @foreach($allMixerProducts as $mixer)
                                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-white/[0.03] border border-white/5 hover:bg-white/[0.06] transition-all">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-amber-400/60"></span>
                                            <span class="text-xs font-medium text-zinc-200">{{ $mixer->name }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <label class="text-[11px] text-zinc-400 font-mono">Combos:</label>
                                            <input type="number" min="0" value="0" 
                                                   data-mixer-id="{{ $mixer->id }}" 
                                                   data-mixer-name="{{ $mixer->name }}"
                                                   class="modal-special-qty glass-input w-16 text-center rounded-lg px-2 py-1 text-xs font-mono font-bold text-white">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="p-3 bg-amber-400/10 border border-amber-400/20 rounded-xl flex items-center justify-between text-xs font-mono">
                            <span class="text-zinc-300">Mixers por defecto restantes:</span>
                            <span class="font-bold text-amber-400 text-sm" id="modal-remaining-default">0</span>
                        </div>

                        <div class="flex justify-end gap-2 pt-2 border-t border-white/10">
                            <button type="button" onclick="closeSpecialModal()" class="px-4 py-2 glass-card rounded-xl text-xs text-zinc-400 font-bold">Cancelar</button>
                            <button type="button" onclick="saveSpecialModal()" class="px-6 py-2 bg-amber-400 text-zinc-950 font-bold text-xs rounded-xl hover:brightness-110 shadow-lg shadow-amber-500/20">Listo / Aplicar</button>
                        </div>
                    </div>
                </div>

                    <!-- 1. TABLA DE LICORES (COMBOS) -->
                    <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl">
                        <div class="px-6 py-4 bg-white/[0.02] border-b border-white/10 flex items-center justify-between">
                            <div>
                                <h3 class="text-xs font-bold text-zinc-100 uppercase tracking-wider font-mono">1. Licores & Combos</h3>
                                <p class="text-[11px] text-zinc-400">
                                    Apertura y reposiciones sincronizadas desde <a href="{{ route('barInventory.index', ['session_id' => $session->id, 'bar' => $selectedBar]) }}" class="text-amber-400 font-bold underline hover:text-amber-300">Inventario de Barras</a>. Cada combo incluye su soda (Gins incluyen <strong>2 Aguas Tónicas</strong>). Puedes cambiar acompañamiento con el botón <strong>+ Especial</strong>.
                                </p>
                            </div>
                            <span class="text-xs font-mono font-bold text-amber-400" id="badge-liquor-subtotal">
                                Subtotal: Bs. {{ number_format($subtotalLiquors, 2) }}
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left" id="table-liquors">
                                <thead class="bg-white/[0.04] border-b border-white/10 text-zinc-400 font-mono uppercase text-[10px] tracking-wider">
                                    <tr>
                                        <th class="px-4 py-3 w-10 text-center">N°</th>
                                        <th class="px-4 py-3">Licor</th>
                                        <th class="px-4 py-3">Mixer Incluido</th>
                                        <th class="px-3 py-3 text-center">Especiales</th>
                                        <th class="px-2 py-3 w-20 text-center">Paquete</th>
                                        <th class="px-2 py-3 w-20 text-center">Unidad</th>
                                        <th class="px-4 py-3 w-20 text-center">Total Inicial</th>
                                        <th class="px-2 py-3 w-20 text-center">Saldo</th>
                                        <th class="px-4 py-3 w-24 text-center font-bold text-amber-400">Combos Vendidos</th>
                                        <th class="px-4 py-3 w-28 text-right">Precio Combo</th>
                                        <th class="px-4 py-3 w-32 text-right">Subtotal (Bs.)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5 font-mono">
                                    @forelse($liquorSales as $index => $sale)
                                        @php
                                            $mixerInfo = $mapping[$sale->product_id] ?? null;
                                            $mixerId = $mixerInfo['mixer_id'] ?? 0;
                                            $mixerRatio = $mixerInfo['ratio'] ?? 1;
                                            $mixerName = 'Solo';
                                            if ($mixerId == 23) $mixerName = '1 Ginger Ale 2L';
                                            elseif ($mixerId == 24) $mixerName = '1 Coca Cola 2L';
                                            elseif ($mixerId == 25) $mixerName = '1 Agua Vital 2L';
                                            elseif ($mixerId == 26) $mixerName = '2 Aguas Tónicas 1L';

                                            $specialsJson = is_array($sale->selected_special_mixer) ? json_encode($sale->selected_special_mixer) : ($sale->selected_special_mixer ?? '{}');
                                            $specialsDecoded = [];
                                            if (!empty($sale->selected_special_mixer)) {
                                                $decoded = is_string($sale->selected_special_mixer) ? json_decode($sale->selected_special_mixer, true) : $sale->selected_special_mixer;
                                                if (is_array($decoded)) $specialsDecoded = $decoded;
                                            }

                                            $badgeParts = [];
                                            foreach ($specialsDecoded as $mId => $q) {
                                                if ($q > 0) {
                                                    $mObj = $allMixerProducts->firstWhere('id', $mId);
                                                    $shortName = $mObj ? explode(' ', $mObj->name)[0] : 'Mixer';
                                                    $badgeParts[] = "$shortName ($q)";
                                                }
                                            }
                                            $badgeText = count($badgeParts) > 0 ? implode(', ', $badgeParts) : '+ Especial';
                                            $hasSpecials = count($badgeParts) > 0;
                                        @endphp
                                        <tr class="hover:bg-white/[0.02] liquor-row" 
                                            data-row-id="{{ $sale->id }}" 
                                            data-product-id="{{ $sale->product_id }}"
                                            data-product-name="{{ $sale->product->name }}"
                                            data-units-per-pkg="{{ $sale->product->units_per_package ?? 1 }}"
                                            data-mixer-id="{{ $mixerId }}"
                                            data-mixer-name="{{ $mixerName }}"
                                            data-mixer-ratio="{{ $mixerRatio }}">
                                            <td class="px-4 py-2.5 text-center text-zinc-500 font-bold">{{ $index + 1 }}</td>
                                            <td class="px-4 py-2.5 font-bold text-white font-sans">
                                                {{ $sale->product->name }}
                                            </td>
                                            <td class="px-4 py-2.5 text-zinc-400 text-[11px]">
                                                <span class="inline-flex px-2 py-0.5 rounded-full {{ $mixerRatio == 2 ? 'bg-amber-400/10 text-amber-300 border border-amber-400/30 font-bold' : 'bg-white/5 text-zinc-300' }}">
                                                    + {{ $mixerName }}
                                                </span>
                                            </td>
                                            
                                            <!-- BOTÓN INTERACTIVO DE MIXER ESPECIAL -->
                                            <td class="px-3 py-1.5 text-center">
                                                <input type="hidden" 
                                                       name="sales[{{ $sale->id }}][selected_special_mixer]" 
                                                       id="input-specials-{{ $sale->id }}" 
                                                       value="{{ $specialsJson }}" 
                                                       class="input-special-mixers">
                                                <button type="button" 
                                                        onclick="openSpecialModal({{ $sale->id }})" 
                                                        id="btn-special-{{ $sale->id }}"
                                                        class="px-2.5 py-1 rounded-lg text-[11px] font-mono font-bold transition-all inline-flex items-center gap-1.5 cursor-pointer border {{ $hasSpecials ? 'bg-amber-400/20 text-amber-300 border-amber-400/40 shadow-sm' : 'bg-white/5 hover:bg-white/10 text-zinc-400 border-white/10 hover:text-white' }}">
                                                    <span class="text-amber-400 font-black">+</span>
                                                    <span id="badge-special-{{ $sale->id }}">{{ $badgeText }}</span>
                                                </button>
                                            </td>

                                            <td class="px-2 py-1.5 text-center">
                                                <input type="number" name="sales[{{ $sale->id }}][packages]" value="{{ $sale->packages }}" min="0"
                                                       class="glass-input w-16 text-center rounded-lg px-2 py-1 text-xs font-mono font-bold text-white input-packages">
                                            </td>
                                            <td class="px-2 py-1.5 text-center">
                                                <input type="number" name="sales[{{ $sale->id }}][units]" value="{{ $sale->units }}" min="0"
                                                       class="glass-input w-16 text-center rounded-lg px-2 py-1 text-xs font-mono font-bold text-white input-units">
                                            </td>
                                            <td class="px-4 py-2.5 text-center font-bold text-zinc-200 cell-total-initial">
                                                {{ $sale->total_initial }}
                                            </td>
                                            <td class="px-2 py-1.5 text-center">
                                                <input type="number" name="sales[{{ $sale->id }}][saldo]" value="{{ $sale->saldo }}" min="0"
                                                       class="glass-input w-16 text-center rounded-lg px-2 py-1 text-xs font-mono font-bold text-amber-300 input-saldo">
                                            </td>
                                            <td class="px-4 py-2.5 text-center font-black text-amber-400 text-sm cell-vendido">
                                                {{ $sale->vendido }}
                                            </td>
                                            <td class="px-2 py-1.5 text-right">
                                                <input type="number" step="0.5" name="sales[{{ $sale->id }}][unit_price]" value="{{ $sale->unit_price }}" min="0"
                                                       class="glass-input w-24 text-right rounded-lg px-2 py-1 text-xs font-mono font-bold text-zinc-200 input-price">
                                            </td>
                                            <td class="px-4 py-2.5 text-right font-black text-white cell-subtotal">
                                                Bs. {{ number_format($sale->subtotal, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="px-4 py-8 text-center text-zinc-500 font-sans">No hay licores registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-white/[0.03] border-t border-white/10 font-bold text-xs text-white">
                                    <tr>
                                        <td colspan="8" class="px-4 py-3 text-right uppercase tracking-wider font-mono">Subtotal Combos:</td>
                                        <td class="px-4 py-3 text-center font-mono font-black text-amber-400 text-sm" id="tfoot-liquor-vendido">{{ $totalLiquorCombos }}</td>
                                        <td></td>
                                        <td class="px-4 py-3 text-right font-mono font-black text-amber-400 text-sm" id="tfoot-liquor-subtotal">Bs. {{ number_format($subtotalLiquors, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- 2. TABLA DE MIXERS / SODAS (EXTRAS) -->
                    <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl">
                        <div class="px-6 py-4 bg-white/[0.02] border-b border-white/10 flex items-center justify-between">
                            <div>
                                <h3 class="text-xs font-bold text-zinc-100 uppercase tracking-wider font-mono">2. Mixers, Sodas & Aguas (Control de Extras)</h3>
                                <p class="text-[11px] text-zinc-400">
                                    Las sodas consumidas que pertenecen a los combos se descuentan automáticamente (incluyendo variantes especiales). Solo las adicionales (extras) se cobran.
                                </p>
                            </div>
                            <span class="text-xs font-mono font-bold text-amber-400" id="badge-mixer-subtotal">
                                Subtotal Extras: Bs. {{ number_format($subtotalMixers, 2) }}
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left" id="table-mixers">
                                <thead class="bg-white/[0.04] border-b border-white/10 text-zinc-400 font-mono uppercase text-[10px] tracking-wider">
                                    <tr>
                                        <th class="px-4 py-3 w-10 text-center">N°</th>
                                        <th class="px-4 py-3">Mixer / Soda</th>
                                        <th class="px-2 py-3 w-20 text-center">Paquete</th>
                                        <th class="px-2 py-3 w-20 text-center">Unidad</th>
                                        <th class="px-4 py-3 w-20 text-center">Total Inicial</th>
                                        <th class="px-2 py-3 w-20 text-center">Saldo</th>
                                        <th class="px-4 py-3 w-20 text-center">Consumidas</th>
                                        <th class="px-4 py-3 w-28 text-center text-zinc-400">En Combos</th>
                                        <th class="px-4 py-3 w-24 text-center font-bold text-amber-400">Extras</th>
                                        <th class="px-4 py-3 w-28 text-right">Precio Extra</th>
                                        <th class="px-4 py-3 w-32 text-right">Total Extras (Bs.)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5 font-mono">
                                    @forelse($mixerSales as $index => $sale)
                                        <tr class="hover:bg-white/[0.02] mixer-row" 
                                            data-row-id="{{ $sale->id }}" 
                                            data-units-per-pkg="{{ $sale->product->units_per_package ?? 1 }}"
                                            data-product-id="{{ $sale->product_id }}">
                                            <td class="px-4 py-2.5 text-center text-zinc-500 font-bold">{{ $index + 1 }}</td>
                                            <td class="px-4 py-2.5 font-bold text-white font-sans">
                                                {{ $sale->product->name }}
                                            </td>
                                            <td class="px-2 py-1.5 text-center">
                                                <input type="number" name="sales[{{ $sale->id }}][packages]" value="{{ $sale->packages }}" min="0"
                                                       class="glass-input w-16 text-center rounded-lg px-2 py-1 text-xs font-mono font-bold text-white input-packages">
                                            </td>
                                            <td class="px-2 py-1.5 text-center">
                                                <input type="number" name="sales[{{ $sale->id }}][units]" value="{{ $sale->units }}" min="0"
                                                       class="glass-input w-16 text-center rounded-lg px-2 py-1 text-xs font-mono font-bold text-white input-units">
                                            </td>
                                            <td class="px-4 py-2.5 text-center font-bold text-zinc-200 cell-total-initial">
                                                {{ $sale->total_initial }}
                                            </td>
                                            <td class="px-2 py-1.5 text-center">
                                                <input type="number" name="sales[{{ $sale->id }}][saldo]" value="{{ $sale->saldo }}" min="0"
                                                       class="glass-input w-16 text-center rounded-lg px-2 py-1 text-xs font-mono font-bold text-amber-300 input-saldo">
                                            </td>
                                            <td class="px-4 py-2.5 text-center text-zinc-200 cell-consumido">
                                                {{ $sale->vendido }}
                                            </td>
                                            <td class="px-4 py-2.5 text-center text-zinc-400 cell-included">
                                                {{ $sale->included_in_combos ?? 0 }}
                                            </td>
                                            <td class="px-4 py-2.5 text-center font-black text-amber-400 cell-extras">
                                                {{ $sale->extras ?? 0 }}
                                            </td>
                                            <td class="px-2 py-1.5 text-right">
                                                <input type="number" step="0.5" name="sales[{{ $sale->id }}][unit_price]" value="{{ $sale->unit_price }}" min="0"
                                                       class="glass-input w-24 text-right rounded-lg px-2 py-1 text-xs font-mono font-bold text-zinc-200 input-price">
                                            </td>
                                            <td class="px-4 py-2.5 text-right font-black text-white cell-subtotal">
                                                Bs. {{ number_format($sale->subtotal, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="px-4 py-8 text-center text-zinc-500 font-sans">No hay mixers registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-white/[0.03] border-t border-white/10 font-bold text-xs text-white">
                                    <tr>
                                        <td colspan="6" class="px-4 py-3 text-right uppercase tracking-wider font-mono">Totales Mixers:</td>
                                        <td class="px-4 py-3 text-center font-mono font-bold" id="tfoot-mixer-consumido">{{ $totalMixerConsumed }}</td>
                                        <td></td>
                                        <td class="px-4 py-3 text-center font-mono font-black text-amber-400" id="tfoot-mixer-extras">{{ $totalMixerExtras }}</td>
                                        <td></td>
                                        <td class="px-4 py-3 text-right font-mono font-black text-amber-400" id="tfoot-mixer-subtotal">Bs. {{ number_format($subtotalMixers, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Barra de Acciones y Guardado en Liquid Glass -->
                    <div class="p-6 glass-panel-elevated rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4 border border-amber-400/30">
                        <div class="text-sm font-mono text-zinc-300">
                            <span class="font-bold font-sans">Total Bebidas Liquidado en {{ $selectedBar }}:</span>
                            <span class="text-2xl font-black text-amber-400 ml-2" id="footer-grand-total">
                                Bs. {{ number_format($grandTotalBar, 2) }}
                            </span>
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-amber-400 to-amber-500 text-zinc-950 text-xs font-black rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-xl shadow-amber-500/25 cursor-pointer uppercase tracking-wider">
                            Guardar Cambios de {{ $selectedBar }}
                        </button>
                    </div>

                </div>
            </form>

            <!-- Script Reactivo para Combos, Deducción Automática y Mixers Especiales -->
            <script>
            let currentActiveRowId = null;

            function openSpecialModal(rowId) {
                currentActiveRowId = rowId;
                const row = document.querySelector(`.liquor-row[data-row-id="${rowId}"]`);
                if (!row) return;

                const liquorName = row.getAttribute('data-product-name');
                const defaultMixerName = row.getAttribute('data-mixer-name');
                const unitsPerPkg = parseInt(row.getAttribute('data-units-per-pkg')) || 1;
                const pkg = parseInt(row.querySelector('.input-packages').value) || 0;
                const units = parseInt(row.querySelector('.input-units').value) || 0;
                const saldo = parseInt(row.querySelector('.input-saldo').value) || 0;
                const totalCombos = Math.max(0, (pkg * unitsPerPkg + units) - saldo);

                document.getElementById('modal-liquor-title').textContent = liquorName;
                document.getElementById('modal-total-combos').textContent = totalCombos;
                document.getElementById('modal-default-mixer-name').textContent = defaultMixerName;

                // Cargar valores actuales del input JSON
                const inputJson = document.getElementById(`input-specials-${rowId}`);
                let currentSpecials = {};
                try {
                    currentSpecials = JSON.parse(inputJson.value || '{}');
                } catch(e) {
                    currentSpecials = {};
                }

                // Llenar inputs del modal
                document.querySelectorAll('.modal-special-qty').forEach(inp => {
                    const mId = inp.getAttribute('data-mixer-id');
                    inp.value = currentSpecials[mId] || 0;
                });

                updateModalRemaining(totalCombos);
                document.getElementById('modal-assign-special').classList.remove('hidden');
            }

            function updateModalRemaining(totalCombos) {
                let specialSum = 0;
                document.querySelectorAll('.modal-special-qty').forEach(inp => {
                    specialSum += parseInt(inp.value) || 0;
                });
                const remaining = Math.max(0, totalCombos - specialSum);
                const remEl = document.getElementById('modal-remaining-default');
                remEl.textContent = remaining;
                if (specialSum > totalCombos) {
                    remEl.className = 'font-bold text-rose-400 text-sm';
                    remEl.textContent = `${remaining} (¡Excede los combos!)`;
                } else {
                    remEl.className = 'font-bold text-amber-400 text-sm';
                }
            }

            document.querySelectorAll('.modal-special-qty').forEach(inp => {
                inp.addEventListener('input', function() {
                    const totalCombos = parseInt(document.getElementById('modal-total-combos').textContent) || 0;
                    updateModalRemaining(totalCombos);
                });
            });

            function closeSpecialModal() {
                document.getElementById('modal-assign-special').classList.add('hidden');
                currentActiveRowId = null;
            }

            function saveSpecialModal() {
                if (!currentActiveRowId) return;

                const rowId = currentActiveRowId;
                const inputJson = document.getElementById(`input-specials-${rowId}`);
                const badgeEl = document.getElementById(`badge-special-${rowId}`);
                const btnEl = document.getElementById(`btn-special-${rowId}`);

                let specialsObj = {};
                let badgeParts = [];

                document.querySelectorAll('.modal-special-qty').forEach(inp => {
                    const qty = parseInt(inp.value) || 0;
                    const mId = inp.getAttribute('data-mixer-id');
                    const mName = inp.getAttribute('data-mixer-name');
                    if (qty > 0) {
                        specialsObj[mId] = qty;
                        const shortName = mName.split(' ')[0];
                        badgeParts.push(`${shortName} (${qty})`);
                    }
                });

                inputJson.value = JSON.stringify(specialsObj);

                if (badgeParts.length > 0) {
                    badgeEl.textContent = badgeParts.join(', ');
                    btnEl.className = 'px-2.5 py-1 rounded-lg text-[11px] font-mono font-bold transition-all inline-flex items-center gap-1.5 cursor-pointer border bg-amber-400/20 text-amber-300 border-amber-400/40 shadow-sm';
                } else {
                    badgeEl.textContent = '+ Especial';
                    btnEl.className = 'px-2.5 py-1 rounded-lg text-[11px] font-mono font-bold transition-all inline-flex items-center gap-1.5 cursor-pointer border bg-white/5 hover:bg-white/10 text-zinc-400 border-white/10 hover:text-white';
                }

                closeSpecialModal();
                window.recalculateSales();
            }

            document.addEventListener('DOMContentLoaded', function () {
                const liquorRows = document.querySelectorAll('.liquor-row');
                const mixerRows = document.querySelectorAll('.mixer-row');

                if (!liquorRows.length && !mixerRows.length) return;

                window.recalculateSales = function() {
                    let combosPerMixer = {};
                    let totalLiquorCombos = 0;
                    let subtotalLiquors = 0;

                    liquorRows.forEach(row => {
                        const rowId = row.getAttribute('data-row-id');
                        const unitsPerPkg = parseInt(row.getAttribute('data-units-per-pkg')) || 1;
                        const pkg = parseInt(row.querySelector('.input-packages').value) || 0;
                        const units = parseInt(row.querySelector('.input-units').value) || 0;
                        const saldo = parseInt(row.querySelector('.input-saldo').value) || 0;
                        const price = parseFloat(row.querySelector('.input-price').value) || 0;
                        const defaultMixerId = parseInt(row.getAttribute('data-mixer-id')) || 0;
                        const mixerRatio = parseInt(row.getAttribute('data-mixer-ratio')) || 1;

                        const totalInitial = (pkg * unitsPerPkg) + units;
                        const vendido = Math.max(0, totalInitial - saldo);
                        const subtotal = vendido * price;

                        row.querySelector('.cell-total-initial').textContent = totalInitial;
                        row.querySelector('.cell-vendido').textContent = vendido;
                        row.querySelector('.cell-subtotal').textContent = 'Bs. ' + subtotal.toFixed(2);

                        totalLiquorCombos += vendido;
                        subtotalLiquors += subtotal;

                        // Parsear especiales asignados a esta fila
                        const specialInput = document.getElementById(`input-specials-${rowId}`);
                        let specials = {};
                        try {
                            specials = JSON.parse(specialInput.value || '{}');
                        } catch(e) {
                            specials = {};
                        }

                        let specialCount = 0;
                        for (const [mId, qty] of Object.entries(specials)) {
                            const q = parseInt(qty) || 0;
                            if (q > 0) {
                                combosPerMixer[mId] = (combosPerMixer[mId] || 0) + q;
                                specialCount += q;
                            }
                        }

                        // El mixer por defecto recibe el remanente
                        if (defaultMixerId > 0) {
                            const remaining = Math.max(0, vendido - specialCount);
                            combosPerMixer[defaultMixerId] = (combosPerMixer[defaultMixerId] || 0) + (remaining * mixerRatio);
                        }
                    });

                    let totalMixerConsumed = 0;
                    let totalMixerExtras = 0;
                    let subtotalMixers = 0;

                    mixerRows.forEach(row => {
                        const unitsPerPkg = parseInt(row.getAttribute('data-units-per-pkg')) || 1;
                        const pkg = parseInt(row.querySelector('.input-packages').value) || 0;
                        const units = parseInt(row.querySelector('.input-units').value) || 0;
                        const saldo = parseInt(row.querySelector('.input-saldo').value) || 0;
                        const price = parseFloat(row.querySelector('.input-price').value) || 0;
                        const productId = parseInt(row.getAttribute('data-product-id')) || 0;

                        const totalInitial = (pkg * unitsPerPkg) + units;
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

                    document.getElementById('header-total-combos').innerHTML = `${totalLiquorCombos} <span class="text-xs font-sans text-zinc-400 font-normal">combos</span>`;
                    document.getElementById('header-subtotal-combos').textContent = 'Subtotal: Bs. ' + subtotalLiquors.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    document.getElementById('badge-liquor-subtotal').textContent = 'Subtotal: Bs. ' + subtotalLiquors.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    document.getElementById('tfoot-liquor-vendido').textContent = totalLiquorCombos;
                    document.getElementById('tfoot-liquor-subtotal').textContent = 'Bs. ' + subtotalLiquors.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                    document.getElementById('header-total-extras').innerHTML = `${totalMixerExtras} <span class="text-xs font-sans text-zinc-400 font-normal">extras</span>`;
                    document.getElementById('header-subtotal-extras').textContent = 'Subtotal: Bs. ' + subtotalMixers.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    document.getElementById('badge-mixer-subtotal').textContent = 'Subtotal Extras: Bs. ' + subtotalMixers.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    document.getElementById('tfoot-mixer-consumido').textContent = totalMixerConsumed;
                    document.getElementById('tfoot-mixer-extras').textContent = totalMixerExtras;
                    document.getElementById('tfoot-mixer-subtotal').textContent = 'Bs. ' + subtotalMixers.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                    document.getElementById('header-grand-total').innerHTML = `<span class="text-amber-400 text-lg mr-0.5 font-sans">Bs.</span>${grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                    document.getElementById('footer-grand-total').textContent = 'Bs. ' + grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                };

                document.querySelectorAll('#sales-form input').forEach(input => {
                    input.addEventListener('input', window.recalculateSales);
                });
            });
            </script>
        @endif

    @endif

</div>
@endsection
