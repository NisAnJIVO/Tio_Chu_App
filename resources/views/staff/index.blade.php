@extends('layouts.app')

@section('title', 'Personal y Turnos')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-10">

    <!-- ==========================================
         CABECERA (iOS PURE DARK - SIN RELLENO DE IA)
         ========================================== -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 px-5 py-4 rounded-2xl bg-[#09090b] border border-zinc-800/80 shadow-sm">
        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-[#F5B81C]/10 border border-[#F5B81C]/30 flex items-center justify-center text-[#F5B81C]">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white font-sans">
                Personal y Turnos
            </h1>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('staffPayments.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-950 border border-zinc-800 hover:border-[#F5B81C] text-zinc-300 hover:text-white text-xs sm:text-sm font-bold rounded-xl transition-all shadow-sm active:scale-95">
                <svg class="w-4 h-4 text-[#F5B81C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Planilla de Pagos</span>
            </a>

            <button type="button" onclick="openCreateStaffDrawer()"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-[#F5B81C] hover:bg-[#e5ac18] text-black text-xs sm:text-sm font-black rounded-xl transition-all shadow-md cursor-pointer active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                <span>+ Nuevo Personal</span>
            </button>
        </div>
    </div>

    <!-- Pestañas por Día: iOS Segmented Control (Fuente Sans Oficial - Dimensiones Fijas) -->
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="inline-flex p-1 bg-zinc-950 border border-zinc-800 rounded-2xl gap-1 text-xs sm:text-sm font-sans">
            <a href="{{ route('staff.index', ['day' => 'viernes']) }}" 
               class="px-4 py-2 min-w-[110px] text-center rounded-xl font-bold transition-all {{ $currentDay === 'viernes' ? 'bg-[#F5B81C] text-black shadow-sm font-black' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/60' }}">
                Viernes <span class="ml-1 text-xs {{ $currentDay === 'viernes' ? 'text-black font-black' : 'text-zinc-500' }}">({{ $countViernes }})</span>
            </a>
            <a href="{{ route('staff.index', ['day' => 'sabado']) }}" 
               class="px-4 py-2 min-w-[110px] text-center rounded-xl font-bold transition-all {{ $currentDay === 'sabado' ? 'bg-[#F5B81C] text-black shadow-sm font-black' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/60' }}">
                Sábado <span class="ml-1 text-xs {{ $currentDay === 'sabado' ? 'text-black font-black' : 'text-zinc-500' }}">({{ $countSabado }})</span>
            </a>
            <a href="{{ route('staff.index', ['day' => 'domingo']) }}" 
               class="px-4 py-2 min-w-[110px] text-center rounded-xl font-bold transition-all {{ $currentDay === 'domingo' ? 'bg-[#F5B81C] text-black shadow-sm font-black' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/60' }}">
                Domingo <span class="ml-1 text-xs {{ $currentDay === 'domingo' ? 'text-black font-black' : 'text-zinc-500' }}">({{ $countDomingo }})</span>
            </a>
            <a href="{{ route('staff.index', ['day' => 'todos']) }}" 
               class="px-4 py-2 min-w-[110px] text-center rounded-xl font-bold transition-all {{ $currentDay === 'todos' ? 'bg-[#F5B81C] text-black shadow-sm font-black' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/60' }}">
                Todos <span class="ml-1 text-xs {{ $currentDay === 'todos' ? 'text-black font-black' : 'text-zinc-500' }}">({{ $countTodos }})</span>
            </a>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto">
            <!-- Buscador por Nombre iOS Minimalista -->
            <div class="relative flex-1 sm:w-72">
                <svg class="w-4 h-4 text-zinc-500 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" 
                       id="staff-search-input" 
                       oninput="filterStaffTable(this.value)" 
                       placeholder="Buscar por nombre..." 
                       class="w-full pl-9 pr-8 py-2 rounded-xl text-xs sm:text-sm bg-zinc-950 border border-zinc-800 text-white placeholder-zinc-500 focus:outline-none focus:border-[#F5B81C] transition-all">
                <button type="button" 
                        id="clear-staff-search" 
                        onclick="clearStaffSearch()" 
                        class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-white text-base leading-none cursor-pointer">
                    &times;
                </button>
            </div>

            <div class="text-xs sm:text-sm text-zinc-400 font-sans whitespace-nowrap hidden sm:block">
                <span id="staff-count-display">Mostrando <span class="text-white font-bold">{{ $staffMembers->count() }}</span> integrantes</span>
            </div>
        </div>
    </div>

    <!-- Tabla Minimalista de Personal (Fuente Sans Oficial - Ancho Fijo Bloqueado) -->
    <div class="rounded-2xl border border-zinc-800/80 bg-[#09090b] overflow-hidden shadow-xl font-sans min-h-[540px]">
        <div class="overflow-x-auto">
            <table class="w-full text-xs sm:text-sm text-left table-fixed">
                <colgroup>
                    <col style="width: 5%;">
                    <col style="width: 27%;">
                    <col style="width: 18%;">
                    <col style="width: 17%;">
                    <col style="width: 17%;">
                    <col style="width: 10%;">
                    <col style="width: 6%;">
                </colgroup>
                <thead class="bg-zinc-950 border-b border-zinc-800/80 text-zinc-400 uppercase text-[10px] tracking-wider font-bold">
                    <tr>
                        <th class="px-3 py-3.5 text-center">N°</th>
                        <th class="px-4 py-3.5">Trabajador</th>
                        <th class="px-4 py-3.5">Contacto / Celular</th>
                        <th class="px-4 py-3.5">Cargo / Rol</th>
                        <th class="px-4 py-3.5">Área Asignada</th>
                        <th class="px-4 py-3.5 text-right">Jornal del Turno</th>
                        <th class="px-3 py-3.5 text-right">Acción</th>
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
                            $displayRole = str_ireplace(['Staff / ', 'Staff/', ' (S)', '(S)', 'mozo', 'mozos'], ['', '', '', '', 'MESERO', 'MESEROS'], $member->role);
                            $displayBar = str_ireplace(['Pista / Mozos', 'Pista o mozos', 'Pista', 'Seguridad / Puerta'], ['Meseros', 'Meseros', 'Meseros', 'Seguridad'], $member->assigned_bar ?? 'General');
                        @endphp
                        <tr class="hover:bg-zinc-900/40 transition-colors staff-table-row" data-name="{{ strtolower($member->name) }}" data-role="{{ strtolower($displayRole) }}" data-bar="{{ strtolower($displayBar) }}">
                            <td class="px-3 py-3 text-center text-zinc-500 font-mono font-bold">{{ $index + 1 }}</td>
                            
                            <!-- Nombre + Turnos programados -->
                            <td class="px-4 py-3">
                                <div class="font-bold text-white text-sm sm:text-base tracking-tight uppercase">{{ $member->name }}</div>
                                <div class="flex items-center gap-1.5 mt-1 font-sans">
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $member->works_friday ? 'bg-zinc-800 text-zinc-200 border border-zinc-700/60' : 'text-zinc-600 bg-zinc-950' }}">
                                        Vie
                                    </span>
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $member->works_saturday ? 'bg-zinc-800 text-zinc-200 border border-zinc-700/60' : 'text-zinc-600 bg-zinc-950' }}">
                                        Sáb
                                    </span>
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $member->works_sunday ? 'bg-zinc-800 text-zinc-200 border border-zinc-700/60' : 'text-zinc-600 bg-zinc-950' }}">
                                        Dom
                                    </span>
                                </div>
                            </td>

                            <!-- Celular / Contacto -->
                            <td class="px-4 py-3">
                                @if(!empty($member->phone))
                                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-zinc-950 border border-zinc-800 text-zinc-200 font-sans text-xs">
                                        <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        <span>{{ $member->phone }}</span>
                                    </div>
                                @else
                                    <span class="text-zinc-600 font-sans text-xs">— Sin número —</span>
                                @endif
                            </td>

                            <!-- Rol / Cargo -->
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold bg-zinc-950 border border-zinc-800 text-zinc-200 uppercase">
                                    {{ strtoupper($displayRole) }}
                                </span>
                            </td>

                            <!-- Área Asignada -->
                            <td class="px-4 py-3 text-zinc-300 font-medium">
                                {{ $displayBar }}
                            </td>

                            <!-- Jornal -->
                            <td class="px-4 py-3 text-right">
                                <span class="font-mono font-black text-[#F5B81C] text-sm sm:text-base">
                                    Bs. {{ number_format($pay, 2) }}
                                </span>
                            </td>

                            <!-- Acción Editar -->
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
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-zinc-950 hover:bg-zinc-900 border border-zinc-800 hover:border-[#F5B81C] text-zinc-300 hover:text-white text-xs font-bold transition-all cursor-pointer shadow-sm active:scale-95">
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
                    <tr id="staff-search-empty" class="hidden">
                        <td colspan="7" class="px-4 py-12 text-center text-zinc-500 font-sans">
                            No se encontró ningún integrante que coincida con la búsqueda.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- DRAWER OVERLAY -->
<div class="drawer-overlay" id="staff-drawer-overlay" onclick="closeStaffDrawer()"></div>

<!-- DRAWER: REGISTRAR NUEVO PERSONAL -->
<div class="drawer-panel" id="drawer-create-staff">
    <div class="drawer-header">
        <div>
            <h2 class="text-base font-black text-white tracking-tight">Registrar Personal</h2>
        </div>
        <button type="button" onclick="closeStaffDrawer()" class="w-8 h-8 flex items-center justify-center rounded-xl bg-zinc-900 border border-zinc-800 text-zinc-400 hover:text-white transition-all cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <form method="POST" action="{{ route('staff.store') }}" class="drawer-body space-y-4 font-sans">
        @csrf

        <!-- Nombre -->
        <div>
            <label for="c_name" class="block text-xs font-bold text-zinc-300 mb-1.5">
                Nombre Completo o Apodo <span class="text-[#F5B81C]">*</span>
            </label>
            <input type="text" name="name" id="c_name" required
                   placeholder="Ej. MAURI, ALEX, KELLY, ARIEL"
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
                       class="w-full text-sm font-sans bg-transparent border-0 text-white placeholder-zinc-500 focus:outline-none p-0">
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
                    <label for="c_default_pay" class="block text-[10px] font-sans font-bold text-zinc-400 mb-1">Base</label>
                    <input type="number" step="5" name="default_pay" id="c_default_pay" required value="100"
                           class="w-full px-2.5 py-2 rounded-xl text-xs font-mono font-bold bg-zinc-900 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] transition-all">
                </div>
                <div>
                    <label for="c_friday_pay" class="block text-[10px] font-sans font-bold text-zinc-400 mb-1">Vie</label>
                    <input type="number" step="5" name="friday_pay" id="c_friday_pay" value="100"
                           class="w-full px-2.5 py-2 rounded-xl text-xs font-mono font-bold bg-zinc-900 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] transition-all">
                </div>
                <div>
                    <label for="c_saturday_pay" class="block text-[10px] font-sans font-bold text-zinc-400 mb-1">Sáb</label>
                    <input type="number" step="5" name="saturday_pay" id="c_saturday_pay" value="110"
                           class="w-full px-2.5 py-2 rounded-xl text-xs font-mono font-bold bg-zinc-900 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] transition-all">
                </div>
                <div>
                    <label for="c_sunday_pay" class="block text-[10px] font-sans font-bold text-zinc-400 mb-1">Dom</label>
                    <input type="number" step="5" name="sunday_pay" id="c_sunday_pay" value="110"
                           class="w-full px-2.5 py-2 rounded-xl text-xs font-mono font-bold bg-zinc-900 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] transition-all">
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="pt-5 border-t border-zinc-800 flex items-center justify-end gap-2.5 mt-6">
            <button type="button" onclick="closeStaffDrawer()" 
                    class="px-4 py-2 rounded-xl border border-zinc-800 text-zinc-400 hover:text-white text-xs sm:text-sm font-bold transition-all cursor-pointer">
                Cancelar
            </button>
            <button type="submit" 
                    class="px-5 py-2 rounded-xl bg-[#F5B81C] hover:bg-[#e5ac18] text-black text-xs sm:text-sm font-black uppercase tracking-wider transition-all cursor-pointer shadow-md">
                Guardar Personal
            </button>
        </div>
    </form>
</div>

<!-- DRAWER: EDITAR PERSONAL -->
<div class="drawer-panel" id="drawer-edit-staff">
    <div class="drawer-header">
        <div>
            <h2 class="text-base font-black text-white tracking-tight" id="edit-staff-title">Editar Personal</h2>
        </div>
        <button type="button" onclick="closeStaffDrawer()" class="w-8 h-8 flex items-center justify-center rounded-xl bg-zinc-900 border border-zinc-800 text-zinc-400 hover:text-white transition-all cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <form method="POST" id="edit-staff-form" action="" class="drawer-body space-y-4 font-sans">
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
                       class="w-full text-sm font-sans bg-transparent border-0 text-white placeholder-zinc-500 focus:outline-none p-0">
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
                    <label for="e_default_pay" class="block text-[10px] font-sans font-bold text-zinc-400 mb-1">Base</label>
                    <input type="number" step="5" name="default_pay" id="e_default_pay" required
                           class="w-full px-2.5 py-2 rounded-xl text-xs font-mono font-bold bg-zinc-900 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] transition-all">
                </div>
                <div>
                    <label for="e_friday_pay" class="block text-[10px] font-sans font-bold text-zinc-400 mb-1">Vie</label>
                    <input type="number" step="5" name="friday_pay" id="e_friday_pay"
                           class="w-full px-2.5 py-2 rounded-xl text-xs font-mono font-bold bg-zinc-900 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] transition-all">
                </div>
                <div>
                    <label for="e_saturday_pay" class="block text-[10px] font-sans font-bold text-zinc-400 mb-1">Sáb</label>
                    <input type="number" step="5" name="saturday_pay" id="e_saturday_pay"
                           class="w-full px-2.5 py-2 rounded-xl text-xs font-mono font-bold bg-zinc-900 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] transition-all">
                </div>
                <div>
                    <label for="e_sunday_pay" class="block text-[10px] font-sans font-bold text-zinc-400 mb-1">Dom</label>
                    <input type="number" step="5" name="sunday_pay" id="e_sunday_pay"
                           class="w-full px-2.5 py-2 rounded-xl text-xs font-mono font-bold bg-zinc-900 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] transition-all">
                </div>
            </div>
        </div>

        <!-- Estado del Personal -->
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

        <!-- Botones de Acción -->
        <div class="pt-5 border-t border-zinc-800 flex items-center justify-between gap-3 mt-6">
            <button type="button" onclick="confirmDeleteStaff()"
                    class="text-xs text-rose-400 hover:text-rose-300 font-bold hover:underline transition-all cursor-pointer">
                Eliminar Personal
            </button>
            <div class="flex items-center gap-2.5">
                <button type="button" onclick="closeStaffDrawer()" 
                        class="px-4 py-2 rounded-xl border border-zinc-800 text-zinc-400 hover:text-white text-xs sm:text-sm font-bold transition-all cursor-pointer">
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-[#F5B81C] hover:bg-[#e5ac18] text-black text-xs sm:text-sm font-black uppercase tracking-wider transition-all cursor-pointer shadow-md">
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
        const cleanRole = String(role || '').replace(/^Staff\s*\/\s*/i, '').replace(/\s*\(S\)/i, '').replace(/mozos?/i, 'Mesero').trim();
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

    function filterStaffTable(query) {
        const q = (query || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.staff-table-row');
        const clearBtn = document.getElementById('clear-staff-search');
        let visibleCount = 0;

        if (clearBtn) {
            clearBtn.classList.toggle('hidden', q.length === 0);
        }

        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const role = row.getAttribute('data-role') || '';
            const bar = row.getAttribute('data-bar') || '';
            const match = !q || name.includes(q) || role.includes(q) || bar.includes(q);
            row.style.display = match ? '' : 'none';
            if (match) visibleCount++;
        });

        const emptyRow = document.getElementById('staff-search-empty');
        if (emptyRow) {
            emptyRow.classList.toggle('hidden', visibleCount > 0 || rows.length === 0);
        }

        const countDisplay = document.getElementById('staff-count-display');
        if (countDisplay) {
            countDisplay.innerHTML = `Mostrando <span class="text-white font-bold">${visibleCount}</span> integrantes`;
        }
    }

    function clearStaffSearch() {
        const input = document.getElementById('staff-search-input');
        if (input) {
            input.value = '';
            filterStaffTable('');
            input.focus();
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeStaffDrawer();
            clearStaffSearch();
        }
    });
</script>
@endsection
