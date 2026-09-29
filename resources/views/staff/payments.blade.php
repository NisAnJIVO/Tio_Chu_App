@extends('layouts.app')

@section('title', 'Pagos a Personal')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">

    <!-- ==========================================
         CABECERA DE LA VISTA (LIQUID GLASS)
         ========================================== -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-2 border-b border-white/10">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider bg-amber-400/10 text-amber-300 border border-amber-400/30">
                    Módulo 3 — Planilla & Jornales
                </span>
                <span class="text-zinc-600 text-xs">•</span>
                <span class="text-xs text-zinc-400 font-mono">Liquidación Nocturna al Personal</span>
            </div>
            <h2 class="text-2xl font-black tracking-tight text-white">
                Pagos al Personal de Turno
            </h2>
            <p class="text-xs text-zinc-400 mt-1">
                Registro de pagos por trabajador, control de propinas/extras y conciliación de jornales nocturnos.
            </p>
        </div>

        @if($allSessions->isNotEmpty())
            <form method="GET" action="{{ route('staffPayments.index') }}" class="glass-panel p-2 rounded-2xl border border-white/10 flex items-center gap-2">
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

    @if(!$session)
        <div class="glass-panel border border-white/10 rounded-3xl p-12 text-center shadow-2xl">
            <h3 class="text-base font-bold text-white">No hay noche seleccionada</h3>
            <p class="text-xs text-zinc-400 mt-1 mb-6">Crea una noche para registrar la planilla de pagos.</p>
            <a href="{{ route('sessions.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-400 to-amber-500 text-zinc-950 text-xs font-bold rounded-xl hover:brightness-110 transition-all shadow-md shadow-amber-500/20">
                + Crear Noche
            </a>
        </div>
    @else

        <!-- ==========================================
             RESUMEN DE PAGOS DE LA NOCHE (KPIs GRANDES)
             ========================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            
            <div class="glass-card rounded-2xl p-6">
                <span class="text-xs font-mono font-semibold text-zinc-400 uppercase tracking-wider">Total Planilla ({{ $session->day_name }})</span>
                <div class="mt-2 text-3xl font-black font-mono text-white tracking-tight">
                    <span class="text-amber-400 text-lg mr-0.5 font-sans">Bs.</span>{{ number_format($totalPlanilla, 2) }}
                </div>
                <p class="text-[11px] text-zinc-500 mt-1 font-mono">{{ $attendances->count() }} trabajadores asignados</p>
            </div>

            <div class="glass-panel-elevated rounded-2xl p-6 border border-emerald-500/30">
                <span class="text-xs font-mono font-bold text-emerald-400 uppercase tracking-wider">Total Liquidado (Pagado)</span>
                <div class="mt-2 text-3xl font-black font-mono text-emerald-400 tracking-tight">
                    <span class="text-lg mr-0.5 font-sans">Bs.</span>{{ number_format($totalPagado, 2) }}
                </div>
                <p class="text-[11px] text-emerald-300/70 mt-1 font-mono">Efectivo entregado al personal</p>
            </div>

            <div class="glass-card rounded-2xl p-6">
                <span class="text-xs font-mono font-semibold text-zinc-400 uppercase tracking-wider">Pendiente por Pagar</span>
                <div class="mt-2 text-3xl font-black font-mono text-amber-400 tracking-tight">
                    <span class="text-lg mr-0.5 font-sans">Bs.</span>{{ number_format($totalPendiente, 2) }}
                </div>
                <p class="text-[11px] text-zinc-500 mt-1 font-mono">Saldo aún en caja</p>
            </div>

        </div>

        <!-- Desglose por Tipo de Personal -->
        <div class="glass-panel rounded-2xl p-6 shadow-2xl">
            <span class="text-xs font-bold uppercase tracking-wider text-zinc-200 block mb-3 font-mono">Desglose por Cargo</span>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs font-mono">
                <div class="glass-card rounded-xl p-4">
                    <span class="font-bold text-white block font-sans text-sm">Meseros & Limpieza</span>
                    <span class="text-zinc-400 block mt-1">Tarifa: Bs. 100 - 110</span>
                    <div class="mt-2 font-bold text-amber-400 text-base">
                        {{ $summaryByType['meseros']['count'] }} pers. — Bs. {{ number_format($summaryByType['meseros']['total'], 2) }}
                    </div>
                </div>

                <div class="glass-card rounded-xl p-4">
                    <span class="font-bold text-white block font-sans text-sm">Bartenders (Kelly / Ariel)</span>
                    <span class="text-zinc-400 block mt-1">Tarifa: Bs. 100 - 110</span>
                    <div class="mt-2 font-bold text-amber-400 text-base">
                        {{ $summaryByType['bartenders']['count'] }} pers. — Bs. {{ number_format($summaryByType['bartenders']['total'], 2) }}
                    </div>
                </div>

                <div class="glass-card rounded-xl p-4">
                    <span class="font-bold text-white block font-sans text-sm">Seguridad & Puerta</span>
                    <span class="text-zinc-400 block mt-1">Tarifa: Bs. 120 - 130</span>
                    <div class="mt-2 font-bold text-amber-400 text-base">
                        {{ $summaryByType['seguridad']['count'] }} pers. — Bs. {{ number_format($summaryByType['seguridad']['total'], 2) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Barra de Acciones Rápidas -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 glass-panel rounded-2xl p-5 shadow-2xl">
            @if($availableStaff->isNotEmpty())
                <form method="POST" action="{{ route('staffPayments.store') }}" class="flex items-center gap-2.5 flex-1">
                    @csrf
                    <input type="hidden" name="night_session_id" value="{{ $session->id }}">
                    <select name="staff_id" required class="glass-input text-xs rounded-xl px-3 py-2 text-zinc-100 cursor-pointer flex-1">
                        <option value="" disabled selected class="bg-[#12141c]">+ Agregar Trabajador a esta noche...</option>
                        @foreach($availableStaff as $as)
                            <option value="{{ $as->id }}" class="bg-[#12141c]">
                                {{ $as->name }} ({{ $as->role }}) — Tarifa: Bs. {{ number_format($as->getPayForDay($session->day_name), 2) }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-amber-400 to-amber-500 text-zinc-950 text-xs font-bold rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-md shadow-amber-500/20 whitespace-nowrap cursor-pointer">
                        + Agregar
                    </button>
                </form>
            @else
                <span class="text-xs text-zinc-400 font-mono">Todo el personal activo está incluido en esta noche.</span>
            @endif

            <!-- Marcar Todos como Pagados -->
            <form method="POST" action="{{ route('staffPayments.markAllPaid', $session) }}" onsubmit="return confirm('¿Marcar a todo el personal como PAGADO?');">
                @csrf
                <button type="submit" class="px-4 py-2 glass-card rounded-xl text-xs font-bold text-emerald-400 hover:text-emerald-300 transition-colors cursor-pointer">
                    ✓ Marcar Todos como Pagados
                </button>
            </form>
        </div>

        <!-- Tabla de Planilla Oficial -->
        <form method="POST" action="{{ route('staffPayments.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="night_session_id" value="{{ $session->id }}">

            <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl">
                <div class="px-6 py-4 bg-white/[0.02] border-b border-white/10 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-zinc-100 uppercase tracking-wider font-mono">
                        PLANILLA: {{ mb_strtoupper($session->day_name) }} {{ \Carbon\Carbon::parse($session->session_date)->format('d-m-Y') }}
                    </h3>
                    <span class="text-sm font-mono font-black text-amber-400">
                        TOTAL: Bs. {{ number_format($totalPlanilla, 2) }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-white/[0.04] border-b border-white/10 text-zinc-400 font-mono uppercase text-[10px] tracking-wider">
                            <tr>
                                <th class="px-4 py-3 w-12 text-center">N°</th>
                                <th class="px-4 py-3">NOMBRE</th>
                                <th class="px-4 py-3">CARGO / ÁREA</th>
                                <th class="px-4 py-3 w-36 text-right">MONTO (Bs.)</th>
                                <th class="px-4 py-3 w-28 text-center">PAGADO</th>
                                <th class="px-4 py-3 w-20 text-right">ACCIÓN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 font-mono">
                            @forelse($attendances as $index => $att)
                                <tr class="hover:bg-white/[0.02]">
                                    <td class="px-4 py-3 text-center text-zinc-500 font-bold">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-bold text-white uppercase font-sans text-sm">
                                        {{ $att->staff->name }}
                                    </td>
                                    <td class="px-4 py-3 text-zinc-400">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/5 text-zinc-300">
                                            {{ $att->staff->role }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        <input type="number" step="5" name="attendances[{{ $att->id }}][pay_amount]" value="{{ $att->pay_amount }}" min="0"
                                               class="glass-input w-28 text-right rounded-lg px-2.5 py-1 text-xs font-mono font-bold text-amber-400">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox" name="attendances[{{ $att->id }}][is_paid]" value="1" {{ $att->is_paid ? 'checked' : '' }}
                                               class="rounded border-zinc-700 bg-zinc-800 text-amber-400 focus:ring-0 w-4 h-4 cursor-pointer">
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <button type="submit" 
                                                formaction="{{ route('staffPayments.destroy', $att) }}" 
                                                formmethod="POST"
                                                onclick="return confirm('¿Quitar a {{ $att->staff->name }} de esta noche?');"
                                                class="text-rose-400 hover:text-rose-300 text-xs font-bold cursor-pointer">
                                            Quitar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-zinc-500 font-sans">
                                        No hay personal asignado a esta noche.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-white/[0.03] border-t border-white/10 font-bold text-xs text-white">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right uppercase tracking-wider font-mono">TOTAL PLANILLA:</td>
                                <td class="px-4 py-3 text-right font-mono font-black text-amber-400 text-sm">Bs. {{ number_format($totalPlanilla, 2) }}</td>
                                <td class="px-4 py-3 text-center font-mono text-emerald-400">
                                    Pagado: Bs. {{ number_format($totalPagado, 2) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="p-5 bg-white/[0.02] border-t border-white/10 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-amber-400 to-amber-500 text-zinc-950 text-xs font-black rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-amber-500/20 cursor-pointer uppercase tracking-wider">
                        Guardar Cambios de Pagos
                    </button>
                </div>
            </div>
        </form>

    @endif

</div>
@endsection
