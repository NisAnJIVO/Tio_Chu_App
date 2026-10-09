@extends('layouts.app')

@section('title', 'Historial de Noches')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    @php
        $openSession = $sessions->firstWhere('status', 'open');
        $closedCount = $sessions->where('status', 'closed')->count();
        $totalCount = $sessions->count();
    @endphp

    <!-- ==========================================
         CABECERA MINIMALISTA (iOS PURE DARK)
         ========================================== -->
    <!-- ==========================================
         CABECERA MINIMALISTA (iOS PURE DARK)
         ========================================== -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-zinc-800/80">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-mono font-bold text-[#F5B81C] uppercase tracking-wider">
                    Control Operativo
                </span>
                <span class="text-zinc-600 text-xs">•</span>
                <span class="text-xs text-zinc-400 font-mono">Jornadas Nocturnas</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-black tracking-tight text-white">
                Historial de Noches de Atención
            </h2>
            <p class="text-xs text-zinc-400 mt-1">
                Registro cronológico de jornadas, comisiones de POS bancario y estados de cierre de caja.
            </p>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto">
            <a href="{{ route('sessions.create') }}" 
               class="w-full sm:w-auto justify-center inline-flex items-center gap-2 px-4 py-2.5 bg-[#F5B81C] hover:bg-[#e5ac18] text-zinc-950 text-xs font-black tracking-wider uppercase rounded-xl shadow-sm active:scale-95 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Aperturar Nueva Noche</span>
            </a>
        </div>
    </div>

    <!-- ==========================================
         KPIs RESUMEN DE JORNADAS
         ========================================== -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        
        <!-- Total Noches -->
        <div class="theme-card rounded-2xl p-3.5 sm:p-5 border border-zinc-800/80 bg-[#09090b]">
            <span class="text-[11px] sm:text-xs font-mono font-semibold text-zinc-400 uppercase tracking-wider">Total Noches</span>
            <div class="mt-1 sm:mt-2 text-xl sm:text-2xl font-black font-mono text-white tracking-tight">
                {{ $totalCount }}
            </div>
            <p class="text-[10px] sm:text-[11px] text-zinc-500 mt-0.5 sm:mt-1 font-mono truncate">Jornadas registradas</p>
        </div>

        <!-- Noche Activa -->
        <div class="theme-card rounded-2xl p-3.5 sm:p-5 border {{ $openSession ? 'border-emerald-500/30' : 'border-zinc-800/80' }} bg-[#09090b]">
            <span class="text-[11px] sm:text-xs font-mono font-semibold {{ $openSession ? 'text-emerald-400' : 'text-zinc-400' }} uppercase tracking-wider">Noche en Curso</span>
            <div class="mt-1 sm:mt-2 text-xl sm:text-2xl font-black font-mono {{ $openSession ? 'text-emerald-400' : 'text-zinc-500' }} tracking-tight truncate">
                @if($openSession)
                    {{ $openSession->day_name }}
                @else
                    Ninguna
                @endif
            </div>
            <p class="text-[10px] sm:text-[11px] text-zinc-500 mt-0.5 sm:mt-1 font-mono truncate">
                @if($openSession)
                    {{ \Carbon\Carbon::parse($openSession->session_date)->format('d/m/Y') }} (Abierta)
                @else
                    Todas cerradas
                @endif
            </p>
        </div>

        <!-- Noches Cerradas -->
        <div class="theme-card rounded-2xl p-3.5 sm:p-5 border border-zinc-800/80 bg-[#09090b]">
            <span class="text-[11px] sm:text-xs font-mono font-semibold text-zinc-400 uppercase tracking-wider">Noches Cerradas</span>
            <div class="mt-1 sm:mt-2 text-xl sm:text-2xl font-black font-mono text-zinc-200 tracking-tight">
                {{ $closedCount }}
            </div>
            <p class="text-[10px] sm:text-[11px] text-zinc-500 mt-0.5 sm:mt-1 font-mono truncate">Arqueadas en Cierre</p>
        </div>

        <!-- Comisión POS Promedio -->
        <div class="theme-card rounded-2xl p-3.5 sm:p-5 border border-zinc-800/80 bg-[#09090b]">
            <span class="text-[11px] sm:text-xs font-mono font-semibold text-zinc-400 uppercase tracking-wider">Comisión POS</span>
            <div class="mt-1 sm:mt-2 text-xl sm:text-2xl font-black font-mono text-[#F5B81C] tracking-tight">
                {{ number_format(($sessions->avg('pos_commission_rate') ?? 0.013) * 100, 2) }}%
            </div>
            <p class="text-[10px] sm:text-[11px] text-zinc-500 mt-0.5 sm:mt-1 font-mono truncate">Tasa promedio</p>
        </div>

    </div>

    <!-- ==========================================
         TARJETAS MÓVILES (md:hidden)
         ========================================== -->
    <div class="md:hidden space-y-3">
        <div class="flex items-center justify-between px-1">
            <span class="text-xs font-bold text-zinc-400 font-mono uppercase tracking-wider">Jornadas ({{ $totalCount }})</span>
        </div>
        
        @forelse($sessions as $s)
            @php
                $isOpen = $s->isOpen();
            @endphp
            <div class="theme-card rounded-2xl p-4 border {{ $isOpen ? 'border-emerald-500/40 bg-emerald-950/10' : 'border-zinc-800/80 bg-[#09090b]' }} space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $isOpen ? 'bg-emerald-500 animate-pulse' : 'bg-zinc-600' }}"></span>
                            <span class="text-base font-bold font-mono text-white">{{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }}</span>
                            <span class="text-xs text-zinc-400 font-medium">({{ $s->day_name }})</span>
                        </div>
                        @if($s->notes)
                            <p class="text-xs text-zinc-400 mt-1 italic">{{ $s->notes }}</p>
                        @endif
                    </div>
                    <div>
                        @if($isOpen)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                Abierta
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-zinc-900 text-zinc-400 border border-zinc-800">
                                Cerrada
                            </span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-2 border-t border-zinc-800/60 font-mono">
                    <span class="text-zinc-500">Comisión POS:</span>
                    <span class="font-bold text-zinc-300">{{ number_format($s->pos_commission_rate * 100, 2) }}%</span>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <a href="{{ route('dashboard', ['session_id' => $s->id]) }}" 
                       class="flex-1 justify-center inline-flex items-center gap-1.5 py-2.5 rounded-xl bg-zinc-900 border border-zinc-700/80 active:bg-zinc-800 text-xs font-mono font-bold text-[#F5B81C] transition-all">
                        <span>Operar Noche</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </a>

                    <form method="POST" action="{{ route('sessions.destroy', $s) }}" class="inline" 
                          onsubmit="return confirm('¿Estás seguro de eliminar esta noche del {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }}? Se borrarán sus inventarios, facturas y cobros asociados.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-2.5 text-xs text-rose-400 active:bg-rose-500/20 font-semibold rounded-xl bg-rose-500/10 border border-rose-500/20 transition-all cursor-pointer">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-zinc-500 theme-card rounded-2xl border border-zinc-800/80 bg-[#09090b]">
                <p class="text-sm font-semibold text-zinc-400">No hay noches registradas</p>
                <p class="text-xs text-zinc-500 mt-1">Apertura una nueva noche para comenzar.</p>
            </div>
        @endforelse
    </div>

    <!-- ==========================================
         TABLA DE NOCHES (DESKTOP: hidden md:block)
         ========================================== -->
    <div class="hidden md:block theme-card rounded-2xl border border-zinc-800/80 bg-[#09090b] shadow-xl overflow-hidden">
        <div class="px-5 py-3.5 bg-zinc-950/60 border-b border-zinc-800/80 flex items-center justify-between">
            <h3 class="text-xs font-bold text-zinc-200 uppercase tracking-wider font-mono">
                Registro de Jornadas Nocturnas
            </h3>
            <span class="text-xs font-mono text-zinc-500">
                Mostrando {{ $totalCount }} noches
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-zinc-950/40 border-b border-zinc-800/80 text-zinc-400 font-mono uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">Fecha</th>
                        <th class="px-5 py-3.5">Día</th>
                        <th class="px-5 py-3.5 text-center">Comisión POS</th>
                        <th class="px-5 py-3.5 text-center">Estado</th>
                        <th class="px-5 py-3.5">Observaciones</th>
                        <th class="px-5 py-3.5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60 font-sans">
                    @forelse($sessions as $s)
                        @php
                            $isOpen = $s->isOpen();
                        @endphp
                        <tr class="hover:bg-zinc-900/40 transition-colors {{ $isOpen ? 'bg-[#F5B81C]/[0.02]' : '' }}">
                            <td class="px-5 py-4 font-mono font-bold text-white text-sm">
                                <div class="flex items-center gap-2.5">
                                    @if($isOpen)
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-zinc-600"></span>
                                    @endif
                                    <span>{{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }}</span>
                                    @if($isOpen)
                                        <span class="text-[9px] font-bold text-[#F5B81C] uppercase tracking-wider bg-[#F5B81C]/10 px-2 py-0.5 rounded-md border border-[#F5B81C]/30">
                                            En curso
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4 font-semibold text-zinc-200">
                                {{ $s->day_name }}
                            </td>
                            <td class="px-5 py-4 text-center font-mono font-bold text-zinc-300">
                                {{ number_format($s->pos_commission_rate * 100, 2) }}%
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($isOpen)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Abierta
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-zinc-900 text-zinc-400 border border-zinc-800">
                                        Cerrada
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-zinc-400 font-sans">
                                {{ $s->notes ?? 'Sin observaciones' }}
                            </td>
                            <td class="px-5 py-4 text-right space-x-2.5 whitespace-nowrap">
                                <a href="{{ route('dashboard', ['session_id' => $s->id]) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-zinc-900 border border-zinc-800 hover:border-zinc-700 text-xs font-mono font-bold text-[#F5B81C] hover:text-amber-300 transition-all">
                                    <span>Operar</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                    </svg>
                                </a>

                                <form method="POST" action="{{ route('sessions.destroy', $s) }}" class="inline" 
                                      onsubmit="return confirm('¿Estás seguro de eliminar esta noche del {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }}? Se borrarán sus inventarios, facturas y cobros asociados.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 text-xs text-rose-400 hover:text-rose-300 font-semibold rounded-lg hover:bg-rose-500/10 border border-transparent hover:border-rose-500/20 transition-all cursor-pointer">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-zinc-500">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <p class="text-sm font-semibold text-zinc-400">No hay noches registradas</p>
                                    <p class="text-xs text-zinc-500">Apertura una nueva noche para comenzar a operar inventarios, personal y cobros.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
