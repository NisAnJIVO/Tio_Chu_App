@extends('layouts.app')

@section('title', 'Historial de Noches')

@section('content')
<div class="space-y-6">

    <!-- Cabecera -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Historial de Noches de Atención</h2>
            <p class="text-xs text-zinc-600">Registro cronológico de noches operadas, comisiones bancarias y estados de apertura o cierre.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('sessions.create') }}" 
               class="inline-flex items-center px-3 py-1.5 bg-zinc-900 text-white text-xs font-medium rounded hover:bg-zinc-800 transition-colors">
                + Nueva Noche
            </a>
        </div>
    </div>

    <!-- Tabla de Noches -->
    <div class="bg-white border border-zinc-200 rounded overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-zinc-50 border-b border-zinc-200 text-zinc-600 font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Fecha de Noche</th>
                        <th class="px-4 py-3">Día</th>
                        <th class="px-4 py-3 text-center">Tasa Comisión POS</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3">Observaciones</th>
                        <th class="px-4 py-3 text-right">Operar Noche</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    @forelse($sessions as $s)
                        <tr class="hover:bg-zinc-50/50">
                            <td class="px-4 py-3 font-mono font-medium text-zinc-900">
                                {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 font-medium text-zinc-800">{{ $s->day_name }}</td>
                            <td class="px-4 py-3 text-center font-mono text-zinc-700">
                                {{ number_format($s->pos_commission_rate * 100, 2) }}%
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($s->isOpen())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Abierta
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-zinc-100 text-zinc-600 border border-zinc-200">
                                        Cerrada
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-zinc-600">{{ $s->notes ?? '—' }}</td>
                            <td class="px-4 py-3 text-right space-x-3 whitespace-nowrap">
                                <a href="{{ route('dashboard', ['session_id' => $s->id]) }}" 
                                   class="text-zinc-900 hover:underline font-semibold">
                                    Ver Cierre &rarr;
                                </a>

                                <form method="POST" action="{{ route('sessions.destroy', $s) }}" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta noche del {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }}? Se borrarán sus inventarios, facturas y cobros asociados.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-medium">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-zinc-600">
                                No hay noches registradas. Crea una para iniciar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
