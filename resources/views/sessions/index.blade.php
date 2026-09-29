@extends('layouts.app')

@section('title', 'Historial de Noches')

@section('content')
<div class="space-y-6">

    <!-- Cabecera -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-white/10 pb-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-amber-500/10 text-amber-400 border border-amber-500/20">
                    Control de Operaciones
                </span>
            </div>
            <h2 class="text-2xl font-black text-white tracking-tight">Historial de Noches de Atención</h2>
            <p class="text-xs text-zinc-400">Registro cronológico de jornadas de discoteca, comisiones bancarias y estados de arqueo.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('sessions.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-black tracking-wider uppercase rounded-xl shadow-lg shadow-amber-500/20 active:scale-95 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Aperturar Nueva Noche
            </a>
        </div>
    </div>

    <!-- Tabla Liquid Glass de Noches -->
    <div class="glass-panel rounded-2xl border border-white/10 shadow-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-white/[0.03] border-b border-white/10 text-zinc-400 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-5 py-3.5">Fecha de Noche</th>
                        <th class="px-5 py-3.5">Día de Fiesta</th>
                        <th class="px-5 py-3.5 text-center">Comisión POS</th>
                        <th class="px-5 py-3.5 text-center">Estado</th>
                        <th class="px-5 py-3.5">Observaciones</th>
                        <th class="px-5 py-3.5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($sessions as $s)
                        <tr class="hover:bg-white/[0.04] transition-colors {{ $s->id == ($currentSession->id ?? null) ? 'bg-amber-500/[0.04]' : '' }}">
                            <td class="px-5 py-4 font-mono font-bold text-white text-sm">
                                <div class="flex items-center gap-2.5">
                                    @if($s->id == ($currentSession->id ?? null))
                                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-zinc-600"></span>
                                    @endif
                                    {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }}
                                    @if($s->id == ($currentSession->id ?? null))
                                        <span class="text-[10px] font-bold text-amber-400 uppercase tracking-widest bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">En curso</span>
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
                                @if($s->isOpen())
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        Abierta
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-zinc-800 text-zinc-400 border border-white/5">
                                        Cerrada
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-zinc-400">
                                {{ $s->notes ?? 'Sin observaciones' }}
                            </td>
                            <td class="px-5 py-4 text-right space-x-3 whitespace-nowrap">
                                <a href="{{ route('dashboard', ['session_id' => $s->id]) }}" 
                                   class="inline-flex items-center gap-1 px-3 py-1.5 glass-card hover:bg-amber-500/20 text-amber-300 font-bold rounded-lg border border-amber-500/30 transition-all text-xs">
                                    Operar / Ver Cierre &rarr;
                                </a>

                                <form method="POST" action="{{ route('sessions.destroy', $s) }}" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta noche del {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }}? Se borrarán sus inventarios, facturas y cobros asociados.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-rose-400 hover:text-rose-300 font-semibold hover:underline">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-zinc-500">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <p class="text-sm font-semibold text-zinc-400">No hay noches de fiesta registradas</p>
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
