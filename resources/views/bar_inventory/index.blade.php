@extends('layouts.app')

@section('title', 'Inventario de Barras')

@section('content')

<style>
    /* Ocultar flechas de números nativas del navegador */
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

<div class="space-y-4 max-w-7xl mx-auto w-full pb-8" id="bar-inventory-app">

    <!-- ========================================================
         1. CABECERA PRINCIPAL: TÍTULO, NOCHE & ACCIÓN DE GUARDAR
         ======================================================== -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 px-5 py-3.5 rounded-2xl theme-card border theme-border">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-white font-sans">
                Inventario de Barras
            </h1>
            <p class="text-xs text-zinc-400 mt-0.5">
                Control de stock, saldo inicial y reposición de botellas por barra
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <!-- Selector de Noche -->
            @if($allSessions->isNotEmpty())
                <form method="GET" action="{{ route('barInventory.index') }}" class="flex items-center">
                    <input type="hidden" name="bar" value="{{ $selectedBar }}">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-zinc-950 border border-zinc-800 text-xs hover:border-[#F5B81C] transition-colors">
                        <svg class="w-3.5 h-3.5 text-[#F5B81C] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                        </svg>
                        <select name="session_id" onchange="this.form.submit()" 
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

            <!-- Estado de Noche Cerrada (Discreto y sin alarmas estridentes) -->
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

            <!-- Copiar Sobrante de Noche Anterior -->
            @if($session && $previousSession && $session->isOpen())
                <form method="POST" action="{{ route('barInventory.sync') }}" onsubmit="return confirm('¿Copiar lo que sobró de la noche anterior ({{ \Carbon\Carbon::parse($previousSession->session_date)->format('d/m') }}) como saldo inicial?');">
                    @csrf
                    <input type="hidden" name="session_id" value="{{ $session->id }}">
                    <input type="hidden" name="bar_name" value="{{ $selectedBar }}">
                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-zinc-950 border border-zinc-800 text-xs font-semibold text-zinc-300 hover:text-white hover:border-zinc-700 transition-all flex items-center gap-1.5 cursor-pointer dilemo-btn">
                        <svg class="w-3.5 h-3.5 text-[#F5B81C]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>Traer Sobrante de Ayer</span>
                    </button>
                </form>
            @endif

            <!-- Botón Principal Guardar (Accesible de inmediato en cabecera) -->
            @if($session && $session->isOpen())
                <span id="inventory-autosave-status" class="text-[10px] font-mono text-zinc-500" aria-live="polite"></span>
                <button type="submit" form="bar-inventory-form" class="px-4 py-1.5 rounded-xl bg-[#F5B81C] text-black font-bold text-xs hover:bg-[#e5ac18] transition-all flex items-center gap-1.5 cursor-pointer dilemo-btn shadow-sm">
                    <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Guardar Cambios</span>
                </button>
            @endif
        </div>
    </div>

    @if(!$session)
        <!-- Estado Vacío -->
        <div class="p-12 text-center rounded-2xl theme-card border theme-border">
            <h3 class="text-sm font-bold uppercase tracking-wider text-white">No hay ninguna noche abierta o seleccionada</h3>
            <p class="text-xs text-zinc-400 mt-1 mb-4">Apertura una noche para registrar el stock de las barras.</p>
            <a href="{{ route('sessions.create') }}" class="px-4 py-2 rounded-xl bg-[#F5B81C] text-black font-bold text-xs hover:bg-[#e5ac18] transition-all dilemo-btn inline-block">
                + Aperturar Nueva Noche
            </a>
        </div>
    @else

        <!-- ========================================================
             2. SELECTOR DE BARRAS, FILTROS & BUSCADOR
             ======================================================== -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 p-2 rounded-2xl theme-card border theme-border">
            
            <!-- Selector de Barras (iOS Segmented Control) -->
            <div class="inline-flex p-1 rounded-xl bg-zinc-950 border border-zinc-800">
                @foreach($availableBars as $bar)
                    @php
                        $isKelly = str_contains($bar, 'Kelly');
                        $barLabel = $isKelly ? 'Barra Kelly' : 'Barra Ariel';
                        $barFloor = $isKelly ? 'Piso Principal' : 'Subterráneo';
                        $isSelected = $selectedBar === $bar;
                    @endphp
                    <a href="{{ route('barInventory.index', ['session_id' => $session->id, 'bar' => $bar]) }}" 
                       class="px-4 py-2 rounded-lg font-bold text-xs transition-all flex items-center gap-2 cursor-pointer {{ $isSelected ? 'bg-[#F5B81C] text-black shadow-sm' : 'text-zinc-400 hover:text-white' }}">
                        <span>{{ $barLabel }}</span>
                        <span class="text-[10px] font-medium opacity-75">({{ $barFloor }})</span>
                    </a>
                @endforeach
            </div>

            <!-- Filtros de Categoría & Buscador -->
            <div class="flex items-center gap-2.5">
                <div class="inline-flex p-1 rounded-xl bg-zinc-950 border border-zinc-800 text-xs">
                    <button type="button" 
                            id="filter-all-btn"
                            onclick="filterCategory('all', this)"
                            class="px-3 py-1.5 rounded-lg font-semibold transition-all cursor-pointer bg-[#F5B81C] text-black">
                        Todos
                    </button>
                    <button type="button" 
                            id="filter-licores-btn"
                            onclick="filterCategory('licores', this)"
                            class="px-3 py-1.5 rounded-lg font-semibold transition-all cursor-pointer text-zinc-400 hover:text-white">
                        Licores
                    </button>
                    <button type="button" 
                            id="filter-mixers-btn"
                            onclick="filterCategory('mixers', this)"
                            class="px-3 py-1.5 rounded-lg font-semibold transition-all cursor-pointer text-zinc-400 hover:text-white">
                        Gaseosas & Mixers
                    </button>
                </div>

                <div class="relative w-44 sm:w-52">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-zinc-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </span>
                    <input type="text" 
                           placeholder="Buscar bebida..." 
                           oninput="filterByName(this.value)"
                           class="w-full pl-8 pr-2.5 py-1.5 text-xs bg-zinc-950 border border-zinc-800 rounded-xl text-white placeholder-zinc-500 focus:outline-none focus:border-[#F5B81C] transition-all font-sans">
                </div>
            </div>

        </div>

        <!-- ========================================================
             3. TABLA PRINCIPAL DE INVENTARIO
             ======================================================== -->
        <form method="POST" action="{{ route('barInventory.updateBulk') }}" id="bar-inventory-form" data-ajax="true">
            @csrf
            @method('PUT')
            <input type="hidden" name="night_session_id" value="{{ $session->id }}">
            <input type="hidden" name="bar_name" value="{{ $selectedBar }}">

            <div class="rounded-2xl theme-card border theme-border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b theme-border bg-zinc-950 text-zinc-400 font-semibold uppercase tracking-wider text-[11px]">
                                <th class="py-3 px-4 w-1/4">Bebida / Presentación</th>
                                <th class="py-3 px-3 text-center w-1/5">1. Saldo de Ayer</th>
                                <th class="py-3 px-3 text-center w-1/5">2. Subido al Abrir</th>
                                <th class="py-3 px-3 text-center w-1/5">3. Reposición en Noche</th>
                                <th class="py-3 px-4 text-center w-1/6">Total Disponible</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y theme-border font-sans" id="inventory-rows-tbody">
                            
                            @php
                                $allItems = $liquorSales->merge($mixerSales);
                            @endphp

                            @forelse($allItems as $sale)
                                @php
                                    $isMixer = $sale->product && $sale->product->category === 'Mixers';
                                    $itemCategory = $isMixer ? 'mixers' : 'licores';
                                    $unitsPerPkg = $sale->product->units_per_package > 0 ? (int)$sale->product->units_per_package : 1;
                                    
                                    $initPkg = (int)$sale->initial_packages;
                                    $initUnits = (int)$sale->initial_units;
                                    $addPkg = (int)$sale->added_packages;
                                    $addUnits = (int)$sale->added_units;
                                    $nightPkg = (int)$sale->night_packages;
                                    $nightUnits = (int)$sale->night_units;

                                    $apertPkg = $initPkg + $addPkg;
                                    $apertUnits = $initUnits + $addUnits;

                                    $totNightPkg = $apertPkg + $nightPkg;
                                    $totNightUnits = $apertUnits + $nightUnits;
                                    $totNightBot = ($totNightPkg * $unitsPerPkg) + $totNightUnits;
                                    $initBot = ($initPkg * $unitsPerPkg) + $initUnits;
                                    $addBot = ($addPkg * $unitsPerPkg) + $addUnits;
                                @endphp
                                <tr class="inv-row hover:bg-zinc-900/30 transition-colors"
                                    data-row-id="{{ $sale->id }}"
                                    data-category="{{ $itemCategory }}"
                                    data-name="{{ strtolower($sale->product->name ?? '') }}"
                                    data-units-per-pkg="{{ $unitsPerPkg }}">
                                    
                                    <!-- 1. Bebida -->
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-zinc-950 border border-zinc-800 flex items-center justify-center text-zinc-300 font-bold text-xs shrink-0">
                                                {{ mb_substr($sale->product->name ?? 'B', 0, 1) }}
                                            </div>
                                            <div class="min-w-0">
                                                <span class="font-bold text-white text-xs block truncate">{{ $sale->product->name }}</span>
                                                <span class="text-[11px] text-zinc-400 font-normal block mt-0.5">
                                                    {{ $sale->product->unit }} • {{ $unitsPerPkg }} unid. x caja
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- 2. Saldo de Ayer (Lo que quedó) -->
                                    <td class="py-3 px-3 text-center">
                                        <div class="inline-flex items-center gap-2">
                                            <div class="flex flex-col items-center">
                                                <input type="number" 
                                                       name="inventory[{{ $sale->id }}][initial_packages]" 
                                                       value="{{ $initPkg }}" 
                                                       min="0" 
                                                       @disabled(!$session->isOpen())
                                                       class="input-init-pkg w-12 text-center font-mono font-semibold text-xs bg-zinc-950 border border-zinc-800 rounded-lg text-white py-1 px-1 focus:border-[#F5B81C] focus:outline-none">
                                                <span class="text-[10px] text-zinc-500 font-medium mt-0.5">Cajas</span>
                                            </div>
                                            <div class="flex flex-col items-center">
                                                <input type="number" 
                                                       name="inventory[{{ $sale->id }}][initial_units]" 
                                                       value="{{ $initUnits }}" 
                                                       min="0" 
                                                       @disabled(!$session->isOpen())
                                                       class="input-init-units w-12 text-center font-mono font-semibold text-xs bg-zinc-950 border border-zinc-800 rounded-lg text-white py-1 px-1 focus:border-[#F5B81C] focus:outline-none">
                                                <span class="text-[10px] text-zinc-500 font-medium mt-0.5">Sueltas</span>
                                            </div>
                                        </div>
                                        <span class="text-[11px] text-zinc-400 block mt-1 font-mono">
                                            = <strong class="cell-init-bot text-zinc-200 font-semibold">{{ $initBot }}</strong> bot.
                                        </span>
                                    </td>

                                    <!-- 3. Subido al Abrir (Apertura) -->
                                    <td class="py-3 px-3 text-center">
                                        <div class="inline-flex items-center gap-2">
                                            <div class="flex flex-col items-center">
                                                <input type="number" 
                                                       name="inventory[{{ $sale->id }}][added_packages]" 
                                                       value="{{ $addPkg }}" 
                                                       min="0" 
                                                       @disabled(!$session->isOpen())
                                                       class="input-add-pkg w-12 text-center font-mono font-semibold text-xs bg-zinc-950 border border-zinc-800 rounded-lg text-white py-1 px-1 focus:border-[#F5B81C] focus:outline-none">
                                                <span class="text-[10px] text-zinc-500 font-medium mt-0.5">Cajas</span>
                                            </div>
                                            <div class="flex flex-col items-center">
                                                <input type="number" 
                                                       name="inventory[{{ $sale->id }}][added_units]" 
                                                       value="{{ $addUnits }}" 
                                                       min="0" 
                                                       @disabled(!$session->isOpen())
                                                       class="input-add-units w-12 text-center font-mono font-semibold text-xs bg-zinc-950 border border-zinc-800 rounded-lg text-white py-1 px-1 focus:border-[#F5B81C] focus:outline-none">
                                                <span class="text-[10px] text-zinc-500 font-medium mt-0.5">Sueltas</span>
                                            </div>
                                        </div>
                                        <span class="text-[11px] text-zinc-400 block mt-1 font-mono">
                                            + <strong class="cell-add-bot text-zinc-200 font-semibold">{{ $addBot }}</strong> bot.
                                        </span>
                                    </td>

                                    <!-- 4. Reposición en Noche (Durante la Fiesta) -->
                                    <td class="py-3 px-3 text-center">
                                        <div class="inline-flex items-center justify-center gap-3">
                                            <!-- Cajas con Stepper sutil -->
                                            <div class="flex flex-col items-center">
                                                <div class="flex items-center gap-0.5 bg-zinc-950 border border-zinc-800 rounded-lg p-0.5">
                                                    @if($session->isOpen())
                                                        <button type="button" class="btn-step-minus w-5 h-5 rounded bg-zinc-900 hover:bg-zinc-800 text-zinc-400 hover:text-white flex items-center justify-center text-xs font-bold transition-all active:scale-95 cursor-pointer">−</button>
                                                    @endif
                                                    <input type="number" 
                                                           name="inventory[{{ $sale->id }}][night_packages]" 
                                                           value="{{ $nightPkg }}" 
                                                           min="0" 
                                                           @disabled(!$session->isOpen())
                                                           class="input-night-pkg w-8 text-center font-mono font-semibold text-xs bg-transparent border-0 text-white focus:outline-none p-0">
                                                    @if($session->isOpen())
                                                        <button type="button" class="btn-step-plus w-5 h-5 rounded bg-zinc-900 hover:bg-zinc-800 text-zinc-400 hover:text-white flex items-center justify-center text-xs font-bold transition-all active:scale-95 cursor-pointer">+</button>
                                                    @endif
                                                </div>
                                                <span class="text-[10px] text-zinc-500 font-medium mt-0.5">Cajas</span>
                                            </div>

                                            <!-- Botellas Sueltas con Stepper sutil -->
                                            <div class="flex flex-col items-center">
                                                <div class="flex items-center gap-0.5 bg-zinc-950 border border-zinc-800 rounded-lg p-0.5">
                                                    @if($session->isOpen())
                                                        <button type="button" class="btn-step-minus w-5 h-5 rounded bg-zinc-900 hover:bg-zinc-800 text-zinc-400 hover:text-white flex items-center justify-center text-xs font-bold transition-all active:scale-95 cursor-pointer">−</button>
                                                    @endif
                                                    <input type="number" 
                                                           name="inventory[{{ $sale->id }}][night_units]" 
                                                           value="{{ $nightUnits }}" 
                                                           min="0" 
                                                           @disabled(!$session->isOpen())
                                                           class="input-night-units w-8 text-center font-mono font-semibold text-xs bg-transparent border-0 text-white focus:outline-none p-0">
                                                    @if($session->isOpen())
                                                        <button type="button" class="btn-step-plus w-5 h-5 rounded bg-zinc-900 hover:bg-zinc-800 text-zinc-400 hover:text-white flex items-center justify-center text-xs font-bold transition-all active:scale-95 cursor-pointer">+</button>
                                                    @endif
                                                </div>
                                                <span class="text-[10px] text-zinc-500 font-medium mt-0.5">Sueltas</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- 5. Total Disponible para Ventas (Destacado y Claro) -->
                                    <td class="py-3 px-4 text-center">
                                        <div class="inline-flex flex-col items-center justify-center px-3.5 py-1.5 rounded-xl bg-zinc-950 border border-zinc-800 min-w-[84px]">
                                            <span class="cell-total-bot font-black font-mono text-base text-[#F5B81C]">
                                                {{ $totNightBot }}
                                            </span>
                                            <span class="text-[10px] text-zinc-400 font-mono mt-0.5">
                                                <span class="cell-total-pkg">{{ $totNightPkg }}</span> c • <span class="cell-total-units">{{ $totNightUnits }}</span> u
                                            </span>
                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-zinc-500 font-sans">
                                        No hay bebidas registradas en esta barra.
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ========================================================
                 4. PIE DE FORMULARIO ESTÁTICO (NO FLOTANTE / NO TOAST)
                 ======================================================== -->
            <div class="mt-4 p-4 rounded-2xl theme-card border theme-border flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-[#F5B81C] shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-400 block font-normal">Total botellas disponibles en {{ $selectedBar }}:</span>
                        <span class="text-base font-bold font-mono text-white" id="bar-total-sum">0 botellas</span>
                    </div>
                </div>

                @if($session->isOpen())
                    <button type="submit" 
                            class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-[#F5B81C] text-black font-bold text-xs uppercase tracking-wider hover:bg-[#e5ac18] transition-all flex items-center justify-center gap-2 dilemo-btn cursor-pointer shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Guardar Inventario de {{ $selectedBar }}</span>
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

        </form>

        <!-- ========================================================
             5. SCRIPT REACTIVO (CÁLCULO AL INSTANTE & FILTROS)
             ======================================================== -->
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rows = document.querySelectorAll('.inv-row');
            const totalSumEl = document.getElementById('bar-total-sum');
            const inventoryForm = document.getElementById('bar-inventory-form');
            const autosaveStatus = document.getElementById('inventory-autosave-status');
            let autosaveTimer = null;
            let autosaveInFlight = false;
            let autosaveQueued = false;

            function setAutosaveStatus(message, className) {
                if (!autosaveStatus) return;
                autosaveStatus.textContent = message;
                autosaveStatus.className = `text-[10px] font-mono ${className}`;
            }

            async function autosaveInventory() {
                if (!inventoryForm) return;
                if (autosaveInFlight) {
                    autosaveQueued = true;
                    return;
                }

                autosaveInFlight = true;
                setAutosaveStatus('Guardando...', 'text-[#F5B81C]');
                try {
                    const response = await fetch(inventoryForm.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: new FormData(inventoryForm)
                    });
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    setAutosaveStatus('Guardado', 'text-emerald-400');
                } catch (error) {
                    console.error('Error en autoguardado de inventario:', error);
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
                if (!inventoryForm) return;
                clearTimeout(autosaveTimer);
                setAutosaveStatus('Pendiente...', 'text-zinc-400');
                autosaveTimer = setTimeout(autosaveInventory, 450);
            }

            function updateTotalSum() {
                let grandTotal = 0;
                rows.forEach(row => {
                    if (row.style.display !== 'none') {
                        const totBotEl = row.querySelector('.cell-total-bot');
                        if (totBotEl) {
                            grandTotal += parseInt(totBotEl.textContent) || 0;
                        }
                    }
                });
                if (totalSumEl) {
                    totalSumEl.textContent = grandTotal + ' botellas';
                }
            }

            function recalculateRow(row) {
                const unitsPerPkg = parseInt(row.getAttribute('data-units-per-pkg')) || 1;
                
                // 1. Saldo de ayer
                const initPkg = parseInt(row.querySelector('.input-init-pkg')?.value) || 0;
                const initUnits = parseInt(row.querySelector('.input-init-units')?.value) || 0;
                
                // 2. Subido al abrir
                const addPkg = parseInt(row.querySelector('.input-add-pkg')?.value) || 0;
                const addUnits = parseInt(row.querySelector('.input-add-units')?.value) || 0;

                // 3. Durante la fiesta
                const nightPkg = parseInt(row.querySelector('.input-night-pkg')?.value) || 0;
                const nightUnits = parseInt(row.querySelector('.input-night-units')?.value) || 0;

                // Conteo de botellas
                const initBot = (initPkg * unitsPerPkg) + initUnits;
                const addBot = (addPkg * unitsPerPkg) + addUnits;

                // Total Apertura
                const apertPkg = initPkg + addPkg;
                const apertUnits = initUnits + addUnits;

                // Total Noche
                const totalNightPkg = apertPkg + nightPkg;
                const totalNightUnits = apertUnits + nightUnits;
                const totalNightBot = (totalNightPkg * unitsPerPkg) + totalNightUnits;

                // Actualizar textos intermedios
                const initBotEl = row.querySelector('.cell-init-bot');
                if (initBotEl) initBotEl.textContent = initBot;

                const addBotEl = row.querySelector('.cell-add-bot');
                if (addBotEl) addBotEl.textContent = addBot;

                // Actualizar total final
                const totBotEl = row.querySelector('.cell-total-bot');
                const totPkgEl = row.querySelector('.cell-total-pkg');
                const totUnitsEl = row.querySelector('.cell-total-units');

                if (totBotEl) totBotEl.textContent = totalNightBot;
                if (totPkgEl) totPkgEl.textContent = totalNightPkg;
                if (totUnitsEl) totUnitsEl.textContent = totalNightUnits;

                updateTotalSum();
            }

            rows.forEach(row => {
                row.querySelectorAll('input').forEach(input => {
                    input.addEventListener('input', () => recalculateRow(row));
                    input.addEventListener('change', queueAutosave);
                });

                row.querySelectorAll('.btn-step-plus').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const input = this.closest('div').querySelector('input');
                        if (input && !input.disabled) {
                            input.value = (parseInt(input.value) || 0) + 1;
                            input.dispatchEvent(new Event('input', { bubbles: true }));
                            recalculateRow(row);
                            queueAutosave();
                        }
                    });
                });

                row.querySelectorAll('.btn-step-minus').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const input = this.closest('div').querySelector('input');
                        if (input && !input.disabled) {
                            const val = parseInt(input.value) || 0;
                            if (val > 0) {
                                input.value = val - 1;
                                input.dispatchEvent(new Event('input', { bubbles: true }));
                                recalculateRow(row);
                                queueAutosave();
                            }
                        }
                    });
                });
            });

            // Conteo inicial
            updateTotalSum();
        });

        /* --- FILTROS DE CATEGORÍA --- */
        function filterCategory(cat, btn) {
            document.querySelectorAll('#filter-all-btn, #filter-licores-btn, #filter-mixers-btn').forEach(b => {
                b.className = 'px-3 py-1.5 rounded-lg font-semibold transition-all cursor-pointer text-zinc-400 hover:text-white';
            });
            btn.className = 'px-3 py-1.5 rounded-lg font-semibold transition-all cursor-pointer bg-[#F5B81C] text-black';

            document.querySelectorAll('.inv-row').forEach(row => {
                if (cat === 'all' || row.dataset.category === cat) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Recalcular suma total tras filtrar
            let grandTotal = 0;
            document.querySelectorAll('.inv-row').forEach(row => {
                if (row.style.display !== 'none') {
                    const totBotEl = row.querySelector('.cell-total-bot');
                    if (totBotEl) grandTotal += parseInt(totBotEl.textContent) || 0;
                }
            });
            const totalSumEl = document.getElementById('bar-total-sum');
            if (totalSumEl) totalSumEl.textContent = grandTotal + ' botellas';
        }

        /* --- FILTRO POR NOMBRE --- */
        function filterByName(q) {
            const query = q.toLowerCase().trim();
            document.querySelectorAll('.inv-row').forEach(row => {
                const name = row.dataset.name || '';
                row.style.display = (!query || name.includes(query)) ? '' : 'none';
            });

            // Recalcular suma total tras buscar
            let grandTotal = 0;
            document.querySelectorAll('.inv-row').forEach(row => {
                if (row.style.display !== 'none') {
                    const totBotEl = row.querySelector('.cell-total-bot');
                    if (totBotEl) grandTotal += parseInt(totBotEl.textContent) || 0;
                }
            });
            const totalSumEl = document.getElementById('bar-total-sum');
            if (totalSumEl) totalSumEl.textContent = grandTotal + ' botellas';
        }
        </script>

    @endif

</div>
@endsection
