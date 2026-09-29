@extends('layouts.app')

@section('title', 'Inventario de Barras & Apertura')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">

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
                <span class="text-xs text-zinc-400 font-mono">Apertura, Saldo Noche Anterior & Reposición</span>
            </div>
            <h2 class="text-2xl font-black tracking-tight text-white">
                Inventario de Barras
            </h2>
            <p class="text-xs text-zinc-400 mt-1">
                Control de botellas iniciales en cada barra: Saldo remanente de la noche anterior más reposición ingresada al inicio.
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
                <form method="POST" action="{{ route('barInventory.sync') }}" onsubmit="return confirm('¿Deseas volver a sincronizar los saldos de la noche anterior? Se reescribirá el saldo inicial.');">
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
                            <h3 class="text-xs font-bold text-zinc-100 uppercase tracking-wider font-mono">1. Licores — Apertura de Barra</h3>
                            <p class="text-[11px] text-zinc-400">
                                Saldo anterior + paquetes y unidades agregados al inicio determinan el total disponible para ventas.
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
                                    <th class="px-2 py-3 w-20 text-center" rowspan="2">Unid/Caja</th>
                                    <th class="px-3 py-2 text-center border-l border-r border-white/10 bg-white/[0.02]" colspan="2">
                                        Saldo Anterior (Noche Previa)
                                    </th>
                                    <th class="px-3 py-2 text-center border-r border-white/10 bg-amber-400/5 text-amber-300" colspan="2">
                                        + Agregar al Inicio (Reposición)
                                    </th>
                                    <th class="px-3 py-2 text-center bg-white/[0.04] text-white" colspan="3">
                                        Total Inicial Consolidado (Ventas)
                                    </th>
                                </tr>
                                <tr>
                                    <th class="px-2 py-2 w-16 text-center border-l border-white/10">Paq.</th>
                                    <th class="px-2 py-2 w-16 text-center border-r border-white/10">Unid.</th>
                                    <th class="px-2 py-2 w-20 text-center bg-amber-400/10 text-amber-400">+ Paq.</th>
                                    <th class="px-2 py-2 w-20 text-center border-r border-white/10 bg-amber-400/10 text-amber-400">+ Unid.</th>
                                    <th class="px-3 py-2 w-20 text-center text-zinc-300">Total Paq.</th>
                                    <th class="px-3 py-2 w-20 text-center text-zinc-300">Total Unid.</th>
                                    <th class="px-4 py-2 w-24 text-center font-bold text-amber-400">Total Bot.</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 font-mono">
                                @forelse($liquorSales as $index => $sale)
                                    @php
                                        $unitsPerPkg = $sale->product->units_per_package > 0 ? (int)$sale->product->units_per_package : 1;
                                    @endphp
                                    <tr class="hover:bg-white/[0.02] inv-row" 
                                        data-row-id="{{ $sale->id }}" 
                                        data-units-per-pkg="{{ $unitsPerPkg }}">
                                        <td class="px-3 py-2.5 text-center text-zinc-500 font-bold">{{ $index + 1 }}</td>
                                        <td class="px-4 py-2.5 font-bold text-white font-sans">
                                            {{ $sale->product->name }}
                                        </td>
                                        <td class="px-2 py-2.5 text-center text-zinc-400 font-mono text-[11px]">
                                            {{ $unitsPerPkg }}
                                        </td>
                                        
                                        <!-- Saldo Anterior -->
                                        <td class="px-2 py-1.5 text-center border-l border-white/5">
                                            <input type="number" name="inventory[{{ $sale->id }}][initial_packages]" value="{{ $sale->initial_packages }}" min="0"
                                                   class="glass-input w-14 text-center rounded-lg px-1.5 py-1 text-xs font-mono font-bold text-zinc-300 input-init-pkg">
                                        </td>
                                        <td class="px-2 py-1.5 text-center border-r border-white/5">
                                            <input type="number" name="inventory[{{ $sale->id }}][initial_units]" value="{{ $sale->initial_units }}" min="0"
                                                   class="glass-input w-14 text-center rounded-lg px-1.5 py-1 text-xs font-mono font-bold text-zinc-300 input-init-units">
                                        </td>

                                        <!-- Agregar al Inicio (Reposición) -->
                                        <td class="px-2 py-1.5 text-center bg-amber-400/[0.02]">
                                            <input type="number" name="inventory[{{ $sale->id }}][added_packages]" value="{{ $sale->added_packages }}" min="0"
                                                   class="glass-input w-16 text-center rounded-lg px-2 py-1 text-xs font-mono font-black text-amber-300 border-amber-400/30 input-add-pkg">
                                        </td>
                                        <td class="px-2 py-1.5 text-center border-r border-white/5 bg-amber-400/[0.02]">
                                            <input type="number" name="inventory[{{ $sale->id }}][added_units]" value="{{ $sale->added_units }}" min="0"
                                                   class="glass-input w-16 text-center rounded-lg px-2 py-1 text-xs font-mono font-black text-amber-300 border-amber-400/30 input-add-units">
                                        </td>

                                        <!-- Total Consolidado -->
                                        <td class="px-3 py-2.5 text-center font-bold text-zinc-200 cell-total-pkg">
                                            {{ $sale->packages }}
                                        </td>
                                        <td class="px-3 py-2.5 text-center font-bold text-zinc-200 cell-total-units">
                                            {{ $sale->units }}
                                        </td>
                                        <td class="px-4 py-2.5 text-center font-black text-amber-400 text-sm cell-total-bot">
                                            {{ $sale->total_initial }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="px-4 py-8 text-center text-zinc-500 font-sans">No hay licores registrados.</td>
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
                            <h3 class="text-xs font-bold text-zinc-100 uppercase tracking-wider font-mono">2. Mixers, Sodas & Aguas — Apertura de Barra</h3>
                            <p class="text-[11px] text-zinc-400">
                                Saldo de sodas de la noche anterior más sodas subidas de almacén.
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
                                    <th class="px-2 py-3 w-20 text-center" rowspan="2">Unid/Caja</th>
                                    <th class="px-3 py-2 text-center border-l border-r border-white/10 bg-white/[0.02]" colspan="2">
                                        Saldo Anterior (Noche Previa)
                                    </th>
                                    <th class="px-3 py-2 text-center border-r border-white/10 bg-amber-400/5 text-amber-300" colspan="2">
                                        + Agregar al Inicio (Reposición)
                                    </th>
                                    <th class="px-3 py-2 text-center bg-white/[0.04] text-white" colspan="3">
                                        Total Inicial Consolidado (Ventas)
                                    </th>
                                </tr>
                                <tr>
                                    <th class="px-2 py-2 w-16 text-center border-l border-white/10">Paq.</th>
                                    <th class="px-2 py-2 w-16 text-center border-r border-white/10">Unid.</th>
                                    <th class="px-2 py-2 w-20 text-center bg-amber-400/10 text-amber-400">+ Paq.</th>
                                    <th class="px-2 py-2 w-20 text-center border-r border-white/10 bg-amber-400/10 text-amber-400">+ Unid.</th>
                                    <th class="px-3 py-2 w-20 text-center text-zinc-300">Total Paq.</th>
                                    <th class="px-3 py-2 w-20 text-center text-zinc-300">Total Unid.</th>
                                    <th class="px-4 py-2 w-24 text-center font-bold text-amber-400">Total Bot.</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 font-mono">
                                @forelse($mixerSales as $index => $sale)
                                    @php
                                        $unitsPerPkg = $sale->product->units_per_package > 0 ? (int)$sale->product->units_per_package : 1;
                                    @endphp
                                    <tr class="hover:bg-white/[0.02] inv-row" 
                                        data-row-id="{{ $sale->id }}" 
                                        data-units-per-pkg="{{ $unitsPerPkg }}">
                                        <td class="px-3 py-2.5 text-center text-zinc-500 font-bold">{{ $index + 1 }}</td>
                                        <td class="px-4 py-2.5 font-bold text-white font-sans">
                                            {{ $sale->product->name }}
                                        </td>
                                        <td class="px-2 py-2.5 text-center text-zinc-400 font-mono text-[11px]">
                                            {{ $unitsPerPkg }}
                                        </td>
                                        
                                        <!-- Saldo Anterior -->
                                        <td class="px-2 py-1.5 text-center border-l border-white/5">
                                            <input type="number" name="inventory[{{ $sale->id }}][initial_packages]" value="{{ $sale->initial_packages }}" min="0"
                                                   class="glass-input w-14 text-center rounded-lg px-1.5 py-1 text-xs font-mono font-bold text-zinc-300 input-init-pkg">
                                        </td>
                                        <td class="px-2 py-1.5 text-center border-r border-white/5">
                                            <input type="number" name="inventory[{{ $sale->id }}][initial_units]" value="{{ $sale->initial_units }}" min="0"
                                                   class="glass-input w-14 text-center rounded-lg px-1.5 py-1 text-xs font-mono font-bold text-zinc-300 input-init-units">
                                        </td>

                                        <!-- Agregar al Inicio (Reposición) -->
                                        <td class="px-2 py-1.5 text-center bg-amber-400/[0.02]">
                                            <input type="number" name="inventory[{{ $sale->id }}][added_packages]" value="{{ $sale->added_packages }}" min="0"
                                                   class="glass-input w-16 text-center rounded-lg px-2 py-1 text-xs font-mono font-black text-amber-300 border-amber-400/30 input-add-pkg">
                                        </td>
                                        <td class="px-2 py-1.5 text-center border-r border-white/5 bg-amber-400/[0.02]">
                                            <input type="number" name="inventory[{{ $sale->id }}][added_units]" value="{{ $sale->added_units }}" min="0"
                                                   class="glass-input w-16 text-center rounded-lg px-2 py-1 text-xs font-mono font-black text-amber-300 border-amber-400/30 input-add-units">
                                        </td>

                                        <!-- Total Consolidado -->
                                        <td class="px-3 py-2.5 text-center font-bold text-zinc-200 cell-total-pkg">
                                            {{ $sale->packages }}
                                        </td>
                                        <td class="px-3 py-2.5 text-center font-bold text-zinc-200 cell-total-units">
                                            {{ $sale->units }}
                                        </td>
                                        <td class="px-4 py-2.5 text-center font-black text-amber-400 text-sm cell-total-bot">
                                            {{ $sale->total_initial }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="px-4 py-8 text-center text-zinc-500 font-sans">No hay mixers registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Barra de Acciones y Guardado en Liquid Glass -->
                <div class="p-6 glass-panel-elevated rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4 border border-amber-400/30">
                    <div class="text-xs font-mono text-zinc-400">
                        * Al guardar, los totales se reflejan automáticamente en el menú <a href="{{ route('sales.index', ['session_id' => $session->id, 'bar' => $selectedBar]) }}" class="text-amber-400 underline font-bold">Ventas por Barra</a>.
                    </div>
                    <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-amber-400 to-amber-500 text-zinc-950 text-xs font-black rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-xl shadow-amber-500/25 cursor-pointer uppercase tracking-wider">
                        Guardar Inventario de {{ $selectedBar }}
                    </button>
                </div>

            </div>
        </form>

        <!-- Script Reactivo para Cálculo de Totales de Apertura -->
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rows = document.querySelectorAll('.inv-row');

            function recalculateRow(row) {
                const unitsPerPkg = parseInt(row.getAttribute('data-units-per-pkg')) || 1;
                const initPkg = parseInt(row.querySelector('.input-init-pkg').value) || 0;
                const initUnits = parseInt(row.querySelector('.input-init-units').value) || 0;
                const addPkg = parseInt(row.querySelector('.input-add-pkg').value) || 0;
                const addUnits = parseInt(row.querySelector('.input-add-units').value) || 0;

                const totalPkg = initPkg + addPkg;
                const totalUnits = initUnits + addUnits;
                const totalBot = (totalPkg * unitsPerPkg) + totalUnits;

                row.querySelector('.cell-total-pkg').textContent = totalPkg;
                row.querySelector('.cell-total-units').textContent = totalUnits;
                row.querySelector('.cell-total-bot').textContent = totalBot;
            }

            rows.forEach(row => {
                row.querySelectorAll('input').forEach(input => {
                    input.addEventListener('input', () => recalculateRow(row));
                });
            });
        });
        </script>

    @endif

</div>
@endsection
