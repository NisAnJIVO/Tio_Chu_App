@extends('layouts.app')

@section('title', 'Historial de Pagos y Deudas del Personal')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">

    <!-- ==========================================
         CABECERA DE LA VISTA (LIQUID GLASS / iOS)
         ========================================== -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-2 border-b border-white/10">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider bg-amber-400/10 text-amber-300 border border-amber-400/30">
                    Módulo 3 — Auditoría & Planilla
                </span>
                <span class="text-zinc-600 text-xs">•</span>
                <span class="text-xs text-zinc-400 font-mono">Control de Deudas y Saldos Pendientes</span>
            </div>
            <h2 class="text-2xl font-black tracking-tight text-white">
                Historial de Pagos y Deudas del Personal
            </h2>
            <p class="text-xs text-zinc-400 mt-1">
                Monitoreo nocturno ordenado por nivel de deuda: trabajadores con pagos pendientes primero y liquidaciones en cero al final.
            </p>
        </div>

        @if($allSessions->isNotEmpty())
            <form method="GET" action="{{ route('paymentHistory.index') }}" class="glass-panel p-2 rounded-2xl border border-white/10 flex items-center gap-2">
                <label for="session_id" class="text-xs text-zinc-400 font-medium pl-1">Noche:</label>
                <select name="session_id" id="session_id" onchange="this.form.submit()" 
                        class="glass-input text-xs font-mono font-bold rounded-xl px-2.5 py-1.5 text-zinc-100 cursor-pointer">
                    @foreach($allSessions as $s)
                        <option value="{{ $s->id }}" {{ $session && $session->id === $s->id ? 'selected' : '' }} class="bg-[#12141c] text-zinc-100">
                            {{ $s->day_name }} {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }} ({{ $s->status === 'open' ? 'Abierta' : 'Cerrada' }})
                        </option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    @if(!$session)
        <div class="glass-panel border border-white/10 rounded-3xl p-12 text-center shadow-2xl">
            <h3 class="text-base font-bold text-white">No hay jornadas registradas</h3>
            <p class="text-xs text-zinc-400 mt-1 mb-6">Apertura una noche para consultar el historial de pagos y deudas.</p>
            <a href="{{ route('sessions.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-400 to-amber-500 text-zinc-950 text-xs font-bold rounded-xl hover:brightness-110 transition-all shadow-md shadow-amber-500/20">
                + Crear Noche
            </a>
        </div>
    @else

        <!-- ==========================================
             ESTADO DE LA NOCHE & BOTÓN DIRECTO
             ========================================== -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 glass-panel rounded-2xl p-4 border border-white/10 shadow-xl">
            <div class="flex items-center gap-3">
                <span class="w-2.5 h-2.5 rounded-full {{ $session->isOpen() ? 'bg-emerald-400 animate-pulse' : 'bg-zinc-500' }}"></span>
                <div>
                    <h4 class="text-xs font-mono font-black uppercase tracking-wider text-white">
                        JORNADA: {{ mb_strtoupper($session->day_name) }} {{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}
                    </h4>
                    <p class="text-[11px] text-zinc-400">
                        Estado: <strong class="{{ $session->isOpen() ? 'text-emerald-400' : 'text-zinc-400' }}">{{ $session->isOpen() ? 'Noche Abierta' : 'Noche Cerrada' }}</strong>
                        • Arqueo y jornales registrados.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('staffPayments.index', ['session_id' => $session->id]) }}" 
                   class="px-3.5 py-1.5 glass-card rounded-xl text-xs font-bold text-amber-400 hover:text-amber-300 transition-colors flex items-center gap-1.5 font-mono">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/>
                    </svg>
                    Ir a Pagos de Esta Noche &rarr;
                </a>
            </div>
        </div>

        <!-- ==========================================
             TARJETAS KPI RESUMEN DE LA NOCHE
             ========================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            
            <!-- Deuda Pendiente de Esta Noche -->
            <div class="glass-card rounded-2xl p-5 border {{ $totalDeuda > 0 ? 'border-rose-500/30 bg-rose-500/5' : 'border-white/10' }}">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">Deuda Pendiente</span>
                    @if($totalDeuda > 0)
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-mono font-bold uppercase bg-rose-500/20 text-rose-300 border border-rose-500/30">
                            Por Pagar
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-mono font-bold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            Al Día
                        </span>
                    @endif
                </div>
                <div class="mt-2 text-2xl font-black font-mono {{ $totalDeuda > 0 ? 'text-rose-400' : 'text-emerald-400' }} tracking-tight">
                    <span class="text-sm mr-0.5 font-sans">Bs.</span>{{ number_format($totalDeuda, 2) }}
                </div>
                <p class="text-[11px] text-zinc-400 mt-1 font-mono">
                    {{ $conDeudaCount }} de {{ $totalTrabajadores }} trabajadores con deuda
                </p>
            </div>

            <!-- Total Liquidado (Pagado) -->
            <div class="glass-card rounded-2xl p-5 border border-emerald-500/20 bg-emerald-500/5">
                <span class="text-[10px] font-mono font-bold text-emerald-400 uppercase tracking-wider">Pagado esa Noche</span>
                <div class="mt-2 text-2xl font-black font-mono text-emerald-400 tracking-tight">
                    <span class="text-sm mr-0.5 font-sans">Bs.</span>{{ number_format($totalPagado, 2) }}
                </div>
                <p class="text-[11px] text-zinc-400 mt-1 font-mono">
                    {{ $pagadosCount }} trabajadores cobraron (Deuda 0)
                </p>
            </div>

            <!-- Total Planilla -->
            <div class="glass-card rounded-2xl p-5">
                <span class="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">Total Planilla Nocturna</span>
                <div class="mt-2 text-2xl font-black font-mono text-white tracking-tight">
                    <span class="text-amber-400 text-sm mr-0.5 font-sans">Bs.</span>{{ number_format($totalPlanilla, 2) }}
                </div>
                <p class="text-[11px] text-zinc-500 mt-1 font-mono">
                    {{ $totalTrabajadores }} trabajadores en nómina
                </p>
            </div>

            <!-- Deuda Histórica Acumulada de Todas las Noches (Clicable) -->
            <div onclick="openNightsHistoryDrawer()" 
                 class="glass-card rounded-2xl p-5 border border-amber-500/30 bg-amber-500/5 hover:border-amber-400 hover:bg-amber-500/10 cursor-pointer transition-all group">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-mono font-bold text-amber-400 uppercase tracking-wider">Deuda Global del Local</span>
                    <span class="text-[10px] font-mono font-bold text-amber-400 group-hover:translate-x-0.5 transition-transform">Ver Noches &rarr;</span>
                </div>
                <div class="mt-2 text-2xl font-black font-mono text-amber-300 tracking-tight">
                    <span class="text-sm mr-0.5 font-sans">Bs.</span>{{ number_format($globalSummary['total_deuda_historica'], 2) }}
                </div>
                <p class="text-[11px] text-zinc-400 mt-1 font-mono">
                    En {{ $globalSummary['noches_con_deuda'] }} noches • <span class="text-amber-400 font-bold underline">Presiona para ver historial</span>
                </p>
            </div>

        </div>

        <!-- ==========================================
             TABLA DE PERSONAL ORDENADA POR DEUDA
             ========================================== -->
        <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl border border-white/10">
            <div class="px-6 py-4 bg-white/[0.02] border-b border-white/10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider font-mono flex items-center gap-2">
                        <span>DETALLE DE PAGOS & DEUDAS — NOCHE: {{ mb_strtoupper($session->day_name) }} {{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}</span>
                    </h3>
                    <p class="text-[11px] text-zinc-400 mt-0.5">
                        Ordenado por prioridad: personal con saldo pendiente arriba (mayor a menor) y personal con deuda cero al final.
                    </p>
                </div>
                <div class="flex items-center gap-3 text-xs font-mono">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-500/10 text-rose-300 border border-rose-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                        Debe: Bs. {{ number_format($totalDeuda, 2) }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        Liquidado: Bs. {{ number_format($totalPagado, 2) }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-white/[0.04] border-b border-white/10 text-zinc-400 font-mono uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="px-4 py-3.5 w-12 text-center">N°</th>
                            <th class="px-4 py-3.5">Trabajador / Personal</th>
                            <th class="px-4 py-3.5">Cargo / Área</th>
                            <th class="px-4 py-3.5 text-right">Sueldo / Jornal</th>
                            <th class="px-4 py-3.5 text-center">Estado Esa Noche</th>
                            <th class="px-4 py-3.5 text-right w-36">Deuda Esa Noche</th>
                            <th class="px-4 py-3.5 text-center w-28 text-white font-bold">ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 font-mono">
                        @forelse($attendances as $index => $att)
                            @php
                                $hasDebt = !$att->is_paid;
                            @endphp
                            <tr class="hover:bg-white/[0.02] transition-colors {{ $hasDebt ? 'bg-rose-500/[0.03]' : '' }}">
                                <td class="px-4 py-3.5 text-center text-zinc-500 font-bold">{{ $index + 1 }}</td>
                                <td class="px-4 py-3.5 font-sans">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-xl flex items-center justify-center font-bold text-xs {{ $hasDebt ? 'bg-rose-500/10 text-rose-300 border border-rose-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' }}">
                                            {{ strtoupper(substr($att->staff->name ?? 'P', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-white uppercase text-sm block">
                                                {{ $att->staff->name }}
                                            </span>
                                            <span class="text-[10px] text-zinc-500 font-mono">
                                                {{ $att->staff->assigned_bar ?? 'General' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/5 text-zinc-300 border border-white/10">
                                        {{ $att->staff->role }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right font-black text-zinc-200">
                                    Bs. {{ number_format($att->pay_amount, 2) }}
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if($att->is_paid)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/25">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <polyline points="20 6 9 17 4 12"/>
                                            </svg>
                                            Pagado esa noche
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/15 text-rose-300 border border-rose-500/30">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="10"/>
                                                <line x1="12" y1="8" x2="12" y2="12"/>
                                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                                            </svg>
                                            No se le pagó
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    @if($hasDebt)
                                        <div class="inline-block px-3 py-1 rounded-xl bg-rose-500/15 border border-rose-500/30">
                                            <span class="text-xs font-bold text-rose-300">
                                                Se le debe: <strong class="text-rose-400 text-sm">Bs. {{ number_format($att->debt_amount, 2) }}</strong>
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xs font-bold text-emerald-400 font-mono">
                                            Deuda: Bs. 0.00
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if($hasDebt)
                                        <form method="POST" action="{{ route('paymentHistory.paySingle', $att) }}" 
                                              onsubmit="return confirm('¿Registrar el pago de Bs. {{ number_format($att->pay_amount, 2) }} para {{ $att->staff->name }}?');">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-3.5 py-1.5 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-md shadow-emerald-500/20 active:scale-95 transition-all cursor-pointer">
                                                Pagar
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-zinc-600 text-xs font-mono">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-zinc-500 font-sans">
                                    No hay personal asignado para la noche seleccionada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-white/[0.03] border-t border-white/10 font-bold text-xs text-white font-mono">
                        <tr>
                            <td colspan="3" class="px-4 py-3.5 text-right uppercase tracking-wider">TOTALES DE LA NOCHE:</td>
                            <td class="px-4 py-3.5 text-right font-black text-amber-400 text-sm">
                                Bs. {{ number_format($totalPlanilla, 2) }}
                            </td>
                            <td class="px-4 py-3.5 text-center text-emerald-400">
                                Pagado: Bs. {{ number_format($totalPagado, 2) }}
                            </td>
                            <td class="px-4 py-3.5 text-right text-rose-400 text-sm font-black">
                                Total Deuda: Bs. {{ number_format($totalDeuda, 2) }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    @endif

</div>

<!-- ==========================================
     DRAWER: HISTORIAL DE NOCHES & DEUDAS GLOBALES
     ========================================== -->
<div id="nights-history-drawer-overlay" class="drawer-overlay" onclick="closeNightsHistoryDrawer()"></div>

<div id="nights-history-drawer" class="drawer-panel" style="z-index: 60;">
    <div class="drawer-header">
        <div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-1.5 font-mono">
                Auditoría Global
            </span>
            <h2 class="text-lg font-black text-white tracking-tight">Historial de Noches y Deudas</h2>
            <p class="text-xs text-zinc-400 mt-0.5">Selecciona cualquier noche para ver el historial y pagar al personal pendiente.</p>
        </div>
        <button type="button" onclick="closeNightsHistoryDrawer()" class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 text-zinc-400 hover:text-white transition-all flex-shrink-0 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <div class="drawer-body space-y-4">
        <div class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-mono font-bold uppercase text-amber-400 block">Deuda Acumulada Total</span>
                <span class="text-xl font-black font-mono text-amber-300">Bs. {{ number_format($globalSummary['total_deuda_historica'], 2) }}</span>
            </div>
            <div class="text-right">
                <span class="text-[10px] font-mono text-zinc-400 block">Noches Pendientes</span>
                <span class="text-sm font-bold font-mono text-rose-400">{{ $globalSummary['noches_con_deuda'] }} noches</span>
            </div>
        </div>

        <p class="text-xs text-zinc-400 font-mono">Toca una noche para abrirla y liquidar a los que faltan:</p>

        <div class="space-y-3 font-mono">
            @forelse($nightsWithDebts as $item)
                @php
                    $sItem = $item['session'];
                    $isSelected = $session && $session->id === $sItem->id;
                @endphp
                <a href="{{ route('paymentHistory.index', ['session_id' => $sItem->id]) }}" 
                   class="block p-4 rounded-2xl glass-card border {{ $isSelected ? 'border-amber-400 bg-amber-500/10' : ($item['has_debt'] ? 'border-rose-500/30 bg-rose-500/5 hover:border-rose-500/50' : 'border-white/10 hover:border-white/20') }} transition-all group">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $sItem->isOpen() ? 'bg-emerald-400 animate-pulse' : 'bg-zinc-500' }}"></span>
                            <span class="text-sm font-black text-white font-sans uppercase">
                                {{ $sItem->day_name }} {{ \Carbon\Carbon::parse($sItem->session_date)->format('d/m/Y') }}
                            </span>
                            @if($isSelected)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-400 text-zinc-950">Viendo</span>
                            @endif
                        </div>
                        <span class="text-xs font-mono {{ $item['has_debt'] ? 'text-rose-400 font-black' : 'text-emerald-400 font-bold' }}">
                            {{ $item['has_debt'] ? 'Deuda: Bs. ' . number_format($item['total_debt'], 2) : '✓ Al Día' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-2 text-[11px] text-zinc-400 pt-2 border-t border-white/5">
                        <div>
                            <span class="text-zinc-500 block text-[9px] uppercase">Planilla</span>
                            <span class="font-bold text-zinc-200">Bs. {{ number_format($item['total_payroll'], 2) }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-500 block text-[9px] uppercase">Pagado</span>
                            <span class="font-bold text-emerald-400">Bs. {{ number_format($item['total_paid'], 2) }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-zinc-500 block text-[9px] uppercase">Sin Cobrar</span>
                            <span class="font-bold {{ $item['unpaid_count'] > 0 ? 'text-rose-400' : 'text-zinc-400' }}">
                                {{ $item['unpaid_count'] }} pers.
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-6 text-center text-xs text-zinc-500">
                    No hay noches registradas en el sistema.
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    function openNightsHistoryDrawer() {
        const overlay = document.getElementById('nights-history-drawer-overlay');
        const drawer = document.getElementById('nights-history-drawer');
        if (overlay && drawer) {
            overlay.classList.add('is-open');
            drawer.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeNightsHistoryDrawer() {
        const overlay = document.getElementById('nights-history-drawer-overlay');
        const drawer = document.getElementById('nights-history-drawer');
        if (overlay && drawer) {
            overlay.classList.remove('is-open');
            drawer.classList.remove('is-open');
            document.body.style.overflow = '';
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeNightsHistoryDrawer();
        }
    });
</script>
@endsection
