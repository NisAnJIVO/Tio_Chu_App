@extends('layouts.app')

@section('title', 'Historial de Pagos y Deudas del Personal')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- ==========================================
         CABECERA MINIMALISTA (iOS PURE DARK)
         ========================================== -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4 border-b border-zinc-800/80">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-mono font-bold text-[#F5B81C] uppercase tracking-wider">
                    Auditoría & Planilla
                </span>
                <span class="text-zinc-600 text-xs">•</span>
                <span class="text-xs text-zinc-400 font-mono">Control de Deudas y Saldos Pendientes</span>
            </div>
            <h2 class="text-2xl font-black tracking-tight text-white">
                Historial de Pagos y Deudas del Personal
            </h2>
            <p class="text-xs text-zinc-400 mt-1">
                Monitoreo nocturno ordenado por saldo: personal con pagos pendientes arriba y liquidaciones al día al final.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            @if($session)
                <a href="{{ route('staffPayments.index', ['session_id' => $session->id]) }}" 
                   class="px-3.5 py-2 rounded-xl bg-zinc-900 border border-zinc-800 hover:border-zinc-700 text-xs font-mono font-bold text-[#F5B81C] hover:text-amber-300 transition-all flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4 text-[#F5B81C]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                    </svg>
                    <span>Planilla de esta Noche</span>
                </a>
            @endif

            @if($allSessions->isNotEmpty())
                <form method="GET" action="{{ route('paymentHistory.index') }}" class="p-1.5 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center gap-2">
                    <label for="session_id" class="text-xs text-zinc-400 font-medium pl-1.5 font-mono">Noche:</label>
                    <select name="session_id" id="session_id" onchange="this.form.submit()" 
                            class="bg-zinc-900 border border-zinc-800 text-xs font-mono font-bold rounded-lg px-2.5 py-1 text-zinc-100 cursor-pointer focus:outline-none focus:border-[#F5B81C]">
                        @foreach($allSessions as $s)
                            <option value="{{ $s->id }}" {{ $session && $session->id === $s->id ? 'selected' : '' }} class="bg-zinc-900 text-zinc-100">
                                {{ $s->day_name }} {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }} ({{ $s->status === 'open' ? 'Abierta' : 'Cerrada' }})
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>
    </div>

    @if(!$session)
        <div class="theme-card border border-zinc-800/80 rounded-2xl p-12 text-center shadow-xl bg-[#09090b]">
            <h3 class="text-base font-bold text-white">No hay jornadas registradas</h3>
            <p class="text-xs text-zinc-400 mt-1 mb-6">Apertura una noche para consultar el historial de pagos y deudas.</p>
            <a href="{{ route('sessions.create') }}" class="inline-flex items-center px-4 py-2 bg-[#F5B81C] hover:bg-[#e5ac18] text-zinc-950 text-xs font-bold rounded-xl transition-all shadow-md">
                + Crear Noche
            </a>
        </div>
    @else

        <!-- ==========================================
             ESTADO DE LA NOCHE & RESUMEN GLOBAL
             ========================================== -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 rounded-2xl bg-[#09090b] border border-zinc-800/80">
            <div class="flex items-center gap-3">
                <span class="w-2.5 h-2.5 rounded-full {{ $session->isOpen() ? 'bg-emerald-400 animate-pulse' : 'bg-zinc-500' }}"></span>
                <div>
                    <h4 class="text-xs font-mono font-black uppercase tracking-wider text-white">
                        JORNADA: {{ mb_strtoupper($session->day_name) }} {{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}
                    </h4>
                    <p class="text-[11px] text-zinc-400 mt-0.5">
                        Estado: <strong class="{{ $session->isOpen() ? 'text-emerald-400' : 'text-zinc-400' }}">{{ $session->isOpen() ? 'Noche Abierta' : 'Noche Cerrada' }}</strong>
                        • Registro y auditoría de jornales.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="openNightsHistoryDrawer()" 
                        class="px-3 py-1.5 rounded-xl bg-zinc-900 border border-zinc-800 hover:border-zinc-700 text-xs font-mono font-bold text-zinc-200 hover:text-white transition-all flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-[#F5B81C]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <span>Historial de Todas las Noches</span>
                </button>
            </div>
        </div>

        <!-- ==========================================
             TARJETAS KPI (RESUMEN NOCHE Y GLOBAL)
             ========================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            
            <!-- Deuda Pendiente de Esta Noche -->
            <div class="theme-card rounded-2xl p-5 border {{ $totalDeuda > 0 ? 'border-rose-500/30' : 'border-zinc-800/80' }} bg-[#09090b]">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-semibold text-zinc-400 uppercase tracking-wider">Deuda Pendiente</span>
                    <span id="badge-deuda" class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold uppercase {{ $totalDeuda > 0 ? 'bg-rose-500/10 text-rose-300 border border-rose-500/20' : 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/20' }}">
                        {{ $totalDeuda > 0 ? 'Por Pagar' : 'Al Día' }}
                    </span>
                </div>
                <div class="mt-2 text-2xl font-black font-mono {{ $totalDeuda > 0 ? 'text-rose-400' : 'text-emerald-400' }} tracking-tight">
                    <span class="text-sm mr-1 font-sans">Bs.</span><span id="kpi-deuda">{{ number_format($totalDeuda, 2) }}</span>
                </div>
                <p class="text-[11px] text-zinc-500 mt-1 font-mono">
                    <span id="kpi-deuda-count">{{ $conDeudaCount }}</span> de {{ $totalTrabajadores }} con saldo pendiente
                </p>
            </div>

            <!-- Total Liquidado (Pagado) -->
            <div class="theme-card rounded-2xl p-5 border border-emerald-500/30 bg-[#09090b]">
                <span class="text-xs font-mono font-bold text-emerald-400 uppercase tracking-wider">Pagado esa Noche</span>
                <div class="mt-2 text-2xl font-black font-mono text-emerald-400 tracking-tight">
                    <span class="text-sm mr-1 font-sans">Bs.</span><span id="kpi-pagado">{{ number_format($totalPagado, 2) }}</span>
                </div>
                <p class="text-[11px] text-emerald-500/70 mt-1 font-mono">
                    <span id="kpi-pagado-count">{{ $pagadosCount }}</span> cobraron su jornal
                </p>
            </div>

            <!-- Total Planilla -->
            <div class="theme-card rounded-2xl p-5 border border-zinc-800/80 bg-[#09090b]">
                <span class="text-xs font-mono font-semibold text-zinc-400 uppercase tracking-wider">Total Planilla Nocturna</span>
                <div class="mt-2 text-2xl font-black font-mono text-white tracking-tight">
                    <span class="text-[#F5B81C] text-sm mr-1 font-sans">Bs.</span>{{ number_format($totalPlanilla, 2) }}
                </div>
                <p class="text-[11px] text-zinc-500 mt-1 font-mono">
                    {{ $totalTrabajadores }} trabajadores en nómina
                </p>
            </div>

            <!-- Deuda Global del Local (Clicable) -->
            <div onclick="openNightsHistoryDrawer()" 
                 class="theme-card rounded-2xl p-5 border border-amber-500/30 bg-[#09090b] hover:border-[#F5B81C] cursor-pointer transition-all group shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-bold text-[#F5B81C] uppercase tracking-wider">Deuda Global</span>
                    <span class="text-[11px] font-mono font-bold text-[#F5B81C] group-hover:translate-x-0.5 transition-transform">Ver &rarr;</span>
                </div>
                <div class="mt-2 text-2xl font-black font-mono text-[#F5B81C] tracking-tight">
                    <span class="text-sm mr-1 font-sans">Bs.</span>{{ number_format($globalSummary['total_deuda_historica'], 2) }}
                </div>
                <p class="text-[11px] text-zinc-400 mt-1 font-mono">
                    En {{ $globalSummary['noches_con_deuda'] }} noches • <span class="text-[#F5B81C] underline font-semibold">Ver detalle</span>
                </p>
            </div>

        </div>

        <!-- ==========================================
             TABLA DE PERSONAL ORDENADA POR DEUDA
             ========================================== -->
        <div class="theme-card rounded-2xl overflow-hidden border border-zinc-800/80 bg-[#09090b] shadow-xl">
            <div class="px-5 py-3.5 bg-zinc-950/60 border-b border-zinc-800/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h3 class="text-xs font-bold text-zinc-200 uppercase tracking-wider font-mono">
                        Planilla: {{ mb_strtoupper($session->day_name) }} {{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}
                    </h3>
                    <p class="text-[11px] text-zinc-400 mt-0.5">
                        Prioridad: personal con saldo pendiente arriba y liquidaciones al día al final.
                    </p>
                </div>
                <div class="flex items-center gap-2.5 text-xs font-mono">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-500/10 text-rose-300 border border-rose-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                        Debe: Bs. <span id="header-deuda">{{ number_format($totalDeuda, 2) }}</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        Liquidado: Bs. <span id="header-pagado">{{ number_format($totalPagado, 2) }}</span>
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left" id="history-table">
                    <thead class="bg-zinc-950/40 border-b border-zinc-800/80 text-zinc-400 font-mono uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="px-4 py-3.5 w-12 text-center">N°</th>
                            <th class="px-4 py-3.5">Personal</th>
                            <th class="px-4 py-3.5">Cargo</th>
                            <th class="px-4 py-3.5 text-right w-28">Jornal</th>
                            <th class="px-4 py-3.5 text-center w-36">Estado</th>
                            <th class="px-4 py-3.5 text-right w-40">Saldo Pendiente</th>
                            <th class="px-4 py-3.5 text-center w-28">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/60 font-mono" id="history-tbody">
                        @forelse($attendances as $index => $att)
                            @php
                                $hasDebt = !$att->is_paid;
                            @endphp
                            <tr id="attendance-row-{{ $att->id }}" class="hover:bg-zinc-900/40 transition-colors {{ $hasDebt ? 'bg-rose-500/[0.02]' : '' }}">
                                <td class="px-4 py-3.5 text-center text-zinc-500 font-bold">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-4 py-3.5 font-sans">
                                    <div class="font-bold text-white uppercase text-sm leading-tight">
                                        {{ $att->staff->name }}
                                    </div>
                                    @if(!empty($att->staff->phone))
                                        <div class="text-zinc-500 font-mono text-[11px] mt-0.5 flex items-center gap-1">
                                            <svg class="w-3 h-3 text-zinc-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                                            </svg>
                                            <span>{{ $att->staff->phone }}</span>
                                        </div>
                                    @else
                                        <div class="text-zinc-600 font-mono text-[10px] mt-0.5">
                                            — Sin celular —
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 font-sans">
                                    <span class="inline-flex px-2 py-0.5 rounded-lg text-[10px] font-medium bg-zinc-900 border border-zinc-800 text-zinc-300">
                                        {{ $att->staff->role }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right font-bold text-zinc-200">
                                    Bs. {{ number_format($att->pay_amount, 2) }}
                                </td>
                                <td class="px-4 py-3.5 text-center status-col">
                                    @if($att->is_paid)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/25">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <polyline points="20 6 9 17 4 12"/>
                                            </svg>
                                            Pagado esa noche
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/15 text-rose-300 border border-rose-500/30">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="10"/>
                                                <line x1="12" y1="8" x2="12" y2="12"/>
                                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                                            </svg>
                                            No se le pagó
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right debt-col">
                                    @if($hasDebt)
                                        <div class="inline-block px-2.5 py-0.5 rounded-lg bg-rose-500/10 border border-rose-500/25">
                                            <span class="text-xs font-bold text-rose-300">
                                                Se le debe: <strong class="text-rose-400 text-xs font-mono">Bs. {{ number_format($att->debt_amount, 2) }}</strong>
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xs font-bold text-emerald-400 font-mono">
                                            Deuda: Bs. 0.00
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-center action-col">
                                    @if($hasDebt)
                                        <button type="button" 
                                                onclick="paySingleWorker({{ $att->id }}, '{{ route('paymentHistory.paySingle', $att) }}', '{{ addslashes($att->staff->name) }}', {{ $att->pay_amount }})"
                                                class="px-3.5 py-1.5 bg-[#F5B81C] hover:bg-[#e5ac18] text-zinc-950 font-black text-xs uppercase tracking-wider rounded-xl shadow-sm active:scale-95 transition-all cursor-pointer">
                                            Pagar
                                        </button>
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
                    <tfoot class="bg-zinc-950/60 border-t border-zinc-800/80 font-bold text-xs text-white font-mono">
                        <tr>
                            <td colspan="3" class="px-4 py-3.5 text-right uppercase tracking-wider">TOTALES DE LA NOCHE:</td>
                            <td class="px-4 py-3.5 text-right font-black text-[#F5B81C] text-sm">
                                Bs. {{ number_format($totalPlanilla, 2) }}
                            </td>
                            <td class="px-4 py-3.5 text-center text-emerald-400">
                                Pagado: Bs. <span id="footer-pagado">{{ number_format($totalPagado, 2) }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-right text-rose-400 text-sm font-black">
                                Total Deuda: Bs. <span id="footer-deuda">{{ number_format($totalDeuda, 2) }}</span>
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
     TOAST FLOTANTE DISCRETO (ESTILO iOS DARK)
     ========================================== -->
<div id="discreet-toast" class="fixed bottom-6 right-6 z-50 transform translate-y-8 opacity-0 pointer-events-none transition-all duration-300 ease-out flex items-center gap-2.5 px-4 py-3 rounded-2xl bg-zinc-900 border border-zinc-700/80 shadow-2xl text-xs font-medium text-white backdrop-blur-md">
    <div id="discreet-toast-icon" class="w-2 h-2 rounded-full bg-[#F5B81C]"></div>
    <span id="discreet-toast-msg" class="tracking-wide">Notificación</span>
</div>

<!-- ==========================================
     DRAWER: HISTORIAL DE NOCHES & DEUDAS GLOBALES
     ========================================== -->
<div id="nights-history-drawer-overlay" class="drawer-overlay" onclick="closeNightsHistoryDrawer()"></div>

<div id="nights-history-drawer" class="drawer-panel" style="z-index: 60;">
    <div class="drawer-header">
        <div>
            <h2 class="text-base font-black text-white tracking-tight">Historial de Noches y Deudas</h2>
            <p class="text-xs text-zinc-400 mt-0.5">Selecciona cualquier noche para auditar y liquidar deudas pendientes.</p>
        </div>
        <button type="button" onclick="closeNightsHistoryDrawer()" class="w-8 h-8 flex items-center justify-center rounded-xl bg-zinc-900 border border-zinc-800 text-zinc-400 hover:text-white transition-all cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <div class="drawer-body space-y-4">
        <div class="p-4 rounded-xl bg-zinc-950 border border-zinc-800/80 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-mono font-bold uppercase text-zinc-400 block">Deuda Acumulada Total</span>
                <span class="text-xl font-black font-mono text-[#F5B81C]">Bs. {{ number_format($globalSummary['total_deuda_historica'], 2) }}</span>
            </div>
            <div class="text-right">
                <span class="text-[10px] font-mono text-zinc-400 block">Noches con Deuda</span>
                <span class="text-sm font-bold font-mono text-rose-400">{{ $globalSummary['noches_con_deuda'] }} noches</span>
            </div>
        </div>

        <p class="text-xs text-zinc-400 font-mono">Selecciona una noche para ver su detalle:</p>

        <div class="space-y-2.5 font-mono">
            @forelse($nightsWithDebts as $item)
                @php
                    $sItem = $item['session'];
                    $isSelected = $session && $session->id === $sItem->id;
                @endphp
                <a href="{{ route('paymentHistory.index', ['session_id' => $sItem->id]) }}" 
                   class="block p-3.5 rounded-xl border {{ $isSelected ? 'border-[#F5B81C] bg-[#F5B81C]/5' : ($item['has_debt'] ? 'border-rose-500/30 bg-zinc-950 hover:border-rose-500/50' : 'border-zinc-800/80 bg-zinc-950 hover:border-zinc-700') }} transition-all group">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $sItem->isOpen() ? 'bg-emerald-400 animate-pulse' : 'bg-zinc-500' }}"></span>
                            <span class="text-sm font-black text-white font-sans uppercase">
                                {{ $sItem->day_name }} {{ \Carbon\Carbon::parse($sItem->session_date)->format('d/m/Y') }}
                            </span>
                            @if($isSelected)
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-[#F5B81C] text-zinc-950">Viendo</span>
                            @endif
                        </div>
                        <span class="text-xs font-mono {{ $item['has_debt'] ? 'text-rose-400 font-black' : 'text-emerald-400 font-bold' }}">
                            {{ $item['has_debt'] ? 'Deuda: Bs. ' . number_format($item['total_debt'], 2) : '✓ Al Día' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-2 text-[11px] text-zinc-400 pt-2 border-t border-zinc-800/60">
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
                            <span class="font-bold {{ $item['unpaid_count'] > 0 ? 'text-rose-400' : 'text-zinc-500' }}">
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
    // ==========================================
    // TOAST MINIMALISTA DISCRETO
    // ==========================================
    let toastTimeout = null;
    function showToast(message, type = 'success') {
        const toast = document.getElementById('discreet-toast');
        const msgEl = document.getElementById('discreet-toast-msg');
        const iconEl = document.getElementById('discreet-toast-icon');
        if (!toast || !msgEl || !iconEl) return;

        clearTimeout(toastTimeout);
        msgEl.textContent = message;

        if (type === 'error') {
            iconEl.className = 'w-2 h-2 rounded-full bg-rose-500';
            toast.classList.add('border-rose-500/40');
            toast.classList.remove('border-zinc-700/80');
        } else {
            iconEl.className = 'w-2 h-2 rounded-full bg-[#F5B81C]';
            toast.classList.remove('border-rose-500/40');
            toast.classList.add('border-zinc-700/80');
        }

        toast.classList.remove('translate-y-8', 'opacity-0', 'pointer-events-none');
        toast.classList.add('translate-y-0', 'opacity-100');

        toastTimeout = setTimeout(() => {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-8', 'opacity-0', 'pointer-events-none');
        }, 2200);
    }

    // ==========================================
    // PAGAR TRABAJADOR ASINCRÓNICO (SIN PARPADEO)
    // ==========================================
    async function paySingleWorker(attId, payUrl, staffName, amount) {
        if (!confirm(`¿Registrar el pago de Bs. ${amount.toFixed(2)} para ${staffName}?`)) {
            return;
        }

        const row = document.getElementById(`attendance-row-${attId}`);
        if (row) {
            row.style.transition = 'all 0.22s ease-out';
            row.style.opacity = '0.4';
            row.style.pointerEvents = 'none';
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                || document.querySelector('input[name="_token"]')?.value;

            const res = await fetch(payUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await res.json().catch(() => ({}));

            if (res.ok && (data.success || data.success === undefined)) {
                if (row) {
                    row.classList.remove('bg-rose-500/[0.02]');

                    // Actualizar Columna de Estado
                    const statusCol = row.querySelector('.status-col');
                    if (statusCol) {
                        statusCol.innerHTML = `
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/25">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                Pagado esa noche
                            </span>
                        `;
                    }

                    // Actualizar Columna de Deuda
                    const debtCol = row.querySelector('.debt-col');
                    if (debtCol) {
                        debtCol.innerHTML = `
                            <span class="text-xs font-bold text-emerald-400 font-mono">
                                Deuda: Bs. 0.00
                            </span>
                        `;
                    }

                    // Actualizar Columna de Acción
                    const actionCol = row.querySelector('.action-col');
                    if (actionCol) {
                        actionCol.innerHTML = `<span class="text-zinc-600 text-xs font-mono">—</span>`;
                    }

                    row.style.opacity = '1';
                    row.style.pointerEvents = 'auto';

                    // Actualizar KPIs si vienen en la respuesta
                    const formatMoney = (val) => Number(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    if (data.totalDeuda !== undefined) {
                        const elDeuda = document.getElementById('kpi-deuda');
                        if (elDeuda) elDeuda.textContent = formatMoney(data.totalDeuda);

                        const elHDeuda = document.getElementById('header-deuda');
                        if (elHDeuda) elHDeuda.textContent = formatMoney(data.totalDeuda);

                        const elFDeuda = document.getElementById('footer-deuda');
                        if (elFDeuda) elFDeuda.textContent = formatMoney(data.totalDeuda);

                        const badgeDeuda = document.getElementById('badge-deuda');
                        if (badgeDeuda) {
                            if (data.totalDeuda > 0) {
                                badgeDeuda.className = 'px-2 py-0.5 rounded-md text-[10px] font-mono font-bold uppercase bg-rose-500/10 text-rose-300 border border-rose-500/20';
                                badgeDeuda.textContent = 'Por Pagar';
                            } else {
                                badgeDeuda.className = 'px-2 py-0.5 rounded-md text-[10px] font-mono font-bold uppercase bg-emerald-500/10 text-emerald-300 border border-emerald-500/20';
                                badgeDeuda.textContent = 'Al Día';
                            }
                        }
                    }

                    if (data.totalPagado !== undefined) {
                        const elPagado = document.getElementById('kpi-pagado');
                        if (elPagado) elPagado.textContent = formatMoney(data.totalPagado);

                        const elHPagado = document.getElementById('header-pagado');
                        if (elHPagado) elHPagado.textContent = formatMoney(data.totalPagado);

                        const elFPagado = document.getElementById('footer-pagado');
                        if (elFPagado) elFPagado.textContent = formatMoney(data.totalPagado);
                    }

                    // Actualizar contadores
                    const rowsWithDebt = document.querySelectorAll('#history-tbody .debt-col strong');
                    const kpiDeudaCount = document.getElementById('kpi-deuda-count');
                    if (kpiDeudaCount) kpiDeudaCount.textContent = rowsWithDebt.length;

                    showToast(`Se registró el pago de Bs. ${amount.toFixed(2)} para ${staffName}. Deuda saldada.`);
                }
            } else {
                if (row) {
                    row.style.opacity = '1';
                    row.style.pointerEvents = 'auto';
                }
                showToast(data.message || 'No se pudo registrar el pago.', 'error');
            }
        } catch (err) {
            console.error('Error al pagar:', err);
            if (row) {
                row.style.opacity = '1';
                row.style.pointerEvents = 'auto';
            }
            showToast('Error de conexión al registrar pago.', 'error');
        }
    }

    // ==========================================
    // CONTROL DEL DRAWER DE NOCHES
    // ==========================================
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
