@extends('layouts.app')

@section('title', 'Personal y Turnos')

@section('content')
<div class="space-y-5">

    <!-- Cabecera Simple -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Personal y Turnos</h2>
            <p class="text-xs text-zinc-600">Lista del personal organizado por día de trabajo.</p>
        </div>

        <a href="{{ route('staff.create') }}" 
           class="px-3 py-1.5 bg-zinc-900 text-white text-xs font-medium rounded hover:bg-zinc-800 transition-colors">
            + Nuevo Personal
        </a>
    </div>

    <!-- 3 Apartados por Día (Viernes, Sábado, Domingo y Todos) -->
    <div class="flex border-b border-zinc-200 gap-1 text-xs">
        <a href="{{ route('staff.index', ['day' => 'viernes']) }}" 
           class="px-4 py-2 border-b-2 font-medium {{ $currentDay === 'viernes' ? 'border-zinc-900 text-zinc-900 font-bold bg-white' : 'border-transparent text-zinc-600 hover:text-zinc-900' }}">
            Viernes ({{ $countViernes }})
        </a>
        <a href="{{ route('staff.index', ['day' => 'sabado']) }}" 
           class="px-4 py-2 border-b-2 font-medium {{ $currentDay === 'sabado' ? 'border-zinc-900 text-zinc-900 font-bold bg-white' : 'border-transparent text-zinc-600 hover:text-zinc-900' }}">
            Sábado ({{ $countSabado }})
        </a>
        <a href="{{ route('staff.index', ['day' => 'domingo']) }}" 
           class="px-4 py-2 border-b-2 font-medium {{ $currentDay === 'domingo' ? 'border-zinc-900 text-zinc-900 font-bold bg-white' : 'border-transparent text-zinc-600 hover:text-zinc-900' }}">
            Domingo ({{ $countDomingo }})
        </a>
        <a href="{{ route('staff.index', ['day' => 'todos']) }}" 
           class="px-4 py-2 border-b-2 font-medium {{ $currentDay === 'todos' ? 'border-zinc-900 text-zinc-900 font-bold bg-white' : 'border-transparent text-zinc-600 hover:text-zinc-900' }}">
            Todos ({{ $countTodos }})
        </a>
    </div>

    <!-- Tabla Simple y Limpia -->
    <div class="bg-white border border-zinc-200 rounded overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-zinc-50 border-b border-zinc-200 text-zinc-600 font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-2.5 w-12 text-center">N°</th>
                        <th class="px-4 py-2.5">Nombre</th>
                        <th class="px-4 py-2.5">Rol / Cargo</th>
                        <th class="px-4 py-2.5">Área Asignada</th>
                        <th class="px-4 py-2.5 text-right">Pago por Turno</th>
                        <th class="px-4 py-2.5 text-right w-24">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    @forelse($staffMembers as $index => $member)
                        @php
                            $pay = match($currentDay) {
                                'viernes' => $member->getPayForDay('Viernes'),
                                'sabado' => $member->getPayForDay('Sábado'),
                                'domingo' => $member->getPayForDay('Domingo'),
                                default => $member->default_pay,
                            };
                        @endphp
                        <tr class="hover:bg-zinc-50/50">
                            <td class="px-4 py-2 text-center font-mono text-zinc-600">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 font-bold text-zinc-900 uppercase">{{ $member->name }}</td>
                            <td class="px-4 py-2 text-zinc-700">{{ $member->role }}</td>
                            <td class="px-4 py-2 text-zinc-600">{{ $member->assigned_bar ?? 'General' }}</td>
                            <td class="px-4 py-2 text-right font-mono font-bold text-zinc-900">
                                Bs. {{ number_format($pay, 2) }}
                            </td>
                            <td class="px-4 py-2 text-right whitespace-nowrap">
                                <a href="{{ route('staff.edit', $member) }}" class="text-zinc-700 hover:text-zinc-900 font-medium mr-2">Editar</a>
                                <form method="POST" action="{{ route('staff.destroy', $member) }}" class="inline" onsubmit="return confirm('¿Eliminar a {{ $member->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Borrar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-zinc-500">
                                No hay personal programado para {{ ucfirst($currentDay) }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
