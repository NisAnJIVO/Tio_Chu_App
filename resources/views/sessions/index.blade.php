@extends('layouts.app')

@section('title', 'Historial de Noches & Consolidado')

@section('content')
<style>
    /* Color amarillo dorado oficial Tío Chu para los checkboxes al marcarse */
    input[type="checkbox"].session-chk,
    #check-all-desktop {
        accent-color: #F5B81C !important;
        cursor: pointer;
    }
</style>
<div class="space-y-6 max-w-7xl mx-auto pb-24">

    @php
        $openSession = $sessions->firstWhere('status', 'open');
        $closedCount = $sessions->where('status', 'closed')->count();
        $totalCount = $sessions->count();
    @endphp

    <!-- ==========================================
         CABECERA NOTORIA: CONTROL OPERATIVO
         ========================================== -->
    <div class="notoria-card px-6 py-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-0.5">
                <span class="text-[10px] font-mono font-bold text-[#F5B81C] uppercase tracking-wider">
                    Control Operativo
                </span>
                <span class="theme-text-muted text-xs">•</span>
                <span class="text-[10px] theme-text-secondary font-mono">Jornadas Nocturnas</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight theme-text-primary font-sans">
                Historial de Noches de Atención
            </h1>
            <p class="text-xs theme-text-secondary mt-0.5">
                Selecciona noches individuales o fines de semana para consolidar arqueos y métodos de pago.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full lg:w-auto">
            <!-- Botón Generar Consolidado -->
            <button type="button" onclick="openConsolidatedModal()" id="btn-header-consolidated"
                    class="flex-1 sm:flex-none justify-center inline-flex items-center gap-1.5 px-4 py-2.5 notoria-subtile hover:border-amber-500 theme-text-primary text-xs font-bold rounded-xl transition-all cursor-pointer shadow-sm">
                <svg class="w-4 h-4 text-[#F5B81C]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Ver Resumen (<span id="header-selected-count">0</span>)</span>
            </button>

            <a href="{{ route('sessions.create') }}" 
               class="w-full sm:w-auto justify-center inline-flex items-center gap-2 px-4 py-2.5 bg-[#F5B81C] hover:bg-[#e5ac18] text-zinc-950 text-xs font-black tracking-wider uppercase rounded-xl shadow-md active:scale-95 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Aperturar Nueva Noche</span>
            </a>
        </div>
    </div>

    <!-- ==========================================
         KPIs RESUMEN DE JORNADAS (CARDS NOTORIAS)
         ========================================== -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        
        <!-- Total Noches -->
        <div class="notoria-card p-4 sm:p-5 flex items-center justify-between shadow-sm">
            <div class="min-w-0">
                <span class="text-[11px] font-black uppercase tracking-wider theme-text-secondary block truncate">Total Noches</span>
                <div class="mt-1 text-2xl sm:text-3xl font-black font-mono theme-text-primary tracking-tight">
                    {{ $totalCount }}
                </div>
                <p class="text-[10px] theme-text-muted mt-0.5 font-mono truncate">Jornadas registradas</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/30 flex items-center justify-center text-blue-500 shrink-0 ml-2 shadow-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
        </div>

        <!-- Noche Activa -->
        <div class="notoria-card p-4 sm:p-5 flex items-center justify-between shadow-sm {{ $openSession ? 'border-2 border-emerald-500/70' : '' }}">
            <div class="min-w-0">
                <span class="text-[11px] font-black uppercase tracking-wider {{ $openSession ? 'text-emerald-500' : 'theme-text-secondary' }} block truncate">Noche en Curso</span>
                <div class="mt-1 text-2xl sm:text-3xl font-black font-mono {{ $openSession ? 'text-emerald-500' : 'theme-text-primary' }} tracking-tight truncate">
                    @if($openSession)
                        {{ $openSession->day_name }}
                    @else
                        Ninguna
                    @endif
                </div>
                <p class="text-[10px] theme-text-muted mt-0.5 font-mono truncate">
                    @if($openSession)
                        {{ \Carbon\Carbon::parse($openSession->session_date)->format('d/m/Y') }} (Abierta)
                    @else
                        Todas cerradas
                    @endif
                </p>
            </div>
            <div class="w-10 h-10 rounded-xl {{ $openSession ? 'bg-emerald-500/15 border border-emerald-500/40 text-emerald-500 animate-pulse' : 'bg-slate-500/10 border border-slate-500/30 text-slate-400' }} flex items-center justify-center shrink-0 ml-2 shadow-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
        </div>

        <!-- Noches Cerradas -->
        <div class="notoria-card p-4 sm:p-5 flex items-center justify-between shadow-sm">
            <div class="min-w-0">
                <span class="text-[11px] font-black uppercase tracking-wider theme-text-secondary block truncate">Noches Cerradas</span>
                <div class="mt-1 text-2xl sm:text-3xl font-black font-mono theme-text-primary tracking-tight">
                    {{ $closedCount }}
                </div>
                <p class="text-[10px] theme-text-muted mt-0.5 font-mono truncate">Arqueadas en Cierre</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/30 flex items-center justify-center text-purple-500 shrink-0 ml-2 shadow-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
        </div>

        <!-- Comisión POS Promedio -->
        <div class="notoria-card p-4 sm:p-5 flex items-center justify-between shadow-sm">
            <div class="min-w-0">
                <span class="text-[11px] font-black uppercase tracking-wider theme-text-secondary block truncate">Comisión POS</span>
                <div class="mt-1 text-2xl sm:text-3xl font-black font-mono text-[#F5B81C] tracking-tight">
                    {{ number_format(($sessions->avg('pos_commission_rate') ?? 0.013) * 100, 2) }}%
                </div>
                <p class="text-[10px] theme-text-muted mt-0.5 font-mono truncate">Tasa promedio</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-[#F5B81C] shrink-0 ml-2 shadow-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <rect width="20" height="14" x="2" y="5" rx="2"/>
                    <line x1="2" y1="10" x2="22" y2="10"/>
                </svg>
            </div>
        </div>

    </div>

    <!-- ==========================================
         TARJETAS MÓVILES (md:hidden) (CARDS NOTORIAS)
         ========================================== -->
    <div class="md:hidden space-y-3">
        <div class="flex items-center justify-between px-1">
            <span class="text-xs font-bold theme-text-muted font-mono uppercase tracking-wider">Jornadas ({{ $totalCount }})</span>
            <span class="text-[11px] theme-text-muted font-mono">Toca las casillas para consolidar</span>
        </div>
        
        @forelse($sessions as $s)
            @php
                $isOpen = $s->isOpen();
            @endphp
            <div class="notoria-card p-4 space-y-3 {{ $isOpen ? 'border-2 border-emerald-500/70 shadow-sm' : '' }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <input type="checkbox" value="{{ $s->id }}" data-day="{{ $s->day_name }}" data-date="{{ $s->session_date }}"
                               class="session-chk mt-1 w-5 h-5 rounded-lg border-zinc-700 bg-black text-[#F5B81C] cursor-pointer"
                               style="accent-color: #F5B81C !important;"
                               onchange="onSessionCheckChange(this)">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full {{ $isOpen ? 'bg-emerald-500 animate-pulse' : 'bg-zinc-400' }}"></span>
                                <span class="text-base font-black font-mono theme-text-primary">{{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }}</span>
                                <span class="text-xs theme-text-secondary font-bold">({{ $s->day_name }})</span>
                            </div>
                            @if($s->notes)
                                <p class="text-xs theme-text-muted mt-1 italic font-sans">{{ $s->notes }}</p>
                            @endif
                        </div>
                    </div>
                    <div>
                        @if($isOpen)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                                Abierta
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold notoria-subtile theme-text-muted">
                                Cerrada
                            </span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-2 border-t notoria-border font-mono">
                    <span class="theme-text-muted">Comisión POS:</span>
                    <span class="font-bold theme-text-primary">{{ number_format($s->pos_commission_rate * 100, 2) }}%</span>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <a href="{{ route('dashboard', ['session_id' => $s->id]) }}" 
                       class="flex-1 justify-center inline-flex items-center gap-1.5 py-2.5 rounded-xl notoria-subtile hover:border-amber-500 active:scale-95 text-xs font-mono font-bold text-[#F5B81C] hover:text-amber-400 transition-all shadow-sm">
                        <span>Operar Noche</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </a>

                    <form method="POST" action="{{ route('sessions.destroy', $s) }}" class="inline" 
                          onsubmit="return confirm('¿Estás seguro de eliminar esta noche del {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }}? Se borrarán sus inventarios, facturas y cobros asociados.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-2.5 text-xs text-rose-500 active:bg-rose-500/20 font-bold rounded-xl bg-rose-500/10 border border-rose-500/20 transition-all cursor-pointer">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-8 text-center theme-text-muted rounded-xl notoria-card">
                <p class="text-sm font-bold theme-text-primary">No hay noches registradas</p>
                <p class="text-xs theme-text-muted mt-1 font-sans">Apertura una nueva noche para comenzar.</p>
            </div>
        @endforelse
    </div>

    <!-- ==========================================
         TABLA DE NOCHES (DESKTOP: hidden md:block) - CARD NOTORIA
         ========================================== -->
    <div class="hidden md:block notoria-card overflow-hidden shadow-xl">
        <div class="notoria-card-header flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h3 class="text-xs font-black text-white uppercase tracking-wider font-mono">
                    Registro de Jornadas Nocturnas
                </h3>
                <span class="text-xs font-mono text-zinc-300">
                    ({{ $totalCount }} noches en total)
                </span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="selectWeekend()" title="Seleccionar Fin de Semana" class="text-xs text-[#F5B81C] hover:underline font-mono font-bold">
                    + Marcar Fin de Semana
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="notoria-card-header text-white font-mono uppercase text-[11px] tracking-wider border-b notoria-border">
                    <tr>
                        <th class="px-4 py-3.5 w-10 text-center">
                            <input type="checkbox" id="check-all-desktop" onchange="toggleSelectAll(this)" title="Marcar todas" 
                                   class="w-4 h-4 rounded border-zinc-700 bg-black text-[#F5B81C] cursor-pointer"
                                   style="accent-color: #F5B81C !important;">
                        </th>
                        <th class="px-5 py-3.5 text-white font-bold">Fecha</th>
                        <th class="px-5 py-3.5 text-white font-bold">Día</th>
                        <th class="px-5 py-3.5 text-center text-white font-bold">Comisión POS</th>
                        <th class="px-5 py-3.5 text-center text-white font-bold">Estado</th>
                        <th class="px-5 py-3.5 text-white font-bold">Observaciones</th>
                        <th class="px-5 py-3.5 text-right text-white font-bold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y notoria-border font-sans">
                    @forelse($sessions as $s)
                        @php
                            $isOpen = $s->isOpen();
                        @endphp
                        <tr class="hover:bg-slate-500/10 transition-colors {{ $isOpen ? 'bg-[#F5B81C]/[0.05]' : '' }}" data-session-row="{{ $s->id }}">
                            <td class="px-4 py-4 text-center">
                                <input type="checkbox" value="{{ $s->id }}" data-day="{{ $s->day_name }}" data-date="{{ $s->session_date }}"
                                       class="session-chk w-4 h-4 rounded border-zinc-700 bg-black text-[#F5B81C] cursor-pointer"
                                       style="accent-color: #F5B81C !important;"
                                       onchange="onSessionCheckChange(this)">
                            </td>
                            <td class="px-5 py-4 font-mono font-bold theme-text-primary text-sm">
                                <div class="flex items-center gap-2.5">
                                    @if($isOpen)
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-500/50"></span>
                                    @else
                                        <span class="w-2.5 h-2.5 rounded-full bg-zinc-400"></span>
                                    @endif
                                    <span>{{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }}</span>
                                    @if($isOpen)
                                        <span class="text-[9px] font-black text-[#F5B81C] uppercase tracking-wider bg-[#F5B81C]/10 px-2 py-0.5 rounded-md border border-[#F5B81C]/30">
                                            En curso
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4 font-bold theme-text-secondary">
                                {{ $s->day_name }}
                            </td>
                            <td class="px-5 py-4 text-center font-mono font-bold theme-text-primary">
                                {{ number_format($s->pos_commission_rate * 100, 2) }}%
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($isOpen)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-bold bg-emerald-500/15 text-emerald-500 border border-emerald-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Abierta
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold notoria-subtile theme-text-secondary">
                                        Cerrada
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 theme-text-muted font-sans">
                                {{ $s->notes ?? 'Sin observaciones' }}
                            </td>
                            <td class="px-5 py-4 text-right space-x-2.5 whitespace-nowrap">
                                <a href="{{ route('dashboard', ['session_id' => $s->id]) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl notoria-subtile hover:border-amber-500 text-xs font-mono font-bold text-[#F5B81C] hover:text-amber-400 transition-all shadow-sm">
                                    <span>Operar</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                    </svg>
                                </a>

                                <form method="POST" action="{{ route('sessions.destroy', $s) }}" class="inline" 
                                      onsubmit="return confirm('¿Estás seguro de eliminar esta noche del {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }}? Se borrarán sus inventarios, facturas y cobros asociados.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 text-xs text-rose-500 hover:text-rose-400 font-bold rounded-lg hover:bg-rose-500/10 transition-all cursor-pointer">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center theme-text-muted">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <p class="text-sm font-semibold theme-text-secondary">No hay noches registradas</p>
                                    <p class="text-xs theme-text-muted">Apertura una nueva noche para comenzar a operar inventarios, personal y cobros.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ==========================================
         FLOATING ACTION BAR (STICKY BOTTOM PILL)
         ========================================== -->
    <div id="floating-consolidated-bar" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-40 max-w-lg w-[92%] sm:w-auto">
        <div class="bg-slate-900 text-white border-2 border-[#F5B81C] rounded-2xl p-2.5 sm:px-5 sm:py-3 shadow-2xl flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-[#F5B81C] animate-pulse shrink-0"></span>
                <span class="font-bold text-white font-sans text-xs sm:text-sm">
                    <span id="floating-selected-count" class="font-mono text-[#F5B81C] font-black text-sm sm:text-base">0</span> noches seleccionadas
                </span>
                <button type="button" onclick="deselectAll()" class="text-xs text-amber-300 hover:text-white underline ml-1 cursor-pointer font-bold">
                    Desmarcar
                </button>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="openConsolidatedModal()" 
                        class="px-4 py-2 bg-[#F5B81C] hover:bg-[#e5ac18] text-black font-black text-xs rounded-xl transition-all cursor-pointer shadow-md flex items-center gap-1.5 active:scale-95">
                    <span>Ver Resumen</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </button>

                <button type="button" onclick="goToPrintConsolidated()" title="Imprimir Reporte Consolidado"
                        class="p-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-[#F5B81C] hover:text-white transition-all cursor-pointer active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- ==========================================
         MODAL INTERACTIVO DE RESUMEN CONSOLIDADO
         ========================================== -->
    <div id="modal-consolidated-summary" class="hidden fixed inset-0 z-50 bg-black/85 flex items-center justify-center p-3 sm:p-5 backdrop-blur-sm overflow-y-auto">
        <div class="p-4 sm:p-6 rounded-2xl notoria-card notoria-border max-w-4xl w-full shadow-2xl max-h-[92vh] overflow-y-auto space-y-5 my-auto">
            
            <!-- Encabezado Modal -->
            <div class="flex items-start justify-between border-b notoria-border pb-3">
                <div>
                    <span class="text-[11px] uppercase tracking-wider text-amber-600 dark:text-[#F5B81C] font-mono font-black block">
                        Resumen de Cierre • Tío Chu
                    </span>
                    <h2 class="text-lg sm:text-xl font-black theme-text-primary font-sans mt-0.5">
                        Resumen Consolidado de Noches
                    </h2>
                    <p class="text-xs theme-text-secondary mt-0.5 font-mono" id="modal-consolidated-dates">
                        Cargando información...
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="goToPrintConsolidated()" title="Imprimir resumen"
                            class="px-3.5 py-2 rounded-xl bg-[#F5B81C] hover:bg-[#e5ac18] text-black text-xs font-black flex items-center gap-1.5 cursor-pointer transition-all shadow-sm active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span class="hidden sm:inline">Imprimir</span>
                    </button>
                    <button type="button" onclick="closeConsolidatedModal()" 
                            class="w-8 h-8 flex items-center justify-center rounded-xl notoria-subtile hover:border-amber-500 theme-text-primary text-lg font-black cursor-pointer transition-all leading-none">&times;</button>
                </div>
            </div>

            <!-- Loader Spinner -->
            <div id="modal-consolidated-loading" class="py-16 text-center space-y-3">
                <div class="w-10 h-10 border-4 border-[#F5B81C] border-t-transparent rounded-full animate-spin mx-auto"></div>
                <p class="text-xs font-mono theme-text-secondary">Calculando resumen...</p>
            </div>

            <!-- Contenido Dinámico de Datos (Adaptativo Claro & Oscuro) -->
            <div id="modal-consolidated-data" class="hidden space-y-4">
                
                <!-- 1. 4 KPIs Principales -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="p-3.5 rounded-xl notoria-subtile border notoria-border">
                        <span class="text-[10px] theme-text-secondary uppercase font-black tracking-wider block">Total Ventas</span>
                        <div class="mt-1 text-lg sm:text-xl font-black font-mono text-amber-600 dark:text-[#F5B81C]" id="m-kpi-ventas">Bs. 0.00</div>
                        <span class="text-[10px] theme-text-muted font-medium">Principal + Subte + Tienda</span>
                    </div>
                    <div class="p-3.5 rounded-xl notoria-subtile border notoria-border">
                        <span class="text-[10px] theme-text-secondary uppercase font-black tracking-wider block">Total Efectivo</span>
                        <div class="mt-1 text-lg sm:text-xl font-black font-mono text-emerald-600 dark:text-emerald-400" id="m-kpi-efectivo">Bs. 0.00</div>
                        <span class="text-[10px] theme-text-muted font-medium">Efectivo en mano</span>
                    </div>
                    <div class="p-3.5 rounded-xl notoria-subtile border notoria-border">
                        <span class="text-[10px] theme-text-secondary uppercase font-black tracking-wider block">QRs (Banco)</span>
                        <div class="mt-1 text-lg sm:text-xl font-black font-mono text-blue-600 dark:text-blue-400" id="m-kpi-qr">Bs. 0.00</div>
                        <span class="text-[10px] theme-text-muted font-medium">Saldo líquido en cuentas</span>
                    </div>
                    <div class="p-3.5 rounded-xl notoria-subtile border notoria-border">
                        <span class="text-[10px] theme-text-secondary uppercase font-black tracking-wider block">Tarjetero Neto</span>
                        <div class="mt-1 text-lg sm:text-xl font-black font-mono text-purple-600 dark:text-purple-400" id="m-kpi-card-net">Bs. 0.00</div>
                        <span class="text-[10px] theme-text-muted font-medium">Bruto menos 1.30% POS</span>
                    </div>
                </div>

                <!-- 2. Desglose Rápido por Método y Egresos -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="p-3.5 rounded-xl notoria-subtile border notoria-border space-y-2">
                        <div class="flex items-center justify-between border-b notoria-border pb-1.5">
                            <span class="text-xs font-black text-emerald-600 dark:text-emerald-400 uppercase font-mono">Efectivo & Personal</span>
                            <span class="font-black font-mono theme-text-primary text-xs" id="m-pay-cash">Bs. 0.00</span>
                        </div>
                        <div class="text-[11px] space-y-1 theme-text-secondary">
                            <div class="flex justify-between">
                                <span>Principal (neto):</span>
                                <span class="font-mono font-bold theme-text-primary" id="m-pay-cash-principal">Bs. 0.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Subterráneo:</span>
                                <span class="font-mono font-bold theme-text-primary" id="m-pay-cash-subte">Bs. 0.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Tienda:</span>
                                <span class="font-mono font-bold theme-text-primary" id="m-pay-cash-tienda">Bs. 0.00</span>
                            </div>
                            <div class="flex justify-between text-rose-600 dark:text-rose-400 pt-1 border-t notoria-border font-bold">
                                <span>Personal pagado:</span>
                                <span class="font-mono" id="m-pay-personal">Bs. 0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl notoria-subtile border notoria-border space-y-2">
                        <div class="flex items-center justify-between border-b notoria-border pb-1.5">
                            <span class="text-xs font-black text-blue-600 dark:text-blue-400 uppercase font-mono">Cobros QR</span>
                            <span class="font-black font-mono theme-text-primary text-xs" id="m-pay-qr">Bs. 0.00</span>
                        </div>
                        <div class="text-[11px] space-y-1 theme-text-secondary">
                            <div class="flex justify-between">
                                <span>YASTA (Unión):</span>
                                <span class="font-mono font-bold theme-text-primary" id="m-pay-qr-yasta">Bs. 0.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span>YAPE (BCP):</span>
                                <span class="font-mono font-bold theme-text-primary" id="m-pay-qr-yape">Bs. 0.00</span>
                            </div>
                            <div class="flex justify-between text-rose-600 dark:text-rose-400">
                                <span>Cerveza pagada:</span>
                                <span class="font-mono font-bold" id="m-pay-beer">Bs. 0.00</span>
                            </div>
                            <div class="flex justify-between text-blue-600 dark:text-blue-400 font-bold pt-1 border-t notoria-border">
                                <span>QR disponible:</span>
                                <span class="font-mono" id="m-pay-qr-remaining">Bs. 0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl notoria-subtile border notoria-border space-y-2">
                        <div class="flex items-center justify-between border-b notoria-border pb-1.5">
                            <span class="text-xs font-black text-purple-600 dark:text-purple-400 uppercase font-mono">Tarjetero POS</span>
                            <span class="font-black font-mono theme-text-primary text-xs" id="m-pay-card-gross">Bs. 0.00</span>
                        </div>
                        <div class="text-[11px] space-y-1 theme-text-secondary">
                            <div class="flex justify-between">
                                <span>Monto Bruto:</span>
                                <span class="font-mono font-bold theme-text-primary" id="m-pay-card-bruto">Bs. 0.00</span>
                            </div>
                            <div class="flex justify-between text-rose-600 dark:text-rose-400">
                                <span>Descuento POS (1.30%):</span>
                                <span class="font-mono font-bold" id="m-pay-card-comision">- Bs. 0.00</span>
                            </div>
                            <div class="flex justify-between text-emerald-600 dark:text-emerald-400 font-bold pt-1 border-t notoria-border">
                                <span>Neto a cobrar:</span>
                                <span class="font-mono" id="m-pay-card-neto">Bs. 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Resumen por Noche (Estilo Excel Don Ludo) -->
                <div class="space-y-3" id="m-nights-cards">
                    <!-- Populated dynamically -->
                </div>

            </div>

            <!-- Footer Acciones -->
            <div class="flex flex-col sm:flex-row justify-end gap-2.5 pt-3 border-t notoria-border">
                <button type="button" onclick="closeConsolidatedModal()" class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold notoria-subtile theme-text-secondary hover:theme-text-primary hover:border-zinc-400 cursor-pointer w-full sm:w-auto transition-all">
                    Cerrar
                </button>
                <button type="button" onclick="goToPrintConsolidated()" class="px-5 py-2.5 bg-[#F5B81C] text-black font-black text-xs sm:text-sm rounded-xl hover:bg-[#e5ac18] cursor-pointer w-full sm:w-auto flex items-center justify-center gap-1.5 shadow-md shadow-[#F5B81C]/10 transition-all active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Imprimir Resumen</span>
                </button>
            </div>

        </div>
    </div>

</div>

<script>
    function getSelectedSessionIds() {
        const checked = Array.from(document.querySelectorAll('.session-chk:checked'));
        return Array.from(new Set(checked.map(chk => String(chk.value))));
    }

    function onSessionCheckChange(chk) {
        const val = String(chk.value);
        const isChecked = chk.checked;
        // Sincronizar checkbox equivalente en móvil y escritorio
        document.querySelectorAll('.session-chk[value="' + val + '"]').forEach(c => c.checked = isChecked);
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const selected = getSelectedSessionIds();
        const count = selected.length;
        
        const countSpan = document.getElementById('header-selected-count');
        if (countSpan) countSpan.textContent = count;
        
        const floatingBar = document.getElementById('floating-consolidated-bar');
        const floatingCount = document.getElementById('floating-selected-count');
        if (floatingCount) floatingCount.textContent = count;
        
        if (floatingBar) {
            if (count > 0) {
                floatingBar.classList.remove('hidden');
            } else {
                floatingBar.classList.add('hidden');
            }
        }

        // Sincronizar master checkbox con sesiones únicas
        const totalUnique = new Set(Array.from(document.querySelectorAll('.session-chk')).map(c => String(c.value))).size;
        const master = document.getElementById('check-all-desktop');
        if (master && totalUnique > 0) {
            master.checked = count === totalUnique;
        }

        // Resaltar suavemente filas seleccionadas
        document.querySelectorAll('tr[data-session-row]').forEach(tr => {
            const rowId = tr.getAttribute('data-session-row');
            if (selected.includes(rowId)) {
                tr.classList.add('bg-[#F5B81C]/[0.05]');
            } else {
                tr.classList.remove('bg-[#F5B81C]/[0.05]');
            }
        });
    }

    function toggleSelectAll(masterChk) {
        const isChecked = masterChk.checked;
        document.querySelectorAll('.session-chk').forEach(chk => {
            chk.checked = isChecked;
        });
        updateSelectedCount();
    }

    function deselectAll() {
        document.querySelectorAll('.session-chk').forEach(chk => chk.checked = false);
        const master = document.getElementById('check-all-desktop');
        if (master) master.checked = false;
        updateSelectedCount();
    }

    function selectWeekend() {
        deselectAll();
        const weekendDays = ['viernes', 'sábado', 'sabado', 'domingo'];
        const matchedIds = new Set();
        
        document.querySelectorAll('.session-chk').forEach(chk => {
            const day = (chk.getAttribute('data-day') || '').toLowerCase();
            if (weekendDays.some(d => day.includes(d))) {
                matchedIds.add(String(chk.value));
            }
        });
        
        // Si no encontró suficientes de fin de semana, toma las 3 más recientes
        if (matchedIds.size === 0) {
            const allIds = Array.from(new Set(Array.from(document.querySelectorAll('.session-chk')).map(c => String(c.value))));
            allIds.slice(0, 3).forEach(id => matchedIds.add(id));
        }

        matchedIds.forEach(id => {
            document.querySelectorAll('.session-chk[value="' + id + '"]').forEach(c => c.checked = true);
        });
        
        updateSelectedCount();
        openConsolidatedModal();
    }

    async function openConsolidatedModal() {
        let ids = getSelectedSessionIds();
        if (ids.length === 0) {
            selectWeekend();
            ids = getSelectedSessionIds();
        }

        const modal = document.getElementById('modal-consolidated-summary');
        if (!modal) return;
        modal.classList.remove('hidden');
        
        const loading = document.getElementById('modal-consolidated-loading');
        const content = document.getElementById('modal-consolidated-data');
        if (loading) loading.classList.remove('hidden');
        if (content) content.classList.add('hidden');
        
        try {
            const response = await fetch(`{{ route('sessions.consolidatedSummary') }}?session_ids=${ids.join(',')}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            renderConsolidatedData(data);
            if (loading) loading.classList.add('hidden');
            if (content) content.classList.remove('hidden');
        } catch (err) {
            console.error(err);
            alert('Error al calcular el consolidado de noches.');
        }
    }

    function closeConsolidatedModal() {
        const modal = document.getElementById('modal-consolidated-summary');
        if (modal) modal.classList.add('hidden');
    }

    function goToPrintConsolidated() {
        let ids = getSelectedSessionIds();
        if (ids.length === 0) {
            ids = Array.from(document.querySelectorAll('.session-chk')).slice(0, 3).map(c => c.value);
        }
        if (ids.length === 0) return;
        window.location.href = `{{ route('sessions.consolidatedSummary') }}?session_ids=${ids.join(',')}&auto_print=1`;
    }

    function fmtBs(val) {
        return 'Bs. ' + (parseFloat(val) || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function renderConsolidatedData(data) {
        const countLabel = data.count === 1 ? '1 noche seleccionada' : `${data.count} noches seleccionadas`;
        document.getElementById('modal-consolidated-dates').textContent = `${data.date_range} • ${countLabel}`;

        const totals = data.totals || {};
        const principalCash = (data.nights || []).reduce((sum, n) => sum + (parseFloat(n.bars?.principal?.efectivo) || 0), 0);

        // KPIs
        document.getElementById('m-kpi-ventas').textContent = fmtBs(totals.ventas);
        document.getElementById('m-kpi-efectivo').textContent = fmtBs(totals.efectivo);
        document.getElementById('m-kpi-qr').textContent = fmtBs(totals.qr_remaining ?? totals.qr_total);
        document.getElementById('m-kpi-card-net').textContent = fmtBs(totals.card_net);

        // Desglose Rápido
        document.getElementById('m-pay-cash').textContent = fmtBs(totals.efectivo);
        document.getElementById('m-pay-cash-principal').textContent = fmtBs(principalCash);
        document.getElementById('m-pay-cash-subte').textContent = fmtBs(data.bars?.subte?.efectivo);
        document.getElementById('m-pay-cash-tienda').textContent = fmtBs(data.bars?.tienda?.efectivo);
        document.getElementById('m-pay-personal').textContent = fmtBs(totals.personal);

        document.getElementById('m-pay-qr').textContent = fmtBs(totals.qr_total);
        document.getElementById('m-pay-qr-yasta').textContent = fmtBs(totals.qr_yasta);
        document.getElementById('m-pay-qr-yape').textContent = fmtBs(totals.qr_yape);
        document.getElementById('m-pay-beer').textContent = fmtBs(totals.beer_total);
        document.getElementById('m-pay-qr-remaining').textContent = fmtBs(totals.qr_remaining ?? totals.qr_total);

        document.getElementById('m-pay-card-gross').textContent = fmtBs(totals.card_gross);
        document.getElementById('m-pay-card-bruto').textContent = fmtBs(totals.card_gross);
        document.getElementById('m-pay-card-comision').textContent = '- ' + fmtBs(totals.card_commission);
        document.getElementById('m-pay-card-neto').textContent = fmtBs(totals.card_net);

        const barRow = (label, val, cls = 'theme-text-primary') => `
            <div class="flex items-center justify-between text-[11px] py-0.5">
                <span class="theme-text-secondary">${label}</span>
                <span class="font-mono font-semibold ${cls}">${fmtBs(val)}</span>
            </div>`;

        const nightsContainer = document.getElementById('m-nights-cards');
        nightsContainer.innerHTML = (data.nights || []).map(n => {
            const p = n.bars?.principal || { card: 0, qr: 0, qr_net: 0, personal: 0, efectivo: 0, total: 0 };
            const s = n.bars?.subte || { card: 0, qr: 0, efectivo: 0, total: 0 };
            const t = n.bars?.tienda || { card: 0, qr: 0, efectivo: 0, total: 0 };

            return `
            <div class="rounded-xl border notoria-border overflow-hidden">
                <div class="px-4 py-2 notoria-card-header border-b notoria-border flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-black uppercase tracking-wider text-amber-600 dark:text-[#F5B81C]">${n.day_name}</span>
                        <span class="text-xs font-mono font-bold theme-text-primary">${n.date}</span>
                    </div>
                    <div class="text-xs font-black font-mono text-amber-600 dark:text-[#F5B81C]">Total: ${fmtBs(n.ventas)}</div>
                </div>
                <div class="p-3 grid grid-cols-1 md:grid-cols-3 gap-3 notoria-card-elevated">
                    <div class="p-2.5 rounded-lg notoria-subtile border notoria-border space-y-1">
                        <div class="text-[11px] font-black uppercase tracking-wider theme-text-primary border-b notoria-border pb-1 mb-1">Barra Principal</div>
                        ${barRow('Tarjeteros', p.card, 'text-purple-600 dark:text-purple-400')}
                        ${barRow('QRs Principal', p.qr, 'text-blue-600 dark:text-blue-400')}
                        ${barRow('Personal', p.personal, 'text-rose-600 dark:text-rose-400')}
                        ${barRow('Efectivo', p.efectivo, 'text-emerald-600 dark:text-emerald-400')}
                        <div class="flex items-center justify-between text-[11px] pt-1 mt-1 border-t notoria-border font-black">
                            <span class="theme-text-primary uppercase">Total</span>
                            <span class="font-mono text-amber-600 dark:text-[#F5B81C]">${fmtBs(p.total)}</span>
                        </div>
                    </div>

                    <div class="p-2.5 rounded-lg notoria-subtile border notoria-border space-y-1">
                        <div class="text-[11px] font-black uppercase tracking-wider theme-text-primary border-b notoria-border pb-1 mb-1">Barra Subterráneo</div>
                        ${barRow('Tarjetero', s.card, 'text-purple-600 dark:text-purple-400')}
                        ${barRow('QRs', s.qr, 'text-blue-600 dark:text-blue-400')}
                        ${barRow('Efectivo', s.efectivo, 'text-emerald-600 dark:text-emerald-400')}
                        <div class="flex items-center justify-between text-[11px] pt-1 mt-1 border-t notoria-border font-black">
                            <span class="theme-text-primary uppercase">Total</span>
                            <span class="font-mono text-amber-600 dark:text-[#F5B81C]">${fmtBs(s.total)}</span>
                        </div>
                    </div>

                    <div class="p-2.5 rounded-lg notoria-subtile border notoria-border flex flex-col justify-between space-y-2">
                        <div class="space-y-1">
                            <div class="text-[11px] font-black uppercase tracking-wider theme-text-primary border-b notoria-border pb-1 mb-1">Tienda Oficial</div>
                            ${barRow('Tarjetero', t.card, 'text-purple-600 dark:text-purple-400')}
                            ${barRow('QRs', t.qr, 'text-blue-600 dark:text-blue-400')}
                            ${barRow('Efectivo', t.efectivo, 'text-emerald-600 dark:text-emerald-400')}
                            <div class="flex items-center justify-between text-[11px] pt-1 mt-1 border-t notoria-border font-black">
                                <span class="theme-text-primary uppercase">Total</span>
                                <span class="font-mono text-amber-600 dark:text-[#F5B81C]">${fmtBs(t.total)}</span>
                            </div>
                        </div>
                        <div class="pt-1.5 border-t notoria-border space-y-0.5">
                            ${barRow('Total Efectivo', n.efectivo, 'text-emerald-600 dark:text-emerald-400 font-bold')}
                            ${barRow('Cerveza', n.cerveza, 'text-rose-600 dark:text-rose-400 font-bold')}
                        </div>
                    </div>
                </div>
            </div>`;
        }).join('');
    }
</script>
@endsection
