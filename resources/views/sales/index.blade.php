@extends('layouts.app')

@section('title', 'Ventas por Barra')

@section('content')

<style>
    /* Ocultar flechas de números nativas */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none !important;
        margin: 0 !important;
    }
    input[type=number] {
        -moz-appearance: textfield !important;
        appearance: textfield !important;
    }
</style>

<div class="space-y-4 max-w-7xl mx-auto w-full pb-8">

    <!-- ========================================================
         1. CABECERA PRINCIPAL: TÍTULO, NOCHE & ACCIÓN DE GUARDAR
         ======================================================== -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 px-5 py-3.5 rounded-2xl theme-card border theme-border">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-white font-sans">
                Ventas por Barra & Combos
            </h1>
            <p class="text-xs text-zinc-400 mt-0.5">
                Liquidación de combos, consumo de botellas y recaudación por punto de venta
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <!-- Selector de Noche -->
            @if($allSessions->isNotEmpty())
                <form method="GET" action="{{ route('sales.index') }}" class="flex items-center">
                    <input type="hidden" name="bar" value="{{ $selectedBar }}">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-zinc-950 border border-zinc-800 text-xs hover:border-[#F5B81C] transition-colors">
                        <svg class="w-3.5 h-3.5 text-[#F5B81C] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                        </svg>
                        <select name="session_id" id="session_id" onchange="this.form.submit()" 
                                class="bg-transparent border-0 text-xs font-semibold text-white focus:outline-none cursor-pointer pr-1">
                            @foreach($allSessions as $s)
                                <option value="{{ $s->id }}" {{ $session && $session->id === $s->id ? 'selected' : '' }} class="bg-zinc-950 text-white">
                                    {{ $s->day_name }} {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }} ({{ $s->isOpen() ? 'En Vivo' : 'Cerrada' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            @endif

            <!-- Estado Noche Cerrada (Discreto) -->
            @if($session && !$session->isOpen())
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-zinc-950 border border-zinc-800 text-xs text-zinc-400">
                    <svg class="w-3.5 h-3.5 text-zinc-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <span>Modo lectura</span>
                    <a href="{{ route('closing.index', ['session_id' => $session->id]) }}" class="text-[#F5B81C] hover:underline ml-1 font-semibold">Cierre &rarr;</a>
                </div>
            @endif

            <!-- Botón Guardar en Cabecera (Para barras tradicionales) -->
            @if($session && $session->isOpen())
                <span id="sales-autosave-status" class="text-[10px] font-mono text-zinc-500" aria-live="polite"></span>
                <button type="submit" form="sales-form" class="px-4 py-1.5 rounded-xl bg-[#F5B81C] text-black font-bold text-xs hover:bg-[#e5ac18] transition-all flex items-center gap-1.5 cursor-pointer dilemo-btn shadow-sm">
                    <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Guardar Ventas</span>
                </button>
            @endif
        </div>
    </div>

    @if(!$session)
        <!-- Estado Vacío -->
        <div class="p-12 text-center rounded-2xl theme-card border theme-border">
            <h3 class="text-sm font-bold uppercase tracking-wider text-white">No hay ninguna noche abierta o seleccionada</h3>
            <p class="text-xs text-zinc-400 mt-1 mb-4">Apertura una noche para registrar las ventas.</p>
            <a href="{{ route('sessions.create') }}" class="px-4 py-2 rounded-xl bg-[#F5B81C] text-black font-bold text-xs hover:bg-[#e5ac18] transition-all dilemo-btn inline-block">
                + Aperturar Nueva Noche
            </a>
        </div>
    @else

        <!-- ========================================================
             2. SELECTOR DE PUNTOS DE VENTA (iOS SEGMENTED CONTROL)
             ======================================================== -->
        <div class="flex items-center justify-between p-2 rounded-2xl theme-card border theme-border">
            <div class="inline-flex p-1 rounded-xl bg-zinc-950 border border-zinc-800">
                @foreach($availableBars as $bar)
                    @php
                        $isSelected = $selectedBar === $bar;
                        $isKelly = str_contains($bar, 'Kelly');
                        $badgeLabel = $isKelly ? 'Piso Principal' : 'Subterráneo';
                    @endphp
                    <a href="{{ route('sales.index', ['session_id' => $session->id, 'bar' => $bar]) }}" 
                       class="px-4 py-2 rounded-lg font-bold text-xs transition-all flex items-center gap-2 cursor-pointer {{ $isSelected ? 'bg-[#F5B81C] text-black shadow-sm' : 'text-zinc-400 hover:text-white' }}">
                        <span>{{ $bar }}</span>
                        <span class="text-[10px] font-medium opacity-75">({{ $badgeLabel }})</span>
                    </a>
                @endforeach
            </div>

            @if($session->isOpen())
                <button type="button" onclick="document.getElementById('modal-add-special').classList.remove('hidden')"
                        class="px-3.5 py-1.5 rounded-xl bg-zinc-950 border border-zinc-800 text-xs font-semibold text-zinc-300 hover:text-white hover:border-zinc-700 transition-all flex items-center gap-1.5 cursor-pointer dilemo-btn">
                    <span class="text-[#F5B81C] font-bold">+</span>
                    <span>Nueva Soda en Catálogo</span>
                </button>
            @endif
        </div>

            <!-- ======================================================== -->
            <!-- VISTA DE BARRAS TRADICIONALES (KELLY & ARIEL) -->
            <!-- ======================================================== -->

            <!-- Métricas Claras de la Barra para Don Ludo -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                
                <!-- Total Bebidas Liquidado -->
                <div class="theme-card rounded-2xl p-5 border theme-border">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Total Bebidas</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-zinc-950 border border-zinc-800 text-zinc-400">{{ $selectedBar }}</span>
                    </div>
                    <div class="mt-2 text-2xl font-black font-mono text-[#F5B81C] tracking-tight" id="header-grand-total">
                        <span class="text-sm mr-0.5 font-sans font-bold">Bs.</span>{{ number_format($grandTotalBar, 2) }}
                    </div>
                    <p class="text-[11px] text-zinc-400 mt-1 font-sans">
                        <span id="header-total-combos" class="font-semibold text-white">{{ $totalLiquorCombos }} combos</span> (Bs. <span id="header-subtotal-combos-val">{{ number_format($subtotalLiquors, 2) }}</span>) + <span id="header-total-extras" class="font-semibold text-white">{{ $totalMixerExtras }} extras</span>
                    </p>
                </div>

                <!-- En QR (YASTA / YAPE) -->
                <div class="theme-card rounded-2xl p-5 border theme-border">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Cobrado por QR</span>
                        <a href="{{ route('qrs.index', ['session_id' => $session->id]) }}" class="text-[10px] font-semibold text-blue-400 hover:underline">Ver QR &rarr;</a>
                    </div>
                    <div class="mt-2 text-2xl font-black font-mono text-blue-400 tracking-tight">
                        <span class="text-sm mr-0.5 font-sans font-bold">Bs.</span>{{ number_format($barTotalQr, 2) }}
                    </div>
                    <p class="text-[11px] text-zinc-500 mt-1 font-mono">
                        YASTA: Bs. {{ number_format($barTotalQrYasta, 2) }} • YAPE: Bs. {{ number_format($barTotalQrYape, 2) }}
                    </p>
                </div>

                <!-- En Tarjetas POS -->
                <div class="theme-card rounded-2xl p-5 border theme-border">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Tarjetas (POS)</span>
                        <a href="{{ route('invoices.index', ['session_id' => $session->id]) }}" class="text-[10px] font-semibold text-purple-400 hover:underline">Ver POS &rarr;</a>
                    </div>
                    <div class="mt-2 text-2xl font-black font-mono text-purple-400 tracking-tight">
                        <span class="text-sm mr-0.5 font-sans font-bold">Bs.</span>{{ number_format($barTotalCard, 2) }}
                    </div>
                    <p class="text-[11px] text-zinc-500 mt-1 font-mono">
                        Neto: Bs. {{ number_format($barTotalCardNet, 2) }} @if($barTotalCardCommission > 0)<span class="text-zinc-600">(Com. Bs. {{ number_format($barTotalCardCommission, 2) }})</span>@endif
                    </p>
                </div>

                <!-- Efectivo en Mano en Barra -->
                <div class="theme-card rounded-2xl p-5 border theme-border">
                    <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider block">Efectivo en Barra</span>
                    <div class="mt-2 text-2xl font-black font-mono text-emerald-400 tracking-tight">
                        <span class="text-sm mr-0.5 font-sans font-bold">Bs.</span><span id="header-bar-cash">{{ number_format($barCashRemaining, 2) }}</span>
                    </div>
                    <p class="text-[11px] text-zinc-500 mt-1 font-sans">
                        Dinero físico (Total − QR − Tarjetas)
                    </p>
                </div>
            </div>

            <!-- Modal: Registrar Variante Especial en Catálogo -->
            <div id="modal-add-special" class="hidden fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4">
                <div class="theme-card p-6 rounded-2xl border theme-border max-w-md w-full shadow-2xl">
                    <div class="flex items-center justify-between border-b theme-border pb-3 mb-4">
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider font-sans">Nueva Soda en Catálogo</h3>
                        <button type="button" onclick="document.getElementById('modal-add-special').classList.add('hidden')" class="text-zinc-400 hover:text-white text-base font-bold cursor-pointer">&times;</button>
                    </div>
                    <p class="text-xs text-zinc-400 mb-4">Define una soda especial para una categoría de tragos (ej: Sprite para Rones o Fernet).</p>
                    
                    <form method="POST" action="{{ route('sales.specialMixers.store') }}" class="space-y-4 {{ !$session->isOpen() ? 'pointer-events-none opacity-50' : '' }}">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-zinc-300 mb-1">Categoría del Trago</label>
                            <select name="category" required class="w-full text-xs rounded-xl px-3 py-2 bg-zinc-950 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] cursor-pointer">
                                <option value="" disabled selected class="bg-zinc-950">-- Seleccionar Categoría --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" class="bg-zinc-950">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-300 mb-1">Nombre de la Variante / Soda</label>
                            <input type="text" name="mixer_name" placeholder="Ej: Sprite 2.0L, Aquarius Pera" required 
                                   class="w-full text-xs rounded-xl px-3 py-2 bg-zinc-950 border border-zinc-800 text-white placeholder-zinc-500 focus:outline-none focus:border-[#F5B81C]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-300 mb-1">Producto Mixer para Descuento en Barra</label>
                            <select name="product_id" required class="w-full text-xs rounded-xl px-3 py-2 bg-zinc-950 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] cursor-pointer">
                                <option value="" disabled selected class="bg-zinc-950">-- Seleccionar Soda / Mixer en Almacén --</option>
                                @foreach($allMixerProducts as $mix)
                                    <option value="{{ $mix->id }}" class="bg-zinc-950">{{ $mix->name }} (Bs. {{ number_format($mix->sale_price, 2) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex justify-end gap-2 pt-3">
                            <button type="button" onclick="document.getElementById('modal-add-special').classList.add('hidden')" 
                                    class="px-4 py-2 rounded-xl text-xs text-zinc-400 font-semibold bg-zinc-950 border border-zinc-800 hover:text-white cursor-pointer">Cancelar</button>
                            <button type="submit" class="px-5 py-2 bg-[#F5B81C] text-black font-bold text-xs rounded-xl hover:bg-[#e5ac18] cursor-pointer dilemo-btn">Guardar en Catálogo</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal: Asignar Mixers Especiales a la Fila de Venta -->
            <div id="modal-assign-special" class="hidden fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4">
                <div class="theme-card p-6 rounded-2xl border theme-border max-w-lg w-full shadow-2xl space-y-4">
                    <div class="flex items-center justify-between border-b theme-border pb-3">
                        <div>
                            <span class="text-[10px] uppercase tracking-wider text-[#F5B81C] font-bold block">Acompañamiento Alternativo</span>
                            <h3 class="text-base font-bold text-white" id="modal-liquor-title">Licor</h3>
                        </div>
                        <button type="button" onclick="closeSpecialModal()" class="text-zinc-400 hover:text-white text-lg font-bold cursor-pointer">&times;</button>
                    </div>

                    <div class="bg-zinc-950 rounded-xl p-3 border border-zinc-800 flex items-center justify-between text-xs">
                        <div>
                            <span class="text-zinc-400">Total Combos Vendidos:</span>
                            <span class="font-bold text-white ml-1 text-sm font-mono" id="modal-total-combos">0</span>
                        </div>
                        <div>
                            <span class="text-zinc-400">Soda por Defecto:</span>
                            <span class="font-bold text-[#F5B81C] ml-1" id="modal-default-mixer-name">-</span>
                        </div>
                    </div>

                    <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
                        <p class="text-[11px] text-zinc-400">
                            Asigna cuántos combos salieron con un mixer alternativo (se restarán automáticamente de la soda por defecto):
                        </p>
                        
                        <div class="space-y-2">
                            @foreach($allMixerProducts as $mixer)
                                <div class="flex items-center justify-between p-2.5 rounded-xl bg-zinc-950 border border-zinc-800 hover:border-zinc-700 transition-all">
                                    <span class="text-xs font-medium text-white">{{ $mixer->name }}</span>
                                    <div class="flex items-center gap-2">
                                        <label class="text-[11px] text-zinc-400">Combos:</label>
                                        <input type="number" min="0" value="0" 
                                               data-mixer-id="{{ $mixer->id }}" 
                                               data-mixer-name="{{ $mixer->name }}"
                                               class="modal-special-qty w-16 text-center rounded-lg px-2 py-1 text-xs font-mono font-bold bg-black border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C]">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="p-3 bg-zinc-950 border border-zinc-800 rounded-xl flex items-center justify-between text-xs">
                        <span class="text-zinc-300">Sodas por defecto restantes:</span>
                        <span class="font-bold text-[#F5B81C] text-sm font-mono" id="modal-remaining-default">0</span>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t theme-border">
                        <button type="button" onclick="closeSpecialModal()" class="px-4 py-2 rounded-xl text-xs text-zinc-400 font-semibold bg-zinc-950 border border-zinc-800 hover:text-white cursor-pointer">Cancelar</button>
                        <button type="button" onclick="saveSpecialModal()" class="px-5 py-2 bg-[#F5B81C] text-black font-bold text-xs rounded-xl hover:bg-[#e5ac18] cursor-pointer dilemo-btn">Listo / Aplicar</button>
                    </div>
                </div>
            </div>

            <!-- Formulario Tabular de Inventario y Ventas para Barras -->
            <form method="POST" action="{{ route('sales.updateBulk') }}" id="sales-form" data-ajax="true" class="{{ !$session->isOpen() ? 'pointer-events-none opacity-50' : '' }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="night_session_id" value="{{ $session->id }}">
                <input type="hidden" name="bar_name" value="{{ $selectedBar }}">

                <div class="space-y-4">

                    <!-- 1. TABLA DE LICORES (COMBOS) -->
                    <div class="rounded-2xl theme-card border theme-border overflow-hidden">
                        <div class="px-5 py-3.5 bg-zinc-950 border-b theme-border flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h3 class="text-xs font-bold text-white uppercase tracking-wider font-sans">1. Licores & Combos</h3>
                                <p class="text-[11px] text-zinc-400 mt-0.5">
                                    Stock inicial sincronizado desde <a href="{{ route('barInventory.index', ['session_id' => $session->id, 'bar' => $selectedBar]) }}" class="text-[#F5B81C] hover:underline font-semibold">Inventario de Barras</a>. Cada combo incluye su soda (Gins incluyen 2 tónicas).
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-mono font-bold text-[#F5B81C]" id="badge-liquor-subtotal">
                                    Subtotal: Bs. {{ number_format($subtotalLiquors, 2) }}
                                </span>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left border-collapse excel-table" id="table-liquors">
                                <thead class="bg-zinc-950 border-b theme-border text-zinc-400 font-semibold uppercase text-[11px] tracking-wider">
                                    <tr>
                                        <th class="px-3 py-3.5 w-8 text-center">N°</th>
                                        <th class="px-4 py-3.5">Licor / Presentación</th>
                                        <th class="px-4 py-3.5">Acompañamiento</th>
                                        <th class="px-3 py-3.5 text-center">Especial</th>
                                        <th class="px-2 py-3.5 w-16 text-center border-l theme-border bg-zinc-950/60">
                                            <div class="flex flex-col items-center">
                                                <span class="text-zinc-200">Cajas</span>
                                                <span class="text-[9px] text-zinc-500 lowercase">stock</span>
                                            </div>
                                        </th>
                                        <th class="px-2 py-3.5 w-16 text-center bg-zinc-950/60">
                                            <div class="flex flex-col items-center">
                                                <span class="text-zinc-200">Sueltas</span>
                                                <span class="text-[9px] text-zinc-500 lowercase">stock</span>
                                            </div>
                                        </th>
                                        <th class="px-3 py-3.5 w-20 text-center border-l theme-border">Total Inicial</th>
                                        <th class="px-3 py-3.5 w-24 text-center border-l theme-border bg-zinc-950/80">
                                            <div class="flex flex-col items-center text-[#F5B81C]">
                                                <span class="font-bold">Saldo Cierre</span>
                                                <span class="text-[9px] text-zinc-400 lowercase">conteo</span>
                                            </div>
                                        </th>
                                        <th class="px-4 py-3.5 w-24 text-center border-l theme-border font-bold text-[#F5B81C]">Combos Vendidos</th>
                                        <th class="px-4 py-3.5 w-28 text-right border-l theme-border">Precio Combo</th>
                                        <th class="px-4 py-3.5 w-32 text-right border-l theme-border">Subtotal (Bs.)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y theme-border font-sans">
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
                                        <tr class="hover:bg-zinc-900/40 transition-colors liquor-row" 
                                            data-row-id="{{ $sale->id }}" 
                                            data-product-id="{{ $sale->product_id }}"
                                            data-product-name="{{ $sale->product->name }}"
                                            data-units-per-pkg="{{ $sale->product->units_per_package ?? 1 }}"
                                            data-mixer-id="{{ $mixerId }}"
                                            data-mixer-name="{{ $mixerName }}"
                                            data-mixer-ratio="{{ $mixerRatio }}">
                                            <td class="px-3 py-2.5 text-center text-zinc-500 font-mono">{{ $index + 1 }}</td>
                                            <td class="px-4 py-2.5">
                                                <span class="font-bold text-white block">{{ $sale->product->name }}</span>
                                                <span class="text-[11px] text-zinc-400 font-normal">{{ $sale->product->unit }} • {{ $sale->product->units_per_package ?? 1 }} unid/caja</span>
                                            </td>
                                            <td class="px-4 py-2.5 text-zinc-300 text-xs">
                                                <span class="inline-flex px-2 py-0.5 rounded-lg bg-zinc-950 border border-zinc-800 text-[11px] font-medium">
                                                    + {{ $mixerName }}
                                                </span>
                                            </td>
                                            
                                            <!-- BOTÓN INTERACTIVO DE MIXER ESPECIAL -->
                                            <td class="px-3 py-2 text-center">
                                                <input type="hidden" 
                                                       name="sales[{{ $sale->id }}][selected_special_mixer]" 
                                                       id="input-specials-{{ $sale->id }}" 
                                                       value="{{ $specialsJson }}" 
                                                       class="input-special-mixers">
                                                <button type="button" 
                                                        onclick="openSpecialModal({{ $sale->id }})" 
                                                        id="btn-special-{{ $sale->id }}"
                                                        class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-all inline-flex items-center gap-1 cursor-pointer border {{ $hasSpecials ? 'bg-zinc-900 text-[#F5B81C] border-[#F5B81C]/50' : 'bg-zinc-950 text-zinc-400 border-zinc-800 hover:text-white' }}">
                                                    <span id="badge-special-{{ $sale->id }}">{{ $badgeText }}</span>
                                                </button>
                                            </td>

                                            <td class="px-2 py-2 text-center border-l theme-border">
                                                <input type="number" 
                                                       data-excel-col="0"
                                                       name="sales[{{ $sale->id }}][packages]" 
                                                       value="{{ $sale->packages }}" 
                                                       min="0"
                                                       class="excel-input input-packages w-14 sm:w-16 h-9 text-center rounded-xl font-mono font-bold text-xs sm:text-sm bg-zinc-950 border border-zinc-800 text-white focus:outline-none focus:ring-2 focus:ring-[#F5B81C] focus:border-[#F5B81C] focus:bg-[#181507] focus:text-[#F5B81C] transition-all">
                                            </td>
                                            <td class="px-2 py-2 text-center">
                                                <input type="number" 
                                                       data-excel-col="1"
                                                       name="sales[{{ $sale->id }}][units]" 
                                                       value="{{ $sale->units }}" 
                                                       min="0"
                                                       class="excel-input input-units w-14 sm:w-16 h-9 text-center rounded-xl font-mono font-bold text-xs sm:text-sm bg-zinc-950 border border-zinc-800 text-white focus:outline-none focus:ring-2 focus:ring-[#F5B81C] focus:border-[#F5B81C] focus:bg-[#181507] focus:text-[#F5B81C] transition-all">
                                            </td>
                                            <td class="px-3 py-2.5 text-center border-l theme-border">
                                                <div class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-zinc-950 border border-zinc-800 font-bold font-mono text-zinc-300 text-xs cell-total-initial">
                                                    {{ $sale->total_initial }}
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 text-center border-l theme-border bg-zinc-950/40">
                                                <input type="number" 
                                                       data-excel-col="2"
                                                       name="sales[{{ $sale->id }}][saldo]" 
                                                       value="{{ $sale->saldo }}" 
                                                       min="0"
                                                       class="excel-input input-saldo w-16 sm:w-20 h-9 text-center rounded-xl font-mono font-bold text-xs sm:text-sm bg-zinc-950 border-2 border-zinc-700/80 text-white focus:outline-none focus:ring-2 focus:ring-[#F5B81C] focus:border-[#F5B81C] focus:bg-[#181507] focus:text-[#F5B81C] transition-all shadow-inner">
                                            </td>
                                            <td class="px-4 py-2.5 text-center border-l theme-border">
                                                <div class="inline-flex items-center justify-center px-3 py-1 rounded-xl bg-zinc-950 border border-zinc-800 font-black font-mono text-[#F5B81C] text-sm sm:text-base cell-vendido">
                                                    {{ $sale->vendido }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-2 text-right border-l theme-border">
                                                <input type="number" 
                                                       step="0.5" 
                                                       data-excel-col="3"
                                                       name="sales[{{ $sale->id }}][unit_price]" 
                                                       value="{{ $sale->unit_price }}" 
                                                       min="0"
                                                       class="excel-input input-price w-20 sm:w-24 h-9 text-right rounded-xl px-2 font-mono font-bold text-xs sm:text-sm bg-zinc-950 border border-zinc-800 text-zinc-200 focus:outline-none focus:ring-2 focus:ring-[#F5B81C] focus:border-[#F5B81C] focus:bg-[#181507] focus:text-[#F5B81C] transition-all">
                                            </td>
                                            <td class="px-4 py-2.5 text-right font-bold font-mono text-white text-xs sm:text-sm border-l theme-border cell-subtotal">
                                                Bs. {{ number_format($sale->subtotal, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="px-4 py-8 text-center text-zinc-500 font-sans">No hay licores registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-zinc-950 border-t theme-border font-bold text-xs text-white">
                                    <tr>
                                        <td colspan="8" class="px-4 py-3 text-right uppercase tracking-wider font-sans">Subtotal Combos:</td>
                                        <td class="px-4 py-3 text-center font-mono font-black text-[#F5B81C] text-base" id="tfoot-liquor-vendido">{{ $totalLiquorCombos }}</td>
                                        <td></td>
                                        <td class="px-4 py-3 text-right font-mono font-black text-[#F5B81C] text-sm" id="tfoot-liquor-subtotal">Bs. {{ number_format($subtotalLiquors, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- 2. TABLA DE MIXERS / SODAS (EXTRAS) -->
                    <div class="rounded-2xl theme-card border theme-border overflow-hidden">
                        <div class="px-5 py-3.5 bg-zinc-950 border-b theme-border flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h3 class="text-xs font-bold text-white uppercase tracking-wider font-sans">2. Mixers, Sodas & Aguas (Control de Extras)</h3>
                                <p class="text-[11px] text-zinc-400 mt-0.5">
                                    Las sodas de combos se descuentan solas. Solo las botellas adicionales (extras) se cobran.
                                </p>
                            </div>
                            <span class="text-xs font-mono font-bold text-[#F5B81C]" id="badge-mixer-subtotal">
                                Subtotal Extras: Bs. {{ number_format($subtotalMixers, 2) }}
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left border-collapse excel-table" id="table-mixers">
                                <thead class="bg-zinc-950 border-b theme-border text-zinc-400 font-semibold uppercase text-[11px] tracking-wider">
                                    <tr>
                                        <th class="px-3 py-3.5 w-8 text-center">N°</th>
                                        <th class="px-4 py-3.5">Mixer / Soda</th>
                                        <th class="px-2 py-3.5 w-16 text-center border-l theme-border bg-zinc-950/60">
                                            <div class="flex flex-col items-center">
                                                <span class="text-zinc-200">Cajas</span>
                                                <span class="text-[9px] text-zinc-500 lowercase">stock</span>
                                            </div>
                                        </th>
                                        <th class="px-2 py-3.5 w-16 text-center bg-zinc-950/60">
                                            <div class="flex flex-col items-center">
                                                <span class="text-zinc-200">Sueltas</span>
                                                <span class="text-[9px] text-zinc-500 lowercase">stock</span>
                                            </div>
                                        </th>
                                        <th class="px-3 py-3.5 w-20 text-center border-l theme-border">Total Inicial</th>
                                        <th class="px-3 py-3.5 w-24 text-center border-l theme-border bg-zinc-950/80">
                                            <div class="flex flex-col items-center text-[#F5B81C]">
                                                <span class="font-bold">Saldo Cierre</span>
                                                <span class="text-[9px] text-zinc-400 lowercase">conteo</span>
                                            </div>
                                        </th>
                                        <th class="px-4 py-3.5 w-20 text-center border-l theme-border">Consumidas</th>
                                        <th class="px-4 py-3.5 w-24 text-center border-l theme-border text-zinc-400">En Combos</th>
                                        <th class="px-4 py-3.5 w-20 text-center border-l theme-border font-bold text-[#F5B81C]">Extras</th>
                                        <th class="px-4 py-3.5 w-28 text-right border-l theme-border">Precio Extra</th>
                                        <th class="px-4 py-3.5 w-32 text-right border-l theme-border">Total Extras (Bs.)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y theme-border font-sans">
                                    @forelse($mixerSales as $index => $sale)
                                        <tr class="hover:bg-zinc-900/40 transition-colors mixer-row" 
                                            data-row-id="{{ $sale->id }}" 
                                            data-units-per-pkg="{{ $sale->product->units_per_package ?? 1 }}"
                                            data-product-id="{{ $sale->product_id }}">
                                            <td class="px-3 py-2.5 text-center text-zinc-500 font-mono">{{ $index + 1 }}</td>
                                            <td class="px-4 py-2.5 font-bold text-white">
                                                {{ $sale->product->name }}
                                            </td>
                                            <td class="px-2 py-2 text-center border-l theme-border">
                                                <input type="number" 
                                                       data-excel-col="0"
                                                       name="sales[{{ $sale->id }}][packages]" 
                                                       value="{{ $sale->packages }}" 
                                                       min="0"
                                                       class="excel-input input-packages w-14 sm:w-16 h-9 text-center rounded-xl font-mono font-bold text-xs sm:text-sm bg-zinc-950 border border-zinc-800 text-white focus:outline-none focus:ring-2 focus:ring-[#F5B81C] focus:border-[#F5B81C] focus:bg-[#181507] focus:text-[#F5B81C] transition-all">
                                            </td>
                                            <td class="px-2 py-2 text-center">
                                                <input type="number" 
                                                       data-excel-col="1"
                                                       name="sales[{{ $sale->id }}][units]" 
                                                       value="{{ $sale->units }}" 
                                                       min="0"
                                                       class="excel-input input-units w-14 sm:w-16 h-9 text-center rounded-xl font-mono font-bold text-xs sm:text-sm bg-zinc-950 border border-zinc-800 text-white focus:outline-none focus:ring-2 focus:ring-[#F5B81C] focus:border-[#F5B81C] focus:bg-[#181507] focus:text-[#F5B81C] transition-all">
                                            </td>
                                            <td class="px-3 py-2.5 text-center border-l theme-border">
                                                <div class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-zinc-950 border border-zinc-800 font-bold font-mono text-zinc-300 text-xs cell-total-initial">
                                                    {{ $sale->total_initial }}
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 text-center border-l theme-border bg-zinc-950/40">
                                                <input type="number" 
                                                       data-excel-col="2"
                                                       name="sales[{{ $sale->id }}][saldo]" 
                                                       value="{{ $sale->saldo }}" 
                                                       min="0"
                                                       class="excel-input input-saldo w-16 sm:w-20 h-9 text-center rounded-xl font-mono font-bold text-xs sm:text-sm bg-zinc-950 border-2 border-zinc-700/80 text-white focus:outline-none focus:ring-2 focus:ring-[#F5B81C] focus:border-[#F5B81C] focus:bg-[#181507] focus:text-[#F5B81C] transition-all shadow-inner">
                                            </td>
                                            <td class="px-4 py-2.5 text-center font-mono text-zinc-300 border-l theme-border cell-consumido">
                                                {{ $sale->vendido }}
                                            </td>
                                            <td class="px-4 py-2.5 text-center font-mono text-zinc-400 border-l theme-border cell-included">
                                                {{ $sale->included_in_combos ?? 0 }}
                                            </td>
                                            <td class="px-4 py-2.5 text-center border-l theme-border">
                                                <div class="inline-flex items-center justify-center px-3 py-1 rounded-xl bg-zinc-950 border border-zinc-800 font-black font-mono text-[#F5B81C] text-sm sm:text-base cell-extras">
                                                    {{ $sale->extras ?? 0 }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-2 text-right border-l theme-border">
                                                <input type="number" 
                                                       step="0.5" 
                                                       data-excel-col="3"
                                                       name="sales[{{ $sale->id }}][unit_price]" 
                                                       value="{{ $sale->unit_price }}" 
                                                       min="0"
                                                       class="excel-input input-price w-20 sm:w-24 h-9 text-right rounded-xl px-2 font-mono font-bold text-xs sm:text-sm bg-zinc-950 border border-zinc-800 text-zinc-200 focus:outline-none focus:ring-2 focus:ring-[#F5B81C] focus:border-[#F5B81C] focus:bg-[#181507] focus:text-[#F5B81C] transition-all">
                                            </td>
                                            <td class="px-4 py-2.5 text-right font-bold font-mono text-white text-xs sm:text-sm border-l theme-border cell-subtotal">
                                                Bs. {{ number_format($sale->subtotal, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="px-4 py-8 text-center text-zinc-500 font-sans">No hay mixers registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-zinc-950 border-t theme-border font-bold text-xs text-white">
                                    <tr>
                                        <td colspan="6" class="px-4 py-3 text-right uppercase tracking-wider font-sans">Totales Mixers:</td>
                                        <td class="px-4 py-3 text-center font-mono font-bold" id="tfoot-mixer-consumido">{{ $totalMixerConsumed }}</td>
                                        <td></td>
                                        <td class="px-4 py-3 text-center font-mono font-black text-[#F5B81C] text-base" id="tfoot-mixer-extras">{{ $totalMixerExtras }}</td>
                                        <td></td>
                                        <td class="px-4 py-3 text-right font-mono font-black text-[#F5B81C] text-sm" id="tfoot-mixer-subtotal">Bs. {{ number_format($subtotalMixers, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- DETALLE DE COBROS DIGITALES (QR Y TARJETAS POS) -->
                    <!-- ======================================================== -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        
                        <!-- 1. TABLA COBROS QR EN ESTA BARRA -->
                        <div class="rounded-2xl theme-card border theme-border overflow-hidden">
                            <div class="px-4 py-3 bg-zinc-950 border-b theme-border flex items-center justify-between">
                                <div>
                                    <h3 class="text-xs font-bold text-white uppercase tracking-wider font-sans">
                                        Cobros QR en {{ $selectedBar }}
                                    </h3>
                                    <p class="text-[11px] text-zinc-400">Transferencias YASTA & YAPE</p>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <span class="text-xs font-mono font-bold text-blue-400">
                                        Bs. {{ number_format($barTotalQr, 2) }}
                                    </span>
                                    <a href="{{ route('qrs.index', ['session_id' => $session->id]) }}" 
                                       class="px-2.5 py-1 text-[10px] font-semibold rounded-lg bg-zinc-900 border border-zinc-800 text-blue-400 hover:text-white transition-all">
                                        Ver QR &rarr;
                                    </a>
                                </div>
                            </div>

                            <div class="overflow-x-auto max-h-60 overflow-y-auto">
                                <table class="w-full text-xs text-left border-collapse">
                                    <thead class="bg-zinc-950 border-b theme-border text-zinc-400 uppercase text-[10px] tracking-wider sticky top-0 font-semibold">
                                        <tr>
                                            <th class="px-4 py-2.5">Cobrante</th>
                                            <th class="px-3 py-2.5 text-center">App</th>
                                            <th class="px-4 py-2.5">Ref / Comprobante</th>
                                            <th class="px-4 py-2.5 text-right">Monto (Bs.)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y theme-border font-sans">
                                        @forelse($barQrPayments as $qr)
                                            <tr class="hover:bg-zinc-900/30">
                                                <td class="px-4 py-2 text-zinc-200 font-medium">{{ $qr->operator_name ?? '—' }}</td>
                                                <td class="px-3 py-2 text-center">
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-zinc-900 border border-zinc-800 text-zinc-300">
                                                        {{ $qr->bank_app }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2 text-zinc-400 text-[11px] truncate max-w-[120px] font-mono">{{ $qr->reference_code ?? '—' }}</td>
                                                <td class="px-4 py-2 text-right font-bold font-mono text-blue-400">Bs. {{ number_format($qr->amount, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-4 py-6 text-center text-zinc-500 text-xs">No hay cobros QR registrados para esta barra.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- 2. TABLA TARJETAS POS EN ESTA BARRA -->
                        <div class="rounded-2xl theme-card border theme-border overflow-hidden">
                            <div class="px-4 py-3 bg-zinc-950 border-b theme-border flex items-center justify-between">
                                <div>
                                    <h3 class="text-xs font-bold text-white uppercase tracking-wider font-sans">
                                        Pagos con Tarjeta (POS) en {{ $selectedBar }}
                                    </h3>
                                    <p class="text-[11px] text-zinc-400">Facturas y cobro con tarjeta</p>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <span class="text-xs font-mono font-bold text-purple-400">
                                        Bs. {{ number_format($barTotalCard, 2) }}
                                    </span>
                                    <a href="{{ route('invoices.index', ['session_id' => $session->id]) }}" 
                                       class="px-2.5 py-1 text-[10px] font-semibold rounded-lg bg-zinc-900 border border-zinc-800 text-purple-400 hover:text-white transition-all">
                                        Ver POS &rarr;
                                    </a>
                                </div>
                            </div>

                            <div class="overflow-x-auto max-h-60 overflow-y-auto">
                                <table class="w-full text-xs text-left border-collapse">
                                    <thead class="bg-zinc-950 border-b theme-border text-zinc-400 uppercase text-[10px] tracking-wider sticky top-0 font-semibold">
                                        <tr>
                                            <th class="px-4 py-2.5 text-center"># Factura</th>
                                            <th class="px-4 py-2.5">Detalle</th>
                                            <th class="px-4 py-2.5 text-right">Comisión</th>
                                            <th class="px-4 py-2.5 text-right">Monto Bruto</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y theme-border font-sans">
                                        @forelse($barCardInvoices as $inv)
                                            <tr class="hover:bg-zinc-900/30">
                                                <td class="px-4 py-2 text-center text-zinc-400 font-mono font-bold">#{{ $inv->correlative_num }}</td>
                                                <td class="px-4 py-2 text-zinc-300 text-[11px] truncate max-w-[140px]">{{ $inv->notes ?? 'Pago Tarjeta POS' }}</td>
                                                <td class="px-4 py-2 text-right text-zinc-500 text-[11px] font-mono">- Bs. {{ number_format($inv->commission_amount, 2) }}</td>
                                                <td class="px-4 py-2 text-right font-bold font-mono text-purple-400">Bs. {{ number_format($inv->amount, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-4 py-6 text-center text-zinc-500 text-xs">No hay pagos con tarjeta registrados para esta barra.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    <!-- Pie de Formulario Estático con Guardado (Sin Toasts) -->
                    <div class="p-4 rounded-2xl theme-card border theme-border flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-[#F5B81C] shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs text-zinc-400 block font-normal">Total Bebidas Liquidado en {{ $selectedBar }}:</span>
                                <span class="text-lg font-bold font-mono text-white" id="footer-grand-total">
                                    Bs. {{ number_format($grandTotalBar, 2) }}
                                </span>
                            </div>
                        </div>

                        @if($session->isOpen())
                            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-[#F5B81C] text-black font-bold text-xs uppercase tracking-wider hover:bg-[#e5ac18] transition-all flex items-center justify-center gap-2 dilemo-btn cursor-pointer shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Guardar Cambios de {{ $selectedBar }}</span>
                            </button>
                        @else
                            <div class="px-4 py-2 rounded-xl bg-zinc-950 border border-zinc-800 text-zinc-400 text-xs font-semibold flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-zinc-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                                <span>Noche cerrada (solo lectura)</span>
                            </div>
                        @endif
                    </div>

                </div>
            </form>

            <!-- Script Reactivo para Combos, Deducción Automática y Mixers Especiales -->
            <script>
            const barQrTotal = {{ (float)$barTotalQr }};
            const barCardNetTotal = {{ (float)$barTotalCardNet }};
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
                    remEl.className = 'font-bold text-rose-400 text-sm font-mono';
                    remEl.textContent = `${remaining} (¡Excede los combos!)`;
                } else {
                    remEl.className = 'font-bold text-[#F5B81C] text-sm font-mono';
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
                    btnEl.className = 'px-2.5 py-1 rounded-lg text-xs font-semibold transition-all inline-flex items-center gap-1 cursor-pointer border bg-zinc-900 text-[#F5B81C] border-[#F5B81C]/50';
                } else {
                    badgeEl.textContent = '+ Especial';
                    btnEl.className = 'px-2.5 py-1 rounded-lg text-xs font-semibold transition-all inline-flex items-center gap-1 cursor-pointer border bg-zinc-950 text-zinc-400 border-zinc-800 hover:text-white';
                }

                closeSpecialModal();
                window.recalculateSales();
                window.queueSalesAutosave?.();
            }

            document.addEventListener('DOMContentLoaded', function () {
                const liquorRows = document.querySelectorAll('.liquor-row');
                const mixerRows = document.querySelectorAll('.mixer-row');
                const salesForm = document.getElementById('sales-form');
                const autosaveStatus = document.getElementById('sales-autosave-status');
                let autosaveTimer = null;
                let autosaveInFlight = false;
                let autosaveQueued = false;

                function setAutosaveStatus(message, className) {
                    if (!autosaveStatus) return;
                    autosaveStatus.textContent = message;
                    autosaveStatus.className = `text-[10px] font-mono ${className}`;
                }

                async function autosaveSales() {
                    if (!salesForm) return;
                    if (autosaveInFlight) {
                        autosaveQueued = true;
                        return;
                    }

                    autosaveInFlight = true;
                    setAutosaveStatus('Guardando...', 'text-[#F5B81C]');
                    try {
                        const response = await fetch(salesForm.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: new FormData(salesForm)
                        });
                        if (!response.ok) throw new Error(`HTTP ${response.status}`);
                        setAutosaveStatus('Guardado', 'text-emerald-400');
                    } catch (error) {
                        console.error('Error en autoguardado de ventas:', error);
                        setAutosaveStatus('Error al guardar', 'text-rose-400');
                    } finally {
                        autosaveInFlight = false;
                        if (autosaveQueued) {
                            autosaveQueued = false;
                            queueAutosave();
                        }
                    }
                }

                function queueAutosave() {
                    if (!salesForm) return;
                    clearTimeout(autosaveTimer);
                    setAutosaveStatus('Pendiente...', 'text-zinc-400');
                    autosaveTimer = setTimeout(autosaveSales, 450);
                }

                window.queueSalesAutosave = queueAutosave;

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
                    const cashRemaining = Math.max(0, grandTotal - barQrTotal - barCardNetTotal);

                    const headerCombos = document.getElementById('header-total-combos');
                    if (headerCombos) headerCombos.textContent = `${totalLiquorCombos} combos`;

                    const headerSubtotalCombosVal = document.getElementById('header-subtotal-combos-val');
                    if (headerSubtotalCombosVal) headerSubtotalCombosVal.textContent = subtotalLiquors.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                    const badgeLiquorSubtotal = document.getElementById('badge-liquor-subtotal');
                    if (badgeLiquorSubtotal) badgeLiquorSubtotal.textContent = 'Subtotal: Bs. ' + subtotalLiquors.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                    const tfootLiquorVendido = document.getElementById('tfoot-liquor-vendido');
                    if (tfootLiquorVendido) tfootLiquorVendido.textContent = totalLiquorCombos;

                    const tfootLiquorSubtotal = document.getElementById('tfoot-liquor-subtotal');
                    if (tfootLiquorSubtotal) tfootLiquorSubtotal.textContent = 'Bs. ' + subtotalLiquors.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                    const headerExtras = document.getElementById('header-total-extras');
                    if (headerExtras) headerExtras.textContent = `${totalMixerExtras} extras`;

                    const headerSubtotalExtrasVal = document.getElementById('header-subtotal-extras-val');
                    if (headerSubtotalExtrasVal) headerSubtotalExtrasVal.textContent = subtotalMixers.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                    const badgeMixerSubtotal = document.getElementById('badge-mixer-subtotal');
                    if (badgeMixerSubtotal) badgeMixerSubtotal.textContent = 'Subtotal Extras: Bs. ' + subtotalMixers.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                    const tfootMixerConsumido = document.getElementById('tfoot-mixer-consumido');
                    if (tfootMixerConsumido) tfootMixerConsumido.textContent = totalMixerConsumed;

                    const tfootMixerExtras = document.getElementById('tfoot-mixer-extras');
                    if (tfootMixerExtras) tfootMixerExtras.textContent = totalMixerExtras;

                    const tfootMixerSubtotal = document.getElementById('tfoot-mixer-subtotal');
                    if (tfootMixerSubtotal) tfootMixerSubtotal.textContent = 'Bs. ' + subtotalMixers.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                    const headerGrandTotal = document.getElementById('header-grand-total');
                    if (headerGrandTotal) headerGrandTotal.innerHTML = `<span class="text-sm mr-0.5 font-sans font-bold">Bs.</span>${grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

                    const headerBarCash = document.getElementById('header-bar-cash');
                    if (headerBarCash) headerBarCash.textContent = cashRemaining.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                    const footerGrandTotal = document.getElementById('footer-grand-total');
                    if (footerGrandTotal) footerGrandTotal.textContent = 'Bs. ' + grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                };

                /* ========================================================
                   MOTOR DE NAVEGACIÓN Y LLENADO RÁPIDO TIPO EXCEL
                   - Selección automática al enfocar (sobrescribe directo)
                   - Flecha Abajo / Enter: siguiente fila, misma columna
                   - Flecha Arriba / Shift+Enter: fila anterior, misma columna
                   - Flecha Derecha / Izquierda: columna adyacente
                   ======================================================== */
                function initExcelNavigation(form) {
                    if (!form) return;

                    function getMatrix(currentTable) {
                        const table = currentTable || form;
                        const visibleRows = Array.from(table.querySelectorAll('.liquor-row, .mixer-row')).filter(tr => tr.style.display !== 'none');
                        return visibleRows.map(tr => {
                            const inputs = Array.from(tr.querySelectorAll('input[data-excel-col]')).filter(inp => !inp.disabled);
                            inputs.sort((a, b) => parseInt(a.dataset.excelCol) - parseInt(b.dataset.excelCol));
                            return { tr, inputs };
                        });
                    }

                    form.addEventListener('focusin', function (e) {
                        const input = e.target;
                        if (!input.matches('input[data-excel-col]')) return;

                        // Seleccionar todo el número al entrar para escribir directo sin borrar
                        setTimeout(() => {
                            if (document.activeElement === input) {
                                try { input.select(); } catch (err) {}
                            }
                        }, 25);

                        // Iluminar fila activa
                        form.querySelectorAll('.liquor-row, .mixer-row').forEach(r => r.classList.remove('excel-active-row'));
                        const tr = input.closest('tr');
                        if (tr) tr.classList.add('excel-active-row');
                    });

                    form.addEventListener('focusout', function (e) {
                        const input = e.target;
                        if (!input.matches('input[data-excel-col]')) return;
                        const tr = input.closest('tr');
                        if (tr && !tr.contains(document.activeElement)) {
                            tr.classList.remove('excel-active-row');
                        }
                    });

                    form.addEventListener('keydown', function (e) {
                        const input = e.target;
                        if (!input.matches('input[data-excel-col]')) return;

                        const key = e.key;
                        if (!['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'Enter', 'Tab'].includes(key)) {
                            return;
                        }

                        const currentTable = input.closest('table');
                        const matrix = getMatrix(currentTable);
                        const currentTr = input.closest('tr');
                        const rowIndex = matrix.findIndex(item => item.tr === currentTr);
                        if (rowIndex === -1) return;

                        const rowInputs = matrix[rowIndex].inputs;
                        const colIndex = rowInputs.indexOf(input);
                        if (colIndex === -1) return;

                        let targetInput = null;

                        if (key === 'ArrowDown') {
                            e.preventDefault();
                            if (rowIndex + 1 < matrix.length) {
                                const nextRowInputs = matrix[rowIndex + 1].inputs;
                                targetInput = nextRowInputs[colIndex] || nextRowInputs[nextRowInputs.length - 1];
                            }
                        } else if (key === 'ArrowUp') {
                            e.preventDefault();
                            if (rowIndex - 1 >= 0) {
                                const prevRowInputs = matrix[rowIndex - 1].inputs;
                                targetInput = prevRowInputs[colIndex] || prevRowInputs[prevRowInputs.length - 1];
                            }
                        } else if (key === 'Enter') {
                            e.preventDefault(); // Nunca enviar el formulario con Enter
                            if (e.shiftKey) {
                                if (rowIndex - 1 >= 0) {
                                    const prevRowInputs = matrix[rowIndex - 1].inputs;
                                    targetInput = prevRowInputs[colIndex] || prevRowInputs[prevRowInputs.length - 1];
                                }
                            } else {
                                if (rowIndex + 1 < matrix.length) {
                                    const nextRowInputs = matrix[rowIndex + 1].inputs;
                                    targetInput = nextRowInputs[colIndex] || nextRowInputs[nextRowInputs.length - 1];
                                }
                            }
                        } else if (key === 'ArrowRight' || (key === 'Tab' && !e.shiftKey)) {
                            e.preventDefault();
                            if (colIndex + 1 < rowInputs.length) {
                                targetInput = rowInputs[colIndex + 1];
                            } else if (rowIndex + 1 < matrix.length) {
                                targetInput = matrix[rowIndex + 1].inputs[0];
                            }
                        } else if (key === 'ArrowLeft' || (key === 'Tab' && e.shiftKey)) {
                            e.preventDefault();
                            if (colIndex - 1 >= 0) {
                                targetInput = rowInputs[colIndex - 1];
                            } else if (rowIndex - 1 >= 0) {
                                const prevRowInputs = matrix[rowIndex - 1].inputs;
                                targetInput = prevRowInputs[prevRowInputs.length - 1];
                            }
                        }

                        if (targetInput) {
                            targetInput.focus();
                            try { targetInput.select(); } catch (err) {}
                        }
                    });
                }

                if (salesForm) initExcelNavigation(salesForm);

                document.querySelectorAll('#sales-form input').forEach(input => {
                    input.addEventListener('input', window.recalculateSales);
                    if (input.type === 'number') input.addEventListener('change', queueAutosave);
                });

                // Recalcular inmediatamente al cargar la página para reflejar subtotales y totales correctos
                if (typeof window.recalculateSales === 'function') {
                    window.recalculateSales();
                }
            });
            </script>
        

    @endif

</div>
@endsection
