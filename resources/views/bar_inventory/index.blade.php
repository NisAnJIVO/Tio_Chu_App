@extends('layouts.app')

@section('title', 'Inventario de Barras & Apertura')

@section('content')
<div class="space-y-8 max-w-full mx-auto">

    <!-- ==========================================
         CABECERA DE LA VISTA
         ========================================== -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-2 border-b border-white/10">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider bg-amber-400/10 text-amber-300 border border-amber-400/30">
                    Bodega & Licores — Control de Barras
                </span>
                <span class="text-zinc-600 text-xs">•</span>
                <span class="text-xs text-zinc-400 font-mono">Apertura, Noche Previa & Reposición Durante Noche</span>
            </div>
            <h2 class="text-2xl font-black tracking-tight text-white">
                Inventario de Barras
            </h2>
            <p class="text-xs text-zinc-400 mt-1">
                Control de botellas en cada barra: Saldo remanente + reposición inicial + reposición durante la noche. El <span class="text-amber-400 font-semibold">Total Noche</span> alimenta automáticamente el inicio de Ventas por Barra.
            </p>
        </div>

        <!-- Selector de Noche & Acciones -->
        <div class="flex flex-wrap items-center gap-3">
            @if($allSessions->isNotEmpty())
                <form method="GET" action="{{ route('barInventory.index') }}" class="glass-panel p-2 rounded-2xl border border-white/10 flex items-center gap-2">
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

            @if($session && $previousSession)
                <form method="POST" action="{{ route('barInventory.sync') }}" onsubmit="return confirm('¿Deseas volver a sincronizar los saldos de la noche anterior? Se actualizará el saldo inicial.');">
                    @csrf
                    <input type="hidden" name="session_id" value="{{ $session->id }}">
                    <input type="hidden" name="bar_name" value="{{ $selectedBar }}">
                    <button type="submit" class="px-3.5 py-2.5 glass-card rounded-xl text-xs font-bold text-amber-300 hover:text-white hover:bg-amber-400/20 border border-amber-400/30 transition-all flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Sincronizar Noche Anterior ({{ \Carbon\Carbon::parse($previousSession->session_date)->format('d/m') }})</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(!$session)
        <div class="glass-panel border border-white/10 rounded-3xl p-12 text-center shadow-2xl">
            <h3 class="text-base font-bold text-white">No hay noche seleccionada</h3>
            <p class="text-xs text-zinc-400 mt-1 mb-6">Crea una noche para gestionar el inventario de barras.</p>
            <a href="{{ route('sessions.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-400 to-amber-500 text-zinc-950 text-xs font-bold rounded-xl hover:brightness-110 transition-all shadow-md shadow-amber-500/20">
                + Crear Noche
            </a>
        </div>
    @else

        <!-- Selector de Barra (Pills: Barra Kelly, Barra Ariel) -->
        <div class="flex items-center gap-2 glass-panel p-2 rounded-2xl border border-white/10 text-xs font-mono">
            @foreach($availableBars as $bar)
                <a href="{{ route('barInventory.index', ['session_id' => $session->id, 'bar' => $bar]) }}" 
                   class="px-5 py-2 rounded-xl font-bold transition-all {{ $selectedBar === $bar ? 'bg-amber-400 text-zinc-950 shadow-md shadow-amber-400/20' : 'text-zinc-300 hover:text-white hover:bg-white/5' }}">
                    {{ $bar }}
                </a>
            @endforeach
        </div>

        <!-- Formulario Principal de Inventario -->
        <form method="POST" action="{{ route('barInventory.updateBulk') }}" id="inventory-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="night_session_id" value="{{ $session->id }}">
            <input type="hidden" name="bar_name" value="{{ $selectedBar }}">

            <div class="space-y-6">

                <!-- 1. TABLA DE LICORES -->
                <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl">
                    <div class="px-6 py-4 bg-white/[0.02] border-b border-white/10 flex items-center justify-between">
                        <div>
                            <h3 class="text-xs font-bold text-zinc-100 uppercase tracking-wider font-mono">1. Licores — Apertura & Reposición en Turno</h3>
                            <p class="text-[11px] text-zinc-400">
                                Saldo anterior + agregado al inicio + agregado durante la noche = <span class="text-amber-300 font-bold">Total Noche</span> (se refleja en Ventas por Barra).
                            </p>
                        </div>
                        <span class="text-xs font-mono font-bold text-amber-400">
                            {{ $liquorSales->count() }} Licores en {{ $selectedBar }}
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left" id="table-inv-liquors">
                            <thead class="bg-white/[0.04] border-b border-white/10 text-zinc-400 font-mono uppercase text-[10px] tracking-wider">
                                <tr>
                                    <th class="px-3 py-3 w-8 text-center" rowspan="2">N°</th>
                                    <th class="px-4 py-3" rowspan="2">Licor</th>
                                    <th class="px-2 py-3 w-16 text-center" rowspan="2">Unid/Caja</th>
                                    
                                    <!-- Saldo Anterior -->
                                    <th class="px-3 py-2 text-center border-l border-r border-white/10 bg-white/[0.02]" colspan="2">
                                        Saldo Anterior (Noche Previa)
                                    </th>
                                    
                                    <!-- + Agregar al Inicio -->
                                    <th class="px-3 py-2 text-center border-r border-white/10 bg-amber-400/5 text-amber-300" colspan="2">
                                        + Agregar al Inicio (Apertura)
                                    </th>
                                    
                                    <!-- Total Apertura -->
                                    <th class="px-3 py-2 text-center border-r border-white/10 bg-white/[0.04] text-zinc-300" colspan="3">
                                        Total Apertura
                                    </th>

                                    <!-- + Durante Noche -->
                                    <th class="px-3 py-2 text-center border-r border-white/10 bg-emerald-500/10 text-emerald-300" colspan="2">
                                        + Durante Noche (Reposición)
                                    </th>

                                    <!-- Total Noche -->
                                    <th class="px-3 py-2 text-center bg-amber-400/10 text-amber-300 font-bold" colspan="3">
                                        Total Noche (Disponible Ventas)
                                    </th>
                                </tr>
                                <tr>
                                    <!-- Saldo Anterior -->
                                    <th class="px-2 py-2 w-14 text-center border-l border-white/10 text-zinc-400">Paq.</th>
                                    <th class="px-2 py-2 w-14 text-center border-r border-white/10 text-zinc-400">Unid.</th>
                                    
                                    <!-- Agregar al Inicio -->
                                    <th class="px-2 py-2 w-16 text-center bg-amber-400/10 text-amber-400">+ Paq.</th>
                                    <th class="px-2 py-2 w-16 text-center border-r border-white/10 bg-amber-400/10 text-amber-400">+ Unid.</th>
                                    
                                    <!-- Total Apertura -->
                                    <th class="px-2 py-2 w-16 text-center text-zinc-400">Paq.</th>
                                    <th class="px-2 py-2 w-16 text-center text-zinc-400">Unid.</th>
                                    <th class="px-2 py-2 w-18 text-center border-r border-white/10 text-zinc-300 font-bold">Total Bot.</th>

                                    <!-- + Durante Noche -->
                                    <th class="px-2 py-2 w-28 text-center bg-emerald-500/15 text-emerald-300">+ / − Paq.</th>
                                    <th class="px-2 py-2 w-28 text-center border-r border-white/10 bg-emerald-500/15 text-emerald-300">+ / − Unid.</th>

                                    <!-- Total Noche -->
                                    <th class="px-2 py-2 w-16 text-center bg-amber-400/5 text-amber-300 font-bold">Total Paq.</th>
                                    <th class="px-2 py-2 w-16 text-center bg-amber-400/5 text-amber-300 font-bold">Total Unid.</th>
                                    <th class="px-3 py-2 w-20 text-center bg-amber-400/10 text-amber-400 font-black">Total Bot.</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 font-mono">
                                @forelse($liquorSales as $index => $sale)
                                    @php
                                        $unitsPerPkg = $sale->product->units_per_package > 0 ? (int)$sale->product->units_per_package : 1;
                                        $initPkg = (int)$sale->initial_packages;
                                        $initUnits = (int)$sale->initial_units;
                                        $addPkg = (int)$sale->added_packages;
                                        $addUnits = (int)$sale->added_units;
                                        $nightPkg = (int)$sale->night_packages;
                                        $nightUnits = (int)$sale->night_units;

                                        $apertPkg = $initPkg + $addPkg;
                                        $apertUnits = $initUnits + $addUnits;
                                        $apertBot = ($apertPkg * $unitsPerPkg) + $apertUnits;

                                        $totNightPkg = $apertPkg + $nightPkg;
                                        $totNightUnits = $apertUnits + $nightUnits;
                                        $totNightBot = ($totNightPkg * $unitsPerPkg) + $totNightUnits;
                                    @endphp
                                    <tr class="hover:bg-white/[0.02] inv-row" 
                                        data-row-id="{{ $sale->id }}" 
                                        data-units-per-pkg="{{ $unitsPerPkg }}">
                                        <td class="px-3 py-2.5 text-center text-zinc-500 font-bold">{{ $index + 1 }}</td>
                                        <td class="px-4 py-2.5 font-bold text-white font-sans whitespace-nowrap">
                                            {{ $sale->product->name }}
                                        </td>
                                        <td class="px-2 py-2.5 text-center text-zinc-400 font-mono text-[11px]">
                                            {{ $unitsPerPkg }}
                                        </td>
                                        
                                        <!-- Saldo Anterior -->
                                        <td class="px-1.5 py-1.5 text-center border-l border-white/5">
                                            <input type="number" name="inventory[{{ $sale->id }}][initial_packages]" value="{{ $initPkg }}" min="0"
                                                   class="glass-input w-12 text-center rounded-lg px-1 py-1 text-xs font-mono font-bold text-zinc-300 input-init-pkg">
                                        </td>
                                        <td class="px-1.5 py-1.5 text-center border-r border-white/5">
                                            <input type="number" name="inventory[{{ $sale->id }}][initial_units]" value="{{ $initUnits }}" min="0"
                                                   class="glass-input w-12 text-center rounded-lg px-1 py-1 text-xs font-mono font-bold text-zinc-300 input-init-units">
                                        </td>

                                        <!-- Agregar al Inicio (Apertura) -->
                                        <td class="px-1.5 py-1.5 text-center bg-amber-400/[0.02]">
                                            <input type="number" name="inventory[{{ $sale->id }}][added_packages]" value="{{ $addPkg }}" min="0"
                                                   class="glass-input w-14 text-center rounded-lg px-1.5 py-1 text-xs font-mono font-black text-amber-300 border-amber-400/30 input-add-pkg">
                                        </td>
                                        <td class="px-1.5 py-1.5 text-center border-r border-white/5 bg-amber-400/[0.02]">
                                            <input type="number" name="inventory[{{ $sale->id }}][added_units]" value="{{ $addUnits }}" min="0"
                                                   class="glass-input w-14 text-center rounded-lg px-1.5 py-1 text-xs font-mono font-black text-amber-300 border-amber-400/30 input-add-units">
                                        </td>

                                        <!-- Total Apertura -->
                                        <td class="px-2 py-2.5 text-center font-bold text-zinc-400 cell-apert-pkg">
                                            {{ $apertPkg }}
                                        </td>
                                        <td class="px-2 py-2.5 text-center font-bold text-zinc-400 cell-apert-units">
                                            {{ $apertUnits }}
                                        </td>
                                        <td class="px-2 py-2.5 text-center font-bold text-zinc-300 border-r border-white/5 cell-apert-bot">
                                            {{ $apertBot }}
                                        </td>

                                        <!-- + Durante Noche (con botones +/- interactivos) -->
                                        <td class="px-2 py-1.5 text-center bg-emerald-500/[0.04]">
                                            <div class="flex items-center justify-center gap-1">
                                                <button type="button" class="btn-step-minus w-6 h-6 rounded-md bg-white/10 hover:bg-red-500/20 active:scale-90 text-zinc-300 hover:text-red-300 font-black text-sm flex items-center justify-center transition-all cursor-pointer select-none">−</button>
                                                <input type="number" name="inventory[{{ $sale->id }}][night_packages]" value="{{ $nightPkg }}" min="0"
                                                       class="glass-input w-12 text-center rounded-lg px-1 py-1 text-xs font-mono font-black text-emerald-300 border-emerald-500/30 input-night-pkg">
                                                <button type="button" class="btn-step-plus w-6 h-6 rounded-md bg-emerald-500/20 hover:bg-emerald-500/30 active:scale-90 text-emerald-300 font-black text-sm flex items-center justify-center transition-all cursor-pointer select-none">+</button>
                                            </div>
                                        </td>
                                        <td class="px-2 py-1.5 text-center border-r border-white/5 bg-emerald-500/[0.04]">
                                            <div class="flex items-center justify-center gap-1">
                                                <button type="button" class="btn-step-minus w-6 h-6 rounded-md bg-white/10 hover:bg-red-500/20 active:scale-90 text-zinc-300 hover:text-red-300 font-black text-sm flex items-center justify-center transition-all cursor-pointer select-none">−</button>
                                                <input type="number" name="inventory[{{ $sale->id }}][night_units]" value="{{ $nightUnits }}" min="0"
                                                       class="glass-input w-12 text-center rounded-lg px-1 py-1 text-xs font-mono font-black text-emerald-300 border-emerald-500/30 input-night-units">
                                                <button type="button" class="btn-step-plus w-6 h-6 rounded-md bg-emerald-500/20 hover:bg-emerald-500/30 active:scale-90 text-emerald-300 font-black text-sm flex items-center justify-center transition-all cursor-pointer select-none">+</button>
                                            </div>
                                        </td>

                                        <!-- Total Noche (Disponible Ventas) -->
                                        <td class="px-2 py-2.5 text-center font-bold text-amber-200 bg-amber-400/[0.02] cell-total-pkg">
                                            {{ $totNightPkg }}
                                        </td>
                                        <td class="px-2 py-2.5 text-center font-bold text-amber-200 bg-amber-400/[0.02] cell-total-units">
                                            {{ $totNightUnits }}
                                        </td>
                                        <td class="px-3 py-2.5 text-center font-black text-amber-400 text-sm bg-amber-400/10 cell-total-bot">
                                            {{ $totNightBot }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="15" class="px-4 py-8 text-center text-zinc-500 font-sans">No hay licores registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. TABLA DE MIXERS & SODAS -->
                <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl">
                    <div class="px-6 py-4 bg-white/[0.02] border-b border-white/10 flex items-center justify-between">
                        <div>
                            <h3 class="text-xs font-bold text-zinc-100 uppercase tracking-wider font-mono">2. Mixers, Sodas & Aguas — Apertura & Reposición en Turno</h3>
                            <p class="text-[11px] text-zinc-400">
                                Saldo de sodas de la noche previa + sodas subidas al inicio + sodas subidas durante el turno.
                            </p>
                        </div>
                        <span class="text-xs font-mono font-bold text-amber-400">
                            {{ $mixerSales->count() }} Mixers en {{ $selectedBar }}
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left" id="table-inv-mixers">
                            <thead class="bg-white/[0.04] border-b border-white/10 text-zinc-400 font-mono uppercase text-[10px] tracking-wider">
                                <tr>
                                    <th class="px-3 py-3 w-8 text-center" rowspan="2">N°</th>
                                    <th class="px-4 py-3" rowspan="2">Mixer / Soda</th>
                                    <th class="px-2 py-3 w-16 text-center" rowspan="2">Unid/Caja</th>
                                    
                                    <!-- Saldo Anterior -->
                                    <th class="px-3 py-2 text-center border-l border-r border-white/10 bg-white/[0.02]" colspan="2">
                                        Saldo Anterior (Noche Previa)
                                    </th>
                                    
                                    <!-- + Agregar al Inicio -->
                                    <th class="px-3 py-2 text-center border-r border-white/10 bg-amber-400/5 text-amber-300" colspan="2">
                                        + Agregar al Inicio (Apertura)
                                    </th>
                                    
                                    <!-- Total Apertura -->
                                    <th class="px-3 py-2 text-center border-r border-white/10 bg-white/[0.04] text-zinc-300" colspan="3">
                                        Total Apertura
                                    </th>

                                    <!-- + Durante Noche -->
                                    <th class="px-3 py-2 text-center border-r border-white/10 bg-emerald-500/10 text-emerald-300" colspan="2">
                                        + Durante Noche (Reposición)
                                    </th>

                                    <!-- Total Noche -->
                                    <th class="px-3 py-2 text-center bg-amber-400/10 text-amber-300 font-bold" colspan="3">
                                        Total Noche (Disponible Ventas)
                                    </th>
                                </tr>
                                <tr>
                                    <!-- Saldo Anterior -->
                                    <th class="px-2 py-2 w-14 text-center border-l border-white/10 text-zinc-400">Paq.</th>
                                    <th class="px-2 py-2 w-14 text-center border-r border-white/10 text-zinc-400">Unid.</th>
                                    
                                    <!-- Agregar al Inicio -->
                                    <th class="px-2 py-2 w-16 text-center bg-amber-400/10 text-amber-400">+ Paq.</th>
                                    <th class="px-2 py-2 w-16 text-center border-r border-white/10 bg-amber-400/10 text-amber-400">+ Unid.</th>
                                    
                                    <!-- Total Apertura -->
                                    <th class="px-2 py-2 w-16 text-center text-zinc-400">Paq.</th>
                                    <th class="px-2 py-2 w-16 text-center text-zinc-400">Unid.</th>
                                    <th class="px-2 py-2 w-18 text-center border-r border-white/10 text-zinc-300 font-bold">Total Bot.</th>

                                    <!-- + Durante Noche -->
                                    <th class="px-2 py-2 w-28 text-center bg-emerald-500/15 text-emerald-300">+ / − Paq.</th>
                                    <th class="px-2 py-2 w-28 text-center border-r border-white/10 bg-emerald-500/15 text-emerald-300">+ / − Unid.</th>

                                    <!-- Total Noche -->
                                    <th class="px-2 py-2 w-16 text-center bg-amber-400/5 text-amber-300 font-bold">Total Paq.</th>
                                    <th class="px-2 py-2 w-16 text-center bg-amber-400/5 text-amber-300 font-bold">Total Unid.</th>
                                    <th class="px-3 py-2 w-20 text-center bg-amber-400/10 text-amber-400 font-black">Total Bot.</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 font-mono">
                                @forelse($mixerSales as $index => $sale)
                                    @php
                                        $unitsPerPkg = $sale->product->units_per_package > 0 ? (int)$sale->product->units_per_package : 1;
                                        $initPkg = (int)$sale->initial_packages;
                                        $initUnits = (int)$sale->initial_units;
                                        $addPkg = (int)$sale->added_packages;
                                        $addUnits = (int)$sale->added_units;
                                        $nightPkg = (int)$sale->night_packages;
                                        $nightUnits = (int)$sale->night_units;

                                        $apertPkg = $initPkg + $addPkg;
                                        $apertUnits = $initUnits + $addUnits;
                                        $apertBot = ($apertPkg * $unitsPerPkg) + $apertUnits;

                                        $totNightPkg = $apertPkg + $nightPkg;
                                        $totNightUnits = $apertUnits + $nightUnits;
                                        $totNightBot = ($totNightPkg * $unitsPerPkg) + $totNightUnits;
                                    @endphp
                                    <tr class="hover:bg-white/[0.02] inv-row" 
                                        data-row-id="{{ $sale->id }}" 
                                        data-units-per-pkg="{{ $unitsPerPkg }}">
                                        <td class="px-3 py-2.5 text-center text-zinc-500 font-bold">{{ $index + 1 }}</td>
                                        <td class="px-4 py-2.5 font-bold text-white font-sans whitespace-nowrap">
                                            {{ $sale->product->name }}
                                        </td>
                                        <td class="px-2 py-2.5 text-center text-zinc-400 font-mono text-[11px]">
                                            {{ $unitsPerPkg }}
                                        </td>
                                        
                                        <!-- Saldo Anterior -->
                                        <td class="px-1.5 py-1.5 text-center border-l border-white/5">
                                            <input type="number" name="inventory[{{ $sale->id }}][initial_packages]" value="{{ $initPkg }}" min="0"
                                                   class="glass-input w-12 text-center rounded-lg px-1 py-1 text-xs font-mono font-bold text-zinc-300 input-init-pkg">
                                        </td>
                                        <td class="px-1.5 py-1.5 text-center border-r border-white/5">
                                            <input type="number" name="inventory[{{ $sale->id }}][initial_units]" value="{{ $initUnits }}" min="0"
                                                   class="glass-input w-12 text-center rounded-lg px-1 py-1 text-xs font-mono font-bold text-zinc-300 input-init-units">
                                        </td>

                                        <!-- Agregar al Inicio (Apertura) -->
                                        <td class="px-1.5 py-1.5 text-center bg-amber-400/[0.02]">
                                            <input type="number" name="inventory[{{ $sale->id }}][added_packages]" value="{{ $addPkg }}" min="0"
                                                   class="glass-input w-14 text-center rounded-lg px-1.5 py-1 text-xs font-mono font-black text-amber-300 border-amber-400/30 input-add-pkg">
                                        </td>
                                        <td class="px-1.5 py-1.5 text-center border-r border-white/5 bg-amber-400/[0.02]">
                                            <input type="number" name="inventory[{{ $sale->id }}][added_units]" value="{{ $addUnits }}" min="0"
                                                   class="glass-input w-14 text-center rounded-lg px-1.5 py-1 text-xs font-mono font-black text-amber-300 border-amber-400/30 input-add-units">
                                        </td>

                                        <!-- Total Apertura -->
                                        <td class="px-2 py-2.5 text-center font-bold text-zinc-400 cell-apert-pkg">
                                            {{ $apertPkg }}
                                        </td>
                                        <td class="px-2 py-2.5 text-center font-bold text-zinc-400 cell-apert-units">
                                            {{ $apertUnits }}
                                        </td>
                                        <td class="px-2 py-2.5 text-center font-bold text-zinc-300 border-r border-white/5 cell-apert-bot">
                                            {{ $apertBot }}
                                        </td>

                                        <!-- + Durante Noche (con botones +/- interactivos) -->
                                        <td class="px-2 py-1.5 text-center bg-emerald-500/[0.04]">
                                            <div class="flex items-center justify-center gap-1">
                                                <button type="button" class="btn-step-minus w-6 h-6 rounded-md bg-white/10 hover:bg-red-500/20 active:scale-90 text-zinc-300 hover:text-red-300 font-black text-sm flex items-center justify-center transition-all cursor-pointer select-none">−</button>
                                                <input type="number" name="inventory[{{ $sale->id }}][night_packages]" value="{{ $nightPkg }}" min="0"
                                                       class="glass-input w-12 text-center rounded-lg px-1 py-1 text-xs font-mono font-black text-emerald-300 border-emerald-500/30 input-night-pkg">
                                                <button type="button" class="btn-step-plus w-6 h-6 rounded-md bg-emerald-500/20 hover:bg-emerald-500/30 active:scale-90 text-emerald-300 font-black text-sm flex items-center justify-center transition-all cursor-pointer select-none">+</button>
                                            </div>
                                        </td>
                                        <td class="px-2 py-1.5 text-center border-r border-white/5 bg-emerald-500/[0.04]">
                                            <div class="flex items-center justify-center gap-1">
                                                <button type="button" class="btn-step-minus w-6 h-6 rounded-md bg-white/10 hover:bg-red-500/20 active:scale-90 text-zinc-300 hover:text-red-300 font-black text-sm flex items-center justify-center transition-all cursor-pointer select-none">−</button>
                                                <input type="number" name="inventory[{{ $sale->id }}][night_units]" value="{{ $nightUnits }}" min="0"
                                                       class="glass-input w-12 text-center rounded-lg px-1 py-1 text-xs font-mono font-black text-emerald-300 border-emerald-500/30 input-night-units">
                                                <button type="button" class="btn-step-plus w-6 h-6 rounded-md bg-emerald-500/20 hover:bg-emerald-500/30 active:scale-90 text-emerald-300 font-black text-sm flex items-center justify-center transition-all cursor-pointer select-none">+</button>
                                            </div>
                                        </td>

                                        <!-- Total Noche (Disponible Ventas) -->
                                        <td class="px-2 py-2.5 text-center font-bold text-amber-200 bg-amber-400/[0.02] cell-total-pkg">
                                            {{ $totNightPkg }}
                                        </td>
                                        <td class="px-2 py-2.5 text-center font-bold text-amber-200 bg-amber-400/[0.02] cell-total-units">
                                            {{ $totNightUnits }}
                                        </td>
                                        <td class="px-3 py-2.5 text-center font-black text-amber-400 text-sm bg-amber-400/10 cell-total-bot">
                                            {{ $totNightBot }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="15" class="px-4 py-8 text-center text-zinc-500 font-sans">No hay mixers registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Barra de Acciones y Guardado en Liquid Glass -->
                <div class="p-6 glass-panel-elevated rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4 border border-amber-400/30">
                    <div class="text-xs font-mono text-zinc-400">
                        * Al presionar guardar, los valores de <span class="text-amber-300 font-bold">Total Noche</span> se autocompletan directamente en <a href="{{ route('sales.index', ['session_id' => $session->id, 'bar' => $selectedBar]) }}" class="text-amber-400 underline font-bold">Ventas por Barra</a>.
                    </div>
                    <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-amber-400 to-amber-500 text-zinc-950 text-xs font-black rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-xl shadow-amber-500/25 cursor-pointer uppercase tracking-wider">
                        Guardar Inventario de {{ $selectedBar }}
                    </button>
                </div>

            </div>
        </form>

        <!-- Script Reactivo para Cálculo de Totales de Apertura y Durante Noche -->
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rows = document.querySelectorAll('.inv-row');

            function recalculateRow(row) {
                const unitsPerPkg = parseInt(row.getAttribute('data-units-per-pkg')) || 1;
                
                // 1. Saldo inicial
                const initPkg = parseInt(row.querySelector('.input-init-pkg').value) || 0;
                const initUnits = parseInt(row.querySelector('.input-init-units').value) || 0;
                
                // 2. Agregar al inicio
                const addPkg = parseInt(row.querySelector('.input-add-pkg').value) || 0;
                const addUnits = parseInt(row.querySelector('.input-add-units').value) || 0;

                // 3. Durante noche
                const nightPkg = parseInt(row.querySelector('.input-night-pkg').value) || 0;
                const nightUnits = parseInt(row.querySelector('.input-night-units').value) || 0;

                // Totales de apertura
                const apertPkg = initPkg + addPkg;
                const apertUnits = initUnits + addUnits;
                const apertBot = (apertPkg * unitsPerPkg) + apertUnits;

                // Totales de noche (apertura + durante noche)
                const totalNightPkg = apertPkg + nightPkg;
                const totalNightUnits = apertUnits + nightUnits;
                const totalNightBot = (totalNightPkg * unitsPerPkg) + totalNightUnits;

                // Actualizar celdas de apertura
                const apertPkgEl = row.querySelector('.cell-apert-pkg');
                const apertUnitsEl = row.querySelector('.cell-apert-units');
                const apertBotEl = row.querySelector('.cell-apert-bot');
                if (apertPkgEl) apertPkgEl.textContent = apertPkg;
                if (apertUnitsEl) apertUnitsEl.textContent = apertUnits;
                if (apertBotEl) apertBotEl.textContent = apertBot;

                // Actualizar celdas de total noche
                const totPkgEl = row.querySelector('.cell-total-pkg');
                const totUnitsEl = row.querySelector('.cell-total-units');
                const totBotEl = row.querySelector('.cell-total-bot');
                if (totPkgEl) totPkgEl.textContent = totalNightPkg;
                if (totUnitsEl) totUnitsEl.textContent = totalNightUnits;
                if (totBotEl) totBotEl.textContent = totalNightBot;
            }

            rows.forEach(row => {
                // Inputs manuales
                row.querySelectorAll('input').forEach(input => {
                    input.addEventListener('input', () => recalculateRow(row));
                });

                // Botones de incremento / decremento (+ / -)
                row.querySelectorAll('.btn-step-plus').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const input = this.closest('div').querySelector('input');
                        if (input) {
                            input.value = (parseInt(input.value) || 0) + 1;
                            input.dispatchEvent(new Event('input', { bubbles: true }));
                            recalculateRow(row);
                        }
                    });
                });

                row.querySelectorAll('.btn-step-minus').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const input = this.closest('div').querySelector('input');
                        if (input) {
                            const val = parseInt(input.value) || 0;
                            if (val > 0) {
                                input.value = val - 1;
                                input.dispatchEvent(new Event('input', { bubbles: true }));
                                recalculateRow(row);
                            }
                        }
                    });
                });
            });
        });
        </script>

    @endif

</div>
@endsection
