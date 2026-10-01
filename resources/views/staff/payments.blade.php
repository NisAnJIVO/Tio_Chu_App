@extends('layouts.app')

@section('title', 'Pagos al Personal')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- ==========================================
         CABECERA MINIMALISTA (iOS PURE DARK)
         ========================================== -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4 border-b border-zinc-800/80">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-mono font-bold text-[#F5B81C] uppercase tracking-wider">
                    Planilla & Jornales
                </span>
                <span class="text-zinc-600 text-xs">•</span>
                <span class="text-xs text-zinc-400 font-mono">Liquidación Nocturna</span>
            </div>
            <h2 class="text-2xl font-black tracking-tight text-white">
                Pagos al Personal de Turno
            </h2>
            <p class="text-xs text-zinc-400 mt-1">
                Registro de jornales, control de pagos en efectivo y liquidación del turno nocturno.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('paymentHistory.index', $session ? ['session_id' => $session->id] : []) }}" 
               class="px-3.5 py-2 rounded-xl bg-zinc-900 border border-zinc-800 hover:border-zinc-700 text-xs font-mono font-bold text-[#F5B81C] hover:text-amber-300 transition-all flex items-center gap-1.5 shadow-sm">
                <svg class="w-4 h-4 text-[#F5B81C]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                <span>Historial de Pagos</span>
            </a>

            @if($allSessions->isNotEmpty())
                <form method="GET" action="{{ route('staffPayments.index') }}" class="p-1.5 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center gap-2">
                    <label for="session_id" class="text-xs text-zinc-400 font-medium pl-1.5 font-mono">Noche:</label>
                    <select name="session_id" id="session_id" onchange="this.form.submit()" 
                            class="bg-zinc-900 border border-zinc-800 text-xs font-mono font-bold rounded-lg px-2.5 py-1 text-zinc-100 cursor-pointer focus:outline-none focus:border-[#F5B81C]">
                        @foreach($allSessions as $s)
                            <option value="{{ $s->id }}" {{ $session && $session->id === $s->id ? 'selected' : '' }} class="bg-zinc-900 text-zinc-100">
                                {{ $s->day_name }} {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>
    </div>

    @if(!$session)
        <div class="theme-card border border-zinc-800/80 rounded-2xl p-12 text-center shadow-xl bg-[#09090b]">
            <h3 class="text-base font-bold text-white">No hay noche seleccionada</h3>
            <p class="text-xs text-zinc-400 mt-1 mb-6">Crea o selecciona una noche para registrar la planilla de pagos.</p>
            <a href="{{ route('sessions.create') }}" class="inline-flex items-center px-4 py-2 bg-[#F5B81C] hover:bg-[#e5ac18] text-zinc-950 text-xs font-bold rounded-xl transition-all shadow-md">
                + Crear Noche
            </a>
        </div>
    @else

        @if(!$session->isOpen())
            <div class="p-4 rounded-xl border border-rose-500/30 bg-rose-500/10 shadow-lg flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0110 0v4"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-rose-300 uppercase tracking-wider font-mono">Noche Cerrada (Modo Solo Lectura)</h4>
                        <p class="text-xs text-zinc-400 mt-0.5">Esta jornada fue finalizada en Cierre de Caja. Los pagos no pueden modificarse.</p>
                    </div>
                </div>
                <a href="{{ route('closing.index', ['session_id' => $session->id]) }}" 
                   class="px-3.5 py-1.5 rounded-lg border border-rose-400/30 text-rose-300 hover:bg-rose-500/10 text-xs font-bold font-mono shrink-0 transition-all">
                    Ver en Cierre de Caja
                </a>
            </div>
        @endif

        <!-- ==========================================
             KPIs DE LA PLANILLA (LIVE UPDATING)
             ========================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            
            <div class="theme-card rounded-2xl p-5 border border-zinc-800/80 bg-[#09090b]">
                <span class="text-xs font-mono font-semibold text-zinc-400 uppercase tracking-wider">Total Planilla ({{ $session->day_name }})</span>
                <div class="mt-2 text-2xl font-black font-mono text-white tracking-tight">
                    <span class="text-[#F5B81C] text-sm mr-1 font-sans">Bs.</span><span id="kpi-total-planilla">{{ number_format($totalPlanilla, 2) }}</span>
                </div>
                <p class="text-[11px] text-zinc-500 mt-1 font-mono">
                    <span id="kpi-worker-count">{{ $attendances->count() }}</span> trabajadores registrados
                </p>
            </div>

            <div class="theme-card rounded-2xl p-5 border border-emerald-500/30 bg-[#09090b]">
                <span class="text-xs font-mono font-bold text-emerald-400 uppercase tracking-wider">Total Liquidado (Pagado)</span>
                <div class="mt-2 text-2xl font-black font-mono text-emerald-400 tracking-tight">
                    <span class="text-sm mr-1 font-sans">Bs.</span><span id="kpi-total-pagado">{{ number_format($totalPagado, 2) }}</span>
                </div>
                <p class="text-[11px] text-emerald-500/70 mt-1 font-mono">Efectivo entregado al personal</p>
            </div>

            <div class="theme-card rounded-2xl p-5 border border-zinc-800/80 bg-[#09090b]">
                <span class="text-xs font-mono font-semibold text-zinc-400 uppercase tracking-wider">Pendiente por Pagar</span>
                <div class="mt-2 text-2xl font-black font-mono text-[#F5B81C] tracking-tight">
                    <span class="text-sm mr-1 font-sans">Bs.</span><span id="kpi-total-pendiente">{{ number_format($totalPendiente, 2) }}</span>
                </div>
                <p class="text-[11px] text-zinc-500 mt-1 font-mono">Saldo aún en caja</p>
            </div>

        </div>

        <!-- Desglose por Tipo de Personal -->
        <div class="theme-card rounded-2xl p-5 border border-zinc-800/80 bg-[#09090b] shadow-xl">
            <span class="text-xs font-bold uppercase tracking-wider text-zinc-200 block mb-3 font-mono">Desglose por Cargo</span>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs font-mono">
                <div class="p-3.5 rounded-xl bg-zinc-950 border border-zinc-800/80">
                    <span class="font-bold text-white block font-sans text-sm">Meseros & Limpieza</span>
                    <span class="text-zinc-500 block mt-0.5 text-[11px]">Tarifa promedio: Bs. 100 - 110</span>
                    <div class="mt-2 font-bold text-[#F5B81C] text-sm">
                        {{ $summaryByType['meseros']['count'] }} pers. — Bs. {{ number_format($summaryByType['meseros']['total'], 2) }}
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-zinc-950 border border-zinc-800/80">
                    <span class="font-bold text-white block font-sans text-sm">Bartenders (Barras)</span>
                    <span class="text-zinc-500 block mt-0.5 text-[11px]">Tarifa promedio: Bs. 100 - 110</span>
                    <div class="mt-2 font-bold text-[#F5B81C] text-sm">
                        {{ $summaryByType['bartenders']['count'] }} pers. — Bs. {{ number_format($summaryByType['bartenders']['total'], 2) }}
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-zinc-950 border border-zinc-800/80">
                    <span class="font-bold text-white block font-sans text-sm">Seguridad</span>
                    <span class="text-zinc-500 block mt-0.5 text-[11px]">Tarifa promedio: Bs. 120 - 130</span>
                    <div class="mt-2 font-bold text-[#F5B81C] text-sm">
                        {{ $summaryByType['seguridad']['count'] }} pers. — Bs. {{ number_format($summaryByType['seguridad']['total'], 2) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Barra de Acciones Rápidas -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 rounded-2xl bg-[#09090b] border border-zinc-800/80 {{ !$session->isOpen() ? 'pointer-events-none opacity-50' : '' }}">
            @if($availableStaff->isNotEmpty())
                <form method="POST" action="{{ route('staffPayments.store') }}" class="flex items-center gap-2 flex-1">
                    @csrf
                    <input type="hidden" name="night_session_id" value="{{ $session->id }}">
                    <select name="staff_id" required class="bg-zinc-900 border border-zinc-800 text-xs rounded-xl px-3 py-2 text-zinc-100 cursor-pointer flex-1 focus:outline-none focus:border-[#F5B81C]">
                        <option value="" disabled selected class="bg-zinc-900">+ Agregar Trabajador a esta noche...</option>
                        @foreach($availableStaff as $as)
                            <option value="{{ $as->id }}" class="bg-zinc-900">
                                {{ $as->name }} ({{ $as->role }}) — Tarifa: Bs. {{ number_format($as->getPayForDay($session->day_name), 2) }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-[#F5B81C] hover:bg-[#e5ac18] text-zinc-950 text-xs font-bold rounded-xl active:scale-95 transition-all whitespace-nowrap cursor-pointer">
                        + Agregar
                    </button>
                </form>
            @else
                <span class="text-xs text-zinc-500 font-mono">Todo el personal activo está incluido en esta noche.</span>
            @endif

            <div class="flex items-center gap-2 shrink-0">
                <!-- Botón Editar Sueldo General -->
                <button type="button" onclick="openWageBatchDrawer()" 
                        class="px-3.5 py-2 bg-zinc-900 border border-zinc-800 hover:border-zinc-700 text-zinc-200 rounded-xl text-xs font-bold hover:text-white transition-all cursor-pointer flex items-center gap-1.5 shadow-sm">
                    <svg class="w-3.5 h-3.5 text-[#F5B81C]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    <span>Editar Sueldos por Área</span>
                </button>

                <!-- Marcar Todos como Pagados -->
                <form method="POST" action="{{ route('staffPayments.markAllPaid', $session) }}" onsubmit="return confirm('¿Marcar a todo el personal como PAGADO?');">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-zinc-900 border border-emerald-500/30 text-xs font-bold text-emerald-400 hover:bg-emerald-500/10 transition-colors cursor-pointer flex items-center gap-1.5 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Marcar Todos Pagados</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- ==========================================
             TABLA DE PLANILLA OFICIAL (AJAX SAVE & NO FLICKER)
             ========================================== -->
        <form id="payroll-form" data-ajax="true" method="POST" action="{{ route('staffPayments.update') }}" class="{{ !$session->isOpen() ? 'pointer-events-none opacity-50' : '' }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="night_session_id" value="{{ $session->id }}">

            <div class="theme-card rounded-2xl overflow-hidden border border-zinc-800/80 bg-[#09090b] shadow-xl">
                <div class="px-5 py-3.5 bg-zinc-950/60 border-b border-zinc-800/80 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-zinc-200 uppercase tracking-wider font-mono">
                        Planilla: {{ mb_strtoupper($session->day_name) }} {{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}
                    </h3>
                    <span class="text-xs font-mono font-bold text-[#F5B81C]">
                        TOTAL: Bs. <span id="header-total-planilla">{{ number_format($totalPlanilla, 2) }}</span>
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left" id="payroll-table">
                        <thead class="bg-zinc-950/40 border-b border-zinc-800/80 text-zinc-400 font-mono uppercase text-[10px] tracking-wider">
                            <tr>
                                <th class="px-4 py-3 w-12 text-center">N°</th>
                                <th class="px-4 py-3">Personal</th>
                                <th class="px-4 py-3">Cargo</th>
                                <th class="px-4 py-3 w-36 text-right">Monto (Bs.)</th>
                                <th class="px-4 py-3 w-28 text-center">Pagado</th>
                                <th class="px-4 py-3 w-24 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="payroll-tbody" class="divide-y divide-zinc-800/60 font-sans">
                            @forelse($attendances as $index => $att)
                                <tr id="attendance-row-{{ $att->id }}" class="hover:bg-zinc-900/40 transition-colors">
                                    <td class="px-4 py-3 text-center text-zinc-500 font-mono font-bold row-index">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-4 py-3">
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
                                    <td class="px-4 py-3 text-zinc-400">
                                        <span class="inline-flex px-2 py-0.5 rounded-lg text-[10px] font-medium bg-zinc-900 border border-zinc-800 text-zinc-300">
                                            {{ $att->staff->role }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        <input type="number" step="5" min="0" 
                                               name="attendances[{{ $att->id }}][pay_amount]" 
                                               value="{{ $att->pay_amount }}" 
                                               data-attendance-id="{{ $att->id }}"
                                               oninput="recalculateTotals()"
                                               class="pay-amount-input bg-zinc-950 border border-zinc-800 w-28 text-right rounded-lg px-2.5 py-1.5 text-xs font-mono font-bold text-[#F5B81C] focus:outline-none focus:border-[#F5B81C] transition-colors">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox" 
                                               name="attendances[{{ $att->id }}][is_paid]" 
                                               value="1" 
                                               data-attendance-id="{{ $att->id }}"
                                               onchange="recalculateTotals()"
                                               {{ $att->is_paid ? 'checked' : '' }}
                                               class="is-paid-checkbox rounded border-zinc-700 bg-zinc-950 text-[#F5B81C] focus:ring-0 w-4 h-4 cursor-pointer accent-[#F5B81C]">
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <button type="button" 
                                                onclick="removeAttendance({{ $att->id }}, '{{ route('staffPayments.destroy', $att) }}', '{{ addslashes($att->staff->name) }}')"
                                                class="px-2.5 py-1 rounded-lg text-xs font-semibold text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 border border-transparent hover:border-rose-500/20 transition-all cursor-pointer">
                                            Quitar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr id="empty-payroll-row">
                                    <td colspan="6" class="px-4 py-10 text-center text-zinc-500 font-sans">
                                        No hay personal asignado a esta noche.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-zinc-950/60 border-t border-zinc-800/80 font-bold text-xs text-white">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right uppercase tracking-wider font-mono">TOTAL PLANILLA:</td>
                                <td class="px-4 py-3 text-right font-mono font-black text-[#F5B81C] text-sm">
                                    Bs. <span id="footer-total-planilla">{{ number_format($totalPlanilla, 2) }}</span>
                                </td>
                                <td class="px-4 py-3 text-center font-mono text-emerald-400 text-xs">
                                    Pagado: Bs. <span id="footer-total-pagado">{{ number_format($totalPagado, 2) }}</span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="p-4 bg-zinc-950/60 border-t border-zinc-800/80 flex items-center justify-between">
                    <span id="payroll-autosave-feedback" class="text-xs text-zinc-500 font-mono hidden">
                        Cambios sincronizados
                    </span>
                    <div class="ml-auto flex items-center gap-3">
                        <button type="submit" id="btn-save-payroll" 
                                class="px-5 py-2.5 bg-[#F5B81C] hover:bg-[#e5ac18] text-zinc-950 text-xs font-black rounded-xl active:scale-95 transition-all shadow-md cursor-pointer uppercase tracking-wider flex items-center gap-2">
                            <span>Guardar Cambios de Pagos</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>

    @endif

</div>

<!-- ==========================================
     TOAST FLOTANTE DISCRETO (ESTILO iOS DARK)
     ========================================== -->
<div id="discreet-toast" class="fixed bottom-6 right-6 z-50 transform translate-y-8 opacity-0 pointer-events-none transition-all duration-300 ease-out flex items-center gap-2.5 px-4 py-3 rounded-2xl bg-zinc-900 border border-zinc-700/80 shadow-2xl text-xs font-medium text-white backdrop-blur-md">
    <div id="discreet-toast-icon" class="w-2 h-2 rounded-full bg-[#F5B81C]"></div>
    <span id="discreet-toast-msg" class="tracking-wide">Notificación</span>
</div>

@if($session)
<!-- ==========================================
     DRAWER: EDITAR SUELDO GENERAL
     ========================================== -->
<div id="wage-batch-drawer-overlay" class="drawer-overlay" onclick="closeWageBatchDrawer()"></div>

<div id="wage-batch-drawer" class="drawer-panel" style="z-index: 60;">
    <div class="drawer-header">
        <div>
            <h2 class="text-base font-black text-white tracking-tight">Editar Sueldo por Área</h2>
            <span class="text-xs font-bold text-[#F5B81C] tracking-wide block mt-0.5">
                {{ $session->day_name }} {{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}
            </span>
        </div>
        <button type="button" onclick="closeWageBatchDrawer()" class="w-8 h-8 flex items-center justify-center rounded-xl bg-zinc-900 border border-zinc-800 text-zinc-400 hover:text-white transition-all cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <form method="POST" action="{{ route('staffPayments.updateBatchWage') }}" class="drawer-body space-y-4">
        @csrf
        @method('PUT')
        <input type="hidden" name="night_session_id" value="{{ $session->id }}">

        <div class="p-3.5 rounded-xl bg-zinc-950 border border-zinc-800/80 text-xs text-zinc-300">
            <span class="text-[#F5B81C] font-bold block mb-0.5">Modificación de Tarifa por Área</span>
            Ingresa el importe que corresponde para esta jornada. Se aplicará a todos los integrantes de esa función.
        </div>

        <div class="space-y-3">
            <!-- Meseros -->
            <div class="p-3.5 rounded-xl bg-zinc-900 border border-zinc-800 flex items-center justify-between gap-4">
                <div>
                    <label for="wage_meseros" class="text-sm font-bold text-white block">Meseros</label>
                    <span class="text-[11px] text-zinc-500">Atención de meseros</span>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <span class="text-[#F5B81C] font-bold text-xs font-mono">Bs.</span>
                    <input type="number" step="5" min="0" name="wages[meseros]" id="wage_meseros" placeholder="100"
                           class="bg-zinc-950 border border-zinc-800 w-24 text-right rounded-xl px-3 py-2 text-sm font-bold font-mono text-[#F5B81C] focus:outline-none focus:border-[#F5B81C]">
                </div>
            </div>

            <!-- Limpieza -->
            <div class="p-3.5 rounded-xl bg-zinc-900 border border-zinc-800 flex items-center justify-between gap-4">
                <div>
                    <label for="wage_limpieza" class="text-sm font-bold text-white block">Limpieza</label>
                    <span class="text-[11px] text-zinc-500">Aseo y mantenimiento</span>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <span class="text-[#F5B81C] font-bold text-xs font-mono">Bs.</span>
                    <input type="number" step="5" min="0" name="wages[limpieza]" id="wage_limpieza" placeholder="100"
                           class="bg-zinc-950 border border-zinc-800 w-24 text-right rounded-xl px-3 py-2 text-sm font-bold font-mono text-[#F5B81C] focus:outline-none focus:border-[#F5B81C]">
                </div>
            </div>

            <!-- Seguridades -->
            <div class="p-3.5 rounded-xl bg-zinc-900 border border-zinc-800 flex items-center justify-between gap-4">
                <div>
                    <label for="wage_seguridades" class="text-sm font-bold text-white block">Seguridad</label>
                    <span class="text-[11px] text-zinc-500">Puerta y orden</span>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <span class="text-[#F5B81C] font-bold text-xs font-mono">Bs.</span>
                    <input type="number" step="5" min="0" name="wages[seguridades]" id="wage_seguridades" placeholder="120"
                           class="bg-zinc-950 border border-zinc-800 w-24 text-right rounded-xl px-3 py-2 text-sm font-bold font-mono text-[#F5B81C] focus:outline-none focus:border-[#F5B81C]">
                </div>
            </div>

            <!-- Barra -->
            <div class="p-3.5 rounded-xl bg-zinc-900 border border-zinc-800 flex items-center justify-between gap-4">
                <div>
                    <label for="wage_barra" class="text-sm font-bold text-white block">Bartenders (Barras)</label>
                    <span class="text-[11px] text-zinc-500">Barra Kelly y Ariel</span>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <span class="text-[#F5B81C] font-bold text-xs font-mono">Bs.</span>
                    <input type="number" step="5" min="0" name="wages[barra]" id="wage_barra" placeholder="100"
                           class="bg-zinc-950 border border-zinc-800 w-24 text-right rounded-xl px-3 py-2 text-sm font-bold font-mono text-[#F5B81C] focus:outline-none focus:border-[#F5B81C]">
                </div>
            </div>
        </div>

        <div class="pt-5 border-t border-zinc-800 flex items-center justify-end gap-2.5 mt-6">
            <button type="button" onclick="closeWageBatchDrawer()" 
                    class="px-4 py-2 rounded-xl border border-zinc-800 text-zinc-400 hover:text-white text-xs font-bold transition-all cursor-pointer">
                Cancelar
            </button>
            <button type="submit" 
                    class="px-5 py-2 rounded-xl bg-[#F5B81C] hover:bg-[#e5ac18] text-zinc-950 text-xs font-black uppercase tracking-wider transition-all cursor-pointer">
                Aplicar Tarifas
            </button>
        </div>
    </form>
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
    // RECALCULAR TOTALES EN VIVO
    // ==========================================
    function recalculateTotals() {
        const rows = document.querySelectorAll('#payroll-tbody tr[id^="attendance-row-"]');
        let totalPlanilla = 0;
        let totalPagado = 0;
        let workerCount = 0;

        rows.forEach(row => {
            const inputAmount = row.querySelector('.pay-amount-input');
            const checkboxPaid = row.querySelector('.is-paid-checkbox');

            if (inputAmount) {
                workerCount++;
                const amount = parseFloat(inputAmount.value) || 0;
                totalPlanilla += amount;
                if (checkboxPaid && checkboxPaid.checked) {
                    totalPagado += amount;
                }
            }
        });

        const totalPendiente = Math.max(0, totalPlanilla - totalPagado);
        const formatMoney = (val) => val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        // Actualizar KPIs
        const kpiPlanilla = document.getElementById('kpi-total-planilla');
        if (kpiPlanilla) kpiPlanilla.textContent = formatMoney(totalPlanilla);

        const kpiPagado = document.getElementById('kpi-total-pagado');
        if (kpiPagado) kpiPagado.textContent = formatMoney(totalPagado);

        const kpiPendiente = document.getElementById('kpi-total-pendiente');
        if (kpiPendiente) kpiPendiente.textContent = formatMoney(totalPendiente);

        const kpiCount = document.getElementById('kpi-worker-count');
        if (kpiCount) kpiCount.textContent = workerCount;

        // Actualizar Header y Footer de la tabla
        const headerPlanilla = document.getElementById('header-total-planilla');
        if (headerPlanilla) headerPlanilla.textContent = formatMoney(totalPlanilla);

        const footerPlanilla = document.getElementById('footer-total-planilla');
        if (footerPlanilla) footerPlanilla.textContent = formatMoney(totalPlanilla);

        const footerPagado = document.getElementById('footer-total-pagado');
        if (footerPagado) footerPagado.textContent = formatMoney(totalPagado);
    }

    // Renumerar filas secuencialmente
    function renumberRows() {
        const rows = document.querySelectorAll('#payroll-tbody tr[id^="attendance-row-"]');
        rows.forEach((row, index) => {
            const indexCol = row.querySelector('.row-index');
            if (indexCol) indexCol.textContent = index + 1;
        });

        if (rows.length === 0) {
            const tbody = document.getElementById('payroll-tbody');
            if (tbody && !document.getElementById('empty-payroll-row')) {
                tbody.innerHTML = `
                    <tr id="empty-payroll-row">
                        <td colspan="6" class="px-4 py-10 text-center text-zinc-500 font-sans">
                            No hay personal asignado a esta noche.
                        </td>
                    </tr>
                `;
            }
        }
    }

    // ==========================================
    // QUITAR PERSONAL ASINCRÓNICO (SIN PARPADEO)
    // ==========================================
    async function removeAttendance(attId, deleteUrl, staffName) {
        if (!confirm(`¿Quitar a ${staffName} de la planilla de esta noche?`)) {
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

            const res = await fetch(deleteUrl, {
                method: 'DELETE',
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
                    row.style.transform = 'translateX(12px)';
                    row.style.opacity = '0';
                    setTimeout(() => {
                        row.remove();
                        renumberRows();
                        recalculateTotals();
                        showToast(`Se retiró a ${staffName} de la planilla.`);
                    }, 220);
                }
            } else {
                if (row) {
                    row.style.opacity = '1';
                    row.style.pointerEvents = 'auto';
                }
                showToast(data.message || 'No se pudo retirar el personal.', 'error');
            }
        } catch (err) {
            console.error('Error al quitar personal:', err);
            if (row) {
                row.style.opacity = '1';
                row.style.pointerEvents = 'auto';
            }
            showToast('Error de conexión al retirar personal.', 'error');
        }
    }

    // ==========================================
    // GUARDADO ASINCRÓNICO DE PLANILLA (AJAX)
    // ==========================================
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('payroll-form');
        const btnSave = document.getElementById('btn-save-payroll');

        if (form && btnSave) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Estado de guardando
                const originalBtnHtml = btnSave.innerHTML;
                btnSave.disabled = true;
                btnSave.classList.add('opacity-75');
                btnSave.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-3.5 w-3.5 text-zinc-950" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Guardando...</span>
                `;

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                        || document.querySelector('input[name="_token"]')?.value;

                    const formData = new FormData(form);

                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const data = await res.json().catch(() => ({}));

                    if (res.ok && (data.success || data.success === undefined)) {
                        btnSave.innerHTML = `
                            <svg class="w-3.5 h-3.5 text-zinc-950" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                            <span>Guardado</span>
                        `;
                        showToast('Planilla de pagos actualizada.');
                        recalculateTotals();

                        setTimeout(() => {
                            btnSave.disabled = false;
                            btnSave.classList.remove('opacity-75');
                            btnSave.innerHTML = originalBtnHtml;
                        }, 1800);
                    } else {
                        btnSave.disabled = false;
                        btnSave.classList.remove('opacity-75');
                        btnSave.innerHTML = originalBtnHtml;
                        showToast(data.message || 'Error al guardar los cambios.', 'error');
                    }
                } catch (err) {
                    console.error('Error al guardar planilla:', err);
                    btnSave.disabled = false;
                    btnSave.classList.remove('opacity-75');
                    btnSave.innerHTML = originalBtnHtml;
                    showToast('Error de conexión al guardar.', 'error');
                }
            });
        }
    });

    // ==========================================
    // DRAWER: EDITAR SUELDO GENERAL
    // ==========================================
    function openWageBatchDrawer() {
        const overlay = document.getElementById('wage-batch-drawer-overlay');
        const drawer = document.getElementById('wage-batch-drawer');
        if (overlay && drawer) {
            overlay.classList.add('is-open');
            drawer.classList.add('is-open');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                const input = document.getElementById('wage_meseros');
                if (input) input.focus();
            }, 300);
        }
    }

    function closeWageBatchDrawer() {
        const overlay = document.getElementById('wage-batch-drawer-overlay');
        const drawer = document.getElementById('wage-batch-drawer');
        if (overlay && drawer) {
            overlay.classList.remove('is-open');
            drawer.classList.remove('is-open');
            document.body.style.overflow = '';
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeWageBatchDrawer();
        }
    });
</script>
@endif
@endsection
