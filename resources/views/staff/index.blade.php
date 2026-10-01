@extends('layouts.app')

@section('title', 'Personal y Turnos')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- ==========================================
         CABECERA MINIMALISTA (iOS PURE DARK)
         ========================================== -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-zinc-800/80">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-mono font-bold text-[#F5B81C] uppercase tracking-wider">
                    Cuadrilla Nocturna
                </span>
                <span class="text-zinc-600 text-xs">•</span>
                <span class="text-xs text-zinc-400 font-mono">Tío Chu Club</span>
            </div>
            <h2 class="text-2xl font-black tracking-tight text-white">
                Personal y Turnos
            </h2>
            <p class="text-xs text-zinc-400 mt-1">
                Nómina de trabajadores, meseros, barra y seguridad con jornales por turno.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('staffPayments.index') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 bg-zinc-900 border border-zinc-800 hover:border-zinc-700 text-zinc-300 hover:text-white text-xs font-bold rounded-xl transition-all">
                <svg class="w-3.5 h-3.5 text-[#F5B81C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Planilla de Pagos</span>
            </a>

            <button type="button" onclick="openCreateStaffDrawer()"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-[#F5B81C] hover:bg-[#e5ac18] text-zinc-950 text-xs font-black rounded-xl transition-all shadow-md shadow-[#F5B81C]/10 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                <span>+ Nuevo Personal</span>
            </button>
        </div>
    </div>

    <!-- Pestañas por Día: iOS Segmented Control -->
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="inline-flex p-1 bg-zinc-950 border border-zinc-800 rounded-2xl gap-1 text-xs font-mono">
            <a href="{{ route('staff.index', ['day' => 'viernes']) }}" 
               class="px-4 py-2 rounded-xl font-bold transition-all {{ $currentDay === 'viernes' ? 'bg-zinc-800 text-white border border-zinc-700/60 shadow-sm' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-900/60' }}">
                Viernes <span class="ml-1 text-[11px] {{ $currentDay === 'viernes' ? 'text-[#F5B81C]' : 'text-zinc-500' }}">({{ $countViernes }})</span>
            </a>
            <a href="{{ route('staff.index', ['day' => 'sabado']) }}" 
               class="px-4 py-2 rounded-xl font-bold transition-all {{ $currentDay === 'sabado' ? 'bg-zinc-800 text-white border border-zinc-700/60 shadow-sm' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-900/60' }}">
                Sábado <span class="ml-1 text-[11px] {{ $currentDay === 'sabado' ? 'text-[#F5B81C]' : 'text-zinc-500' }}">({{ $countSabado }})</span>
            </a>
            <a href="{{ route('staff.index', ['day' => 'domingo']) }}" 
               class="px-4 py-2 rounded-xl font-bold transition-all {{ $currentDay === 'domingo' ? 'bg-zinc-800 text-white border border-zinc-700/60 shadow-sm' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-900/60' }}">
                Domingo <span class="ml-1 text-[11px] {{ $currentDay === 'domingo' ? 'text-[#F5B81C]' : 'text-zinc-500' }}">({{ $countDomingo }})</span>
            </a>
            <a href="{{ route('staff.index', ['day' => 'todos']) }}" 
               class="px-4 py-2 rounded-xl font-bold transition-all {{ $currentDay === 'todos' ? 'bg-zinc-800 text-white border border-zinc-700/60 shadow-sm' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-900/60' }}">
                Todos <span class="ml-1 text-[11px] {{ $currentDay === 'todos' ? 'text-[#F5B81C]' : 'text-zinc-500' }}">({{ $countTodos }})</span>
            </a>
        </div>

        <div class="text-xs text-zinc-400 font-mono">
            Mostrando <span class="text-zinc-100 font-bold">{{ $staffMembers->count() }}</span> integrantes
        </div>
    </div>

    <!-- Tabla Minimalista de Personal -->
    <div class="theme-card rounded-2xl border border-zinc-800/80 bg-[#09090b] overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-zinc-950/60 border-b border-zinc-800/80 text-zinc-400 font-mono uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5 w-12 text-center">N°</th>
                        <th class="px-4 py-3.5">Trabajador</th>
                        <th class="px-4 py-3.5">Contacto / Celular</th>
                        <th class="px-4 py-3.5">Cargo / Rol</th>
                        <th class="px-4 py-3.5">Área Asignada</th>
                        <th class="px-4 py-3.5 text-right">Jornal del Turno</th>
                        <th class="px-4 py-3.5 text-right w-24">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60 font-sans">
                    @forelse($staffMembers as $index => $member)
                        @php
                            $pay = match($currentDay) {
                                'viernes' => $member->getPayForDay('Viernes'),
                                'sabado' => $member->getPayForDay('Sábado'),
                                'domingo' => $member->getPayForDay('Domingo'),
                                default => $member->friday_pay ?? $member->saturday_pay ?? $member->sunday_pay ?? $member->default_pay ?? 0,
                            };
                        @endphp
                        <tr class="hover:bg-zinc-900/40 transition-colors">
                            <td class="px-4 py-3 text-center text-zinc-500 font-mono font-bold">{{ $index + 1 }}</td>
                            
                            <!-- Nombre + Turnos programados -->
                            <td class="px-4 py-3">
                                <div class="font-bold text-white text-sm tracking-tight">{{ $member->name }}</div>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded {{ $member->works_friday ? 'bg-zinc-800 text-zinc-300 border border-zinc-700/60' : 'text-zinc-600 bg-zinc-950' }}">
                                        Vie
                                    </span>
                                    <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded {{ $member->works_saturday ? 'bg-zinc-800 text-zinc-300 border border-zinc-700/60' : 'text-zinc-600 bg-zinc-950' }}">
                                        Sáb
                                    </span>
                                    <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded {{ $member->works_sunday ? 'bg-zinc-800 text-zinc-300 border border-zinc-700/60' : 'text-zinc-600 bg-zinc-950' }}">
                                        Dom
                                    </span>
                                </div>
                            </td>

                            <!-- Celular / Contacto (Dato Real de la BD) -->
                            <td class="px-4 py-3">
                                @if(!empty($member->phone))
                                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-zinc-900 border border-zinc-800 text-zinc-200 font-mono text-xs">
                                        <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        <span>{{ $member->phone }}</span>
                                    </div>
                                @else
                                    <span class="text-zinc-600 font-mono text-xs">— Sin número —</span>
                                @endif
                            </td>

                            <!-- Rol / Cargo -->
                            <td class="px-4 py-3">
                                @php
                                    $displayRole = str_ireplace(['Staff / ', 'Staff/', ' (S)', '(S)'], '', $member->role);
                                @endphp
                                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-medium bg-zinc-900 border border-zinc-800 text-zinc-300">
                                    {{ $displayRole }}
                                </span>
                            </td>

                            <!-- Área Asignada -->
                            <td class="px-4 py-3 text-zinc-300">
                                @php
                                    $displayBar = str_ireplace(['Pista / Mozos', 'Pista o mozos', 'Pista', 'Seguridad / Puerta'], ['Meseros', 'Meseros', 'Meseros', 'Seguridad'], $member->assigned_bar ?? 'General');
                                @endphp
                                {{ $displayBar }}
                            </td>

                            <!-- Jornal -->
                            <td class="px-4 py-3 text-right">
                                <span class="font-mono font-bold text-[#F5B81C] text-sm">
                                    Bs. {{ number_format($pay, 2) }}
                                </span>
                            </td>

                            <!-- Acción Editar (Sin flechas desfasadas) -->
                            <td class="px-4 py-3 text-right">
                                <button type="button"
                                        onclick="openEditStaffDrawer(
                                            {{ $member->id }},
                                            {{ json_encode($member->name) }},
                                            {{ json_encode($displayRole) }},
                                            {{ json_encode($displayBar) }},
                                            {{ $member->works_friday ? 'true' : 'false' }},
                                            {{ $member->works_saturday ? 'true' : 'false' }},
                                            {{ $member->works_sunday ? 'true' : 'false' }},
                                            {{ $member->default_pay ?? 100 }},
                                            {{ $member->friday_pay ?? 100 }},
                                            {{ $member->saturday_pay ?? 110 }},
                                            {{ $member->sunday_pay ?? 110 }},
                                            {{ json_encode($member->phone ?? '') }},
                                            {{ $member->is_active ? 1 : 0 }}
                                        )"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-zinc-900/80 hover:bg-zinc-800 border border-zinc-800 text-zinc-300 hover:text-white text-xs font-semibold transition-all cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-[#F5B81C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span>Editar</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-zinc-500 font-sans">
                                No hay personal activo registrado para este turno.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ==========================================
     DRAWER OVERLAY
     ========================================== -->
<div class="drawer-overlay" id="staff-drawer-overlay" onclick="closeStaffDrawer()"></div>

<!-- ==========================================
     DRAWER: REGISTRAR NUEVO PERSONAL
     ========================================== -->
<div class="drawer-panel" id="drawer-create-staff">
    <div class="drawer-header">
        <div>
            <h2 class="text-base font-black text-white tracking-tight">Registrar Personal</h2>
            <span class="text-xs font-bold text-[#F5B81C] tracking-wide block mt-0.5">Nuevo Integrante de Cuadrilla</span>
        </div>
        <button type="button" onclick="closeStaffDrawer()" class="w-8 h-8 flex items-center justify-center rounded-xl bg-zinc-900 border border-zinc-800 text-zinc-400 hover:text-white transition-all cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <form method="POST" action="{{ route('staff.store') }}" class="drawer-body space-y-4">
        @csrf

        <!-- Nombre -->
        <div>
            <label for="c_name" class="block text-xs font-bold text-zinc-300 mb-1.5">
                Nombre Completo o Apodo <span class="text-[#F5B81C]">*</span>
            </label>
            <input type="text" name="name" id="c_name" required
                   placeholder="Ej. MAURI, ALEX (S), KELLY, ARIEL"
                   class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-zinc-900 border border-zinc-800 text-white placeholder-zinc-500 uppercase focus:outline-none focus:border-[#F5B81C] transition-all">
        </div>

        <!-- Rol y Área -->
        <div class="grid grid-cols-2 gap-3.5">
            <div>
                <label for="c_role" class="block text-xs font-bold text-zinc-300 mb-1.5">
                    Cargo / Rol <span class="text-[#F5B81C]">*</span>
                </label>
                <select name="role" id="c_role" required
                        class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-zinc-900 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] transition-all cursor-pointer">
                    <option value="Mesero">Mesero</option>
                    <option value="Limpieza">Limpieza</option>
                    <option value="Seguridad">Seguridad</option>
                    <option value="Bartender Principal">Bartender Principal</option>
                    <option value="Bartender Subterráneo">Bartender Subterráneo</option>
                    <option value="DJ / Sonido">DJ / Sonido</option>
                    <option value="Administrador">Administrador</option>
                </select>
            </div>
            <div>
                <label for="c_assigned_bar" class="block text-xs font-bold text-zinc-300 mb-1.5">
                    Área Asignada
                </label>
                <select name="assigned_bar" id="c_assigned_bar"
                        class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-zinc-900 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] transition-all cursor-pointer">
                    <option value="Meseros">Meseros</option>
                    <option value="Barra Principal">Barra Principal</option>
                    <option value="Barra Subterráneo">Barra Subterráneo</option>
                    <option value="Seguridad">Seguridad</option>
                    <option value="Tienda">Tienda</option>
                    <option value="General">General</option>
                </select>
            </div>
        </div>

        <!-- Teléfono / Celular (Opcional) -->
        <div>
            <label for="c_phone" class="block text-xs font-bold text-zinc-300 mb-1.5">
                Teléfono / Celular (Opcional)
            </label>
            <div class="flex items-center rounded-xl bg-zinc-900 border border-zinc-800 px-3.5 py-2 focus-within:border-[#F5B81C] transition-all">
                <svg class="w-4 h-4 text-zinc-500 mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <input type="text" name="phone" id="c_phone" placeholder="Ej. 70012345"
                       class="w-full text-sm font-mono bg-transparent border-0 text-white placeholder-zinc-500 focus:outline-none p-0">
            </div>
        </div>

        <!-- Días de Turno Programados -->
        <div class="p-3.5 rounded-xl bg-zinc-950 border border-zinc-800/80 space-y-2">
            <span class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Días de Turno Habitual</span>
            <div class="grid grid-cols-3 gap-2 pt-1">
                <label class="flex items-center gap-2 p-2 rounded-lg bg-zinc-900 border border-zinc-800 hover:border-zinc-700 cursor-pointer transition-all">
                    <input type="checkbox" name="works_friday" value="1" checked
                           class="w-4 h-4 rounded border-zinc-700 bg-zinc-950 text-[#F5B81C] focus:ring-0 cursor-pointer">
                    <span class="text-xs font-semibold text-zinc-200">Viernes</span>
                </label>
                <label class="flex items-center gap-2 p-2 rounded-lg bg-zinc-900 border border-zinc-800 hover:border-zinc-700 cursor-pointer transition-all">
                    <input type="checkbox" name="works_saturday" value="1" checked
                           class="w-4 h-4 rounded border-zinc-700 bg-zinc-950 text-[#F5B81C] focus:ring-0 cursor-pointer">
                    <span class="text-xs font-semibold text-zinc-200">Sábado</span>
                </label>
                <label class="flex items-center gap-2 p-2 rounded-lg bg-zinc-900 border border-zinc-800 hover:border-zinc-700 cursor-pointer transition-all">
                    <input type="checkbox" name="works_sunday" value="1" checked
                           class="w-4 h-4 rounded border-zinc-700 bg-zinc-950 text-[#F5B81C] focus:ring-0 cursor-pointer">
                    <span class="text-xs font-semibold text-zinc-200">Domingo</span>
                </label>
            </div>
        </div>

        <!-- Tarifas de Pago por Noche -->
        <div>
            <label class="block text-xs font-bold text-zinc-300 mb-1.5">Tarifas por Noche (Bs.)</label>
            <div class="grid grid-cols-4 gap-2">
                <div>
                    <label for="c_default_pay" class="block text-[10px] font-mono text-zinc-400 mb-1">Base</label>
                    <input type="number" step="5" name="default_pay" id="c_default_pay" required value="100"
                           class="w-full px-2.5 py-2 rounded-xl text-xs font-mono font-bold bg-zinc-900 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] transition-all">
                </div>
                <div>
                    <label for="c_friday_pay" class="block text-[10px] font-mono text-zinc-400 mb-1">Vie</label>
                    <input type="number" step="5" name="friday_pay" id="c_friday_pay" value="100"
                           class="w-full px-2.5 py-2 rounded-xl text-xs font-mono font-bold bg-zinc-900 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] transition-all">
                </div>
                <div>
                    <label for="c_saturday_pay" class="block text-[10px] font-mono text-zinc-400 mb-1">Sáb</label>
                    <input type="number" step="5" name="saturday_pay" id="c_saturday_pay" value="110"
                           class="w-full px-2.5 py-2 rounded-xl text-xs font-mono font-bold bg-zinc-900 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] transition-all">
                </div>
                <div>
                    <label for="c_sunday_pay" class="block text-[10px] font-mono text-zinc-400 mb-1">Dom</label>
                    <input type="number" step="5" name="sunday_pay" id="c_sunday_pay" value="110"
                           class="w-full px-2.5 py-2 rounded-xl text-xs font-mono font-bold bg-zinc-900 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] transition-all">
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="pt-5 border-t border-zinc-800 flex items-center justify-end gap-2.5 mt-6">
            <button type="button" onclick="closeStaffDrawer()" 
                    class="px-4 py-2 rounded-xl border border-zinc-800 text-zinc-400 hover:text-white text-xs font-bold transition-all cursor-pointer">
                Cancelar
            </button>
            <button type="submit" 
                    class="px-5 py-2 rounded-xl bg-[#F5B81C] hover:bg-[#e5ac18] text-zinc-950 text-xs font-black uppercase tracking-wider transition-all cursor-pointer">
                Guardar Personal
            </button>
        </div>
    </form>
</div>

<!-- ==========================================
     DRAWER: EDITAR PERSONAL
     ========================================== -->
<div class="drawer-panel" id="drawer-edit-staff">
    <div class="drawer-header">
        <div>
            <h2 class="text-base font-black text-white tracking-tight" id="edit-staff-title">Editar Personal</h2>
            <span class="text-xs font-bold text-[#F5B81C] tracking-wide block mt-0.5" id="edit-staff-badge">Ficha de Personal</span>
        </div>
        <button type="button" onclick="closeStaffDrawer()" class="w-8 h-8 flex items-center justify-center rounded-xl bg-zinc-900 border border-zinc-800 text-zinc-400 hover:text-white transition-all cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <form method="POST" id="edit-staff-form" action="" class="drawer-body space-y-4">
        @csrf
        @method('PUT')

        <!-- Nombre -->
        <div>
            <label for="e_name" class="block text-xs font-bold text-zinc-300 mb-1.5">
                Nombre Completo o Apodo <span class="text-[#F5B81C]">*</span>
            </label>
            <input type="text" name="name" id="e_name" required
                   class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-zinc-900 border border-zinc-800 text-white placeholder-zinc-500 uppercase focus:outline-none focus:border-[#F5B81C] transition-all">
        </div>

        <!-- Rol y Área -->
        <div class="grid grid-cols-2 gap-3.5">
            <div>
                <label for="e_role" class="block text-xs font-bold text-zinc-300 mb-1.5">
                    Cargo / Rol <span class="text-[#F5B81C]">*</span>
                </label>
                <select name="role" id="e_role" required
                        class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-zinc-900 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] transition-all cursor-pointer">
                    <option value="Mesero">Mesero</option>
                    <option value="Limpieza">Limpieza</option>
                    <option value="Seguridad">Seguridad</option>
                    <option value="Bartender Principal">Bartender Principal</option>
                    <option value="Bartender Subterráneo">Bartender Subterráneo</option>
                    <option value="DJ / Sonido">DJ / Sonido</option>
                    <option value="Administrador">Administrador</option>
                </select>
            </div>
            <div>
                <label for="e_assigned_bar" class="block text-xs font-bold text-zinc-300 mb-1.5">
                    Área Asignada
                </label>
                <select name="assigned_bar" id="e_assigned_bar"
                        class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-zinc-900 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] transition-all cursor-pointer">
                    <option value="Meseros">Meseros</option>
                    <option value="Barra Principal">Barra Principal</option>
                    <option value="Barra Subterráneo">Barra Subterráneo</option>
                    <option value="Seguridad">Seguridad</option>
                    <option value="Tienda">Tienda</option>
                    <option value="General">General</option>
                </select>
            </div>
        </div>

        <!-- Teléfono / Celular (De la BD) -->
        <div>
            <label for="e_phone" class="block text-xs font-bold text-zinc-300 mb-1.5">
                Teléfono / Celular (Opcional)
            </label>
            <div class="flex items-center rounded-xl bg-zinc-900 border border-zinc-800 px-3.5 py-2 focus-within:border-[#F5B81C] transition-all">
                <svg class="w-4 h-4 text-zinc-500 mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <input type="text" name="phone" id="e_phone" placeholder="Ej. 70012345"
                       class="w-full text-sm font-mono bg-transparent border-0 text-white placeholder-zinc-500 focus:outline-none p-0">
            </div>
        </div>

        <!-- Días de Turno Programados -->
        <div class="p-3.5 rounded-xl bg-zinc-950 border border-zinc-800/80 space-y-2">
            <span class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Días de Turno Habitual</span>
            <div class="grid grid-cols-3 gap-2 pt-1">
                <label class="flex items-center gap-2 p-2 rounded-lg bg-zinc-900 border border-zinc-800 hover:border-zinc-700 cursor-pointer transition-all">
                    <input type="checkbox" name="works_friday" id="e_works_friday" value="1"
                           class="w-4 h-4 rounded border-zinc-700 bg-zinc-950 text-[#F5B81C] focus:ring-0 cursor-pointer">
                    <span class="text-xs font-semibold text-zinc-200">Viernes</span>
                </label>
                <label class="flex items-center gap-2 p-2 rounded-lg bg-zinc-900 border border-zinc-800 hover:border-zinc-700 cursor-pointer transition-all">
                    <input type="checkbox" name="works_saturday" id="e_works_saturday" value="1"
                           class="w-4 h-4 rounded border-zinc-700 bg-zinc-950 text-[#F5B81C] focus:ring-0 cursor-pointer">
                    <span class="text-xs font-semibold text-zinc-200">Sábado</span>
                </label>
                <label class="flex items-center gap-2 p-2 rounded-lg bg-zinc-900 border border-zinc-800 hover:border-zinc-700 cursor-pointer transition-all">
                    <input type="checkbox" name="works_sunday" id="e_works_sunday" value="1"
                           class="w-4 h-4 rounded border-zinc-700 bg-zinc-950 text-[#F5B81C] focus:ring-0 cursor-pointer">
                    <span class="text-xs font-semibold text-zinc-200">Domingo</span>
                </label>
            </div>
        </div>

        <!-- Tarifas de Pago por Noche -->
        <div>
            <label class="block text-xs font-bold text-zinc-300 mb-1.5">Tarifas por Noche (Bs.)</label>
            <div class="grid grid-cols-4 gap-2">
                <div>
                    <label for="e_default_pay" class="block text-[10px] font-mono text-zinc-400 mb-1">Base</label>
                    <input type="number" step="5" name="default_pay" id="e_default_pay" required
                           class="w-full px-2.5 py-2 rounded-xl text-xs font-mono font-bold bg-zinc-900 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] transition-all">
                </div>
                <div>
                    <label for="e_friday_pay" class="block text-[10px] font-mono text-zinc-400 mb-1">Vie</label>
                    <input type="number" step="5" name="friday_pay" id="e_friday_pay"
                           class="w-full px-2.5 py-2 rounded-xl text-xs font-mono font-bold bg-zinc-900 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] transition-all">
                </div>
                <div>
                    <label for="e_saturday_pay" class="block text-[10px] font-mono text-zinc-400 mb-1">Sáb</label>
                    <input type="number" step="5" name="saturday_pay" id="e_saturday_pay"
                           class="w-full px-2.5 py-2 rounded-xl text-xs font-mono font-bold bg-zinc-900 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] transition-all">
                </div>
                <div>
                    <label for="e_sunday_pay" class="block text-[10px] font-mono text-zinc-400 mb-1">Dom</label>
                    <input type="number" step="5" name="sunday_pay" id="e_sunday_pay"
                           class="w-full px-2.5 py-2 rounded-xl text-xs font-mono font-bold bg-zinc-900 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] transition-all">
                </div>
            </div>
        </div>

        <!-- Estado en Cuadrilla -->
        <div>
            <label for="e_is_active" class="block text-xs font-bold text-zinc-300 mb-1.5">
                Estado de Disponibilidad
            </label>
            <select name="is_active" id="e_is_active"
                    class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-zinc-900 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] transition-all cursor-pointer">
                <option value="1">Activo (Habilitado para turnos)</option>
                <option value="0">Inactivo / De Baja</option>
            </select>
        </div>

        <!-- Botones de Acción (Sin márgenes negativos rotos) -->
        <div class="pt-5 border-t border-zinc-800 flex items-center justify-between gap-3 mt-6">
            <button type="button" onclick="confirmDeleteStaff()"
                    class="text-xs text-rose-400 hover:text-rose-300 font-bold hover:underline transition-all cursor-pointer">
                Eliminar Personal
            </button>
            <div class="flex items-center gap-2.5">
                <button type="button" onclick="closeStaffDrawer()" 
                        class="px-4 py-2 rounded-xl border border-zinc-800 text-zinc-400 hover:text-white text-xs font-bold transition-all cursor-pointer">
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-[#F5B81C] hover:bg-[#e5ac18] text-zinc-950 text-xs font-black uppercase tracking-wider transition-all cursor-pointer">
                    Guardar Cambios
                </button>
            </div>
        </div>
    </form>

    <form id="delete-staff-inline-form" method="POST" action="" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>

<script>
    function openCreateStaffDrawer() {
        document.getElementById('staff-drawer-overlay').classList.add('is-open');
        document.getElementById('drawer-create-staff').classList.add('is-open');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            const input = document.getElementById('c_name');
            if (input) input.focus();
        }, 350);
    }

    function openEditStaffDrawer(id, name, role, assignedBar, worksFri, worksSat, worksSun, defPay, friPay, satPay, sunPay, phone, isActive) {
        const baseUrl = '{{ url("/staff") }}';
        document.getElementById('edit-staff-form').action = baseUrl + '/' + id;
        document.getElementById('delete-staff-inline-form').action = baseUrl + '/' + id;

        document.getElementById('edit-staff-title').textContent = name;
        document.getElementById('edit-staff-badge').textContent = 'Ficha de Personal #' + id;
        document.getElementById('e_name').value = name;
        document.getElementById('e_phone').value = phone || '';
        document.getElementById('e_default_pay').value = defPay;
        document.getElementById('e_friday_pay').value = friPay;
        document.getElementById('e_saturday_pay').value = satPay;
        document.getElementById('e_sunday_pay').value = sunPay;
        document.getElementById('e_is_active').value = isActive;
        document.getElementById('e_works_friday').checked = !!worksFri;
        document.getElementById('e_works_saturday').checked = !!worksSat;
        document.getElementById('e_works_sunday').checked = !!worksSun;

        // Set role select
        const cleanRole = String(role || '').replace(/^Staff\s*\/\s*/i, '').replace(/\s*\(S\)/i, '').trim();
        const roleSelect = document.getElementById('e_role');
        if (roleSelect) {
            for (let opt of roleSelect.options) { 
                opt.selected = opt.value.toLowerCase() === cleanRole.toLowerCase() || opt.value.toLowerCase() === String(role).toLowerCase(); 
            }
        }

        // Set area select
        const cleanArea = String(assignedBar || '').replace(/Pista\s*\/\s*Mozos/i, 'Meseros').replace(/Seguridad\s*\/\s*Puerta/i, 'Seguridad').trim();
        const areaSelect = document.getElementById('e_assigned_bar');
        if (areaSelect) {
            for (let opt of areaSelect.options) { 
                opt.selected = opt.value.toLowerCase() === cleanArea.toLowerCase() || opt.value.toLowerCase() === String(assignedBar).toLowerCase(); 
            }
        }

        document.getElementById('staff-drawer-overlay').classList.add('is-open');
        document.getElementById('drawer-edit-staff').classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeStaffDrawer() {
        document.getElementById('staff-drawer-overlay').classList.remove('is-open');
        document.getElementById('drawer-create-staff').classList.remove('is-open');
        document.getElementById('drawer-edit-staff').classList.remove('is-open');
        document.body.style.overflow = '';
    }

    function confirmDeleteStaff() {
        if (confirm('¿Eliminar este trabajador del registro permanentemente? Esta acción no se puede deshacer.')) {
            document.getElementById('delete-staff-inline-form').submit();
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeStaffDrawer();
    });
</script>
@endsection
