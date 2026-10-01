@extends('layouts.app')

@section('title', 'Personal y Turnos')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">

    <!-- ==========================================
         CABECERA DE LA VISTA (LIQUID GLASS)
         ========================================== -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-white/10">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider bg-amber-400/10 text-amber-300 border border-amber-400/30">
                    Módulo 3 — Personal & Planilla
                </span>
                <span class="text-zinc-600 text-xs">•</span>
                <span class="text-xs text-zinc-400 font-mono">Planilla Nocturna por Turno</span>
            </div>
            <h2 class="text-2xl font-black tracking-tight text-white">
                Personal y Turnos de Atención
            </h2>
            <p class="text-xs text-zinc-400 mt-1">
                Catálogo de trabajadores activos, roles (Bartender, Seguridad, Limpieza) y asignación de jornales nocturnos.
            </p>
        </div>

        <button type="button" onclick="openCreateStaffDrawer()"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-amber-400 to-amber-500 text-zinc-950 text-xs font-bold rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-amber-500/20 cursor-pointer">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>+ Nuevo Trabajador</span>
        </button>
    </div>

    <!-- Pestañas por Día con Estilo Glass Pills -->
    <div class="flex items-center gap-2 glass-panel p-2 rounded-2xl border border-white/10 overflow-x-auto text-xs font-mono">
        <a href="{{ route('staff.index', ['day' => 'viernes']) }}" 
           class="px-4 py-2 rounded-xl font-bold transition-all {{ $currentDay === 'viernes' ? 'bg-amber-400 text-zinc-950 shadow-md shadow-amber-400/20' : 'text-zinc-300 hover:text-white hover:bg-white/5' }}">
            Viernes ({{ $countViernes }})
        </a>
        <a href="{{ route('staff.index', ['day' => 'sabado']) }}" 
           class="px-4 py-2 rounded-xl font-bold transition-all {{ $currentDay === 'sabado' ? 'bg-amber-400 text-zinc-950 shadow-md shadow-amber-400/20' : 'text-zinc-300 hover:text-white hover:bg-white/5' }}">
            Sábado ({{ $countSabado }})
        </a>
        <a href="{{ route('staff.index', ['day' => 'domingo']) }}" 
           class="px-4 py-2 rounded-xl font-bold transition-all {{ $currentDay === 'domingo' ? 'bg-amber-400 text-zinc-950 shadow-md shadow-amber-400/20' : 'text-zinc-300 hover:text-white hover:bg-white/5' }}">
            Domingo ({{ $countDomingo }})
        </a>
        <a href="{{ route('staff.index', ['day' => 'todos']) }}" 
           class="px-4 py-2 rounded-xl font-bold transition-all {{ $currentDay === 'todos' ? 'bg-amber-400 text-zinc-950 shadow-md shadow-amber-400/20' : 'text-zinc-300 hover:text-white hover:bg-white/5' }}">
            Todos ({{ $countTodos }})
        </a>
    </div>

    <!-- Tabla Liquid Glass de Personal -->
    <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl border border-white/10">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-white/[0.04] border-b border-white/10 text-zinc-400 font-mono uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3 w-12 text-center">N°</th>
                        <th class="px-4 py-3">Nombre Completo</th>
                        <th class="px-4 py-3">Rol / Cargo</th>
                        <th class="px-4 py-3">Área Asignada</th>
                        <th class="px-4 py-3 text-right">Jornal del Turno</th>
                        <th class="px-4 py-3 text-right w-28">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 font-mono">
                    @forelse($staffMembers as $index => $member)
                        @php
                            $pay = match($currentDay) {
                                'viernes' => $member->getPayForDay('Viernes'),
                                'sabado' => $member->getPayForDay('Sábado'),
                                'domingo' => $member->getPayForDay('Domingo'),
                                default => $member->friday_pay ?? $member->saturday_pay ?? $member->sunday_pay ?? 0,
                            };
                        @endphp
                        <tr class="hover:bg-white/[0.02]">
                            <td class="px-4 py-3 text-center text-zinc-500 font-bold">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-bold text-white font-sans text-sm">{{ $member->name }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-400/10 text-amber-300 border border-amber-400/30">
                                    {{ $member->role }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-zinc-300 font-sans">{{ $member->assigned_bar }}</td>
                            <td class="px-4 py-3 text-right font-black text-amber-400 text-sm">
                                Bs. {{ number_format($pay, 2) }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button type="button"
                                        onclick="openEditStaffDrawer(
                                            {{ $member->id }},
                                            {{ json_encode($member->name) }},
                                            {{ json_encode($member->role) }},
                                            {{ json_encode($member->assigned_bar ?? 'General') }},
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
                                        class="text-xs font-bold text-zinc-300 hover:text-amber-400 transition-colors cursor-pointer">
                                    Editar &rarr;
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-zinc-500 font-sans">
                                No hay personal registrado para este día.
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
     DRAWER: CREAR PERSONAL
     ========================================== -->
<div class="drawer-panel" id="drawer-create-staff">
    <div class="drawer-header">
        <div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-1.5">
                Planilla de Personal
            </span>
            <h2 class="text-lg font-black text-white tracking-tight">Registrar Personal</h2>
            <p class="text-xs text-zinc-400 mt-0.5">Nuevo integrante del equipo de trabajo de Tío Chu.</p>
        </div>
        <button onclick="closeStaffDrawer()" class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 text-zinc-400 hover:text-white transition-all flex-shrink-0 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <form method="POST" action="{{ route('staff.store') }}" class="drawer-body space-y-5">
        @csrf

        <div>
            <label for="c_name" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">
                Nombre Completo / Apodo de Turno <span class="text-amber-400">*</span>
            </label>
            <input type="text" name="name" id="c_name" required
                   placeholder="Ej. MAURI, ALEX(S), KELLY, ARIEL"
                   class="glass-input w-full px-4 py-3 rounded-xl text-sm uppercase text-white placeholder-zinc-500 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="c_role" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">
                    Rol / Cargo <span class="text-amber-400">*</span>
                </label>
                <select name="role" id="c_role" required
                        class="glass-input w-full px-4 py-3 rounded-xl text-sm text-white bg-zinc-900/90 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                    <option value="Staff / Mesero" class="bg-zinc-900">Staff / Mesero</option>
                    <option value="Staff / Limpieza" class="bg-zinc-900">Staff / Limpieza</option>
                    <option value="Seguridad" class="bg-zinc-900">Seguridad (S)</option>
                    <option value="Bartender Principal" class="bg-zinc-900">Bartender Principal</option>
                    <option value="Bartender Subterráneo" class="bg-zinc-900">Bartender Subterráneo</option>
                    <option value="DJ / Sonido" class="bg-zinc-900">DJ / Sonido</option>
                    <option value="Administrador" class="bg-zinc-900">Administrador</option>
                </select>
            </div>
            <div>
                <label for="c_assigned_bar" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">
                    Área Asignada
                </label>
                <select name="assigned_bar" id="c_assigned_bar"
                        class="glass-input w-full px-4 py-3 rounded-xl text-sm text-white bg-zinc-900/90 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                    <option value="Pista / Mozos" class="bg-zinc-900">Pista / Mozos</option>
                    <option value="Barra Principal" class="bg-zinc-900">Barra Principal</option>
                    <option value="Barra Subterráneo" class="bg-zinc-900">Barra Subterráneo</option>
                    <option value="Seguridad" class="bg-zinc-900">Seguridad / Puerta</option>
                    <option value="Tienda" class="bg-zinc-900">Tienda</option>
                    <option value="General" class="bg-zinc-900">General</option>
                </select>
            </div>
        </div>

        <!-- Días de Turno -->
        <div class="glass-card rounded-xl p-4 border border-white/10 space-y-2">
            <span class="block text-[10px] font-bold text-zinc-300 uppercase tracking-wider">Días de Turno Programados</span>
            <div class="grid grid-cols-3 gap-3 pt-2">
                <label class="flex items-center gap-2 text-xs font-medium text-zinc-200 cursor-pointer glass-card hover:bg-white/10 p-2.5 rounded-lg border border-white/5 transition-all">
                    <input type="checkbox" name="works_friday" value="1" checked
                           class="w-4 h-4 rounded border-white/20 bg-zinc-900 text-amber-500 focus:ring-amber-500/50">
                    <span>Viernes</span>
                </label>
                <label class="flex items-center gap-2 text-xs font-medium text-zinc-200 cursor-pointer glass-card hover:bg-white/10 p-2.5 rounded-lg border border-white/5 transition-all">
                    <input type="checkbox" name="works_saturday" value="1" checked
                           class="w-4 h-4 rounded border-white/20 bg-zinc-900 text-amber-500 focus:ring-amber-500/50">
                    <span>Sábado</span>
                </label>
                <label class="flex items-center gap-2 text-xs font-medium text-zinc-200 cursor-pointer glass-card hover:bg-white/10 p-2.5 rounded-lg border border-white/5 transition-all">
                    <input type="checkbox" name="works_sunday" value="1" checked
                           class="w-4 h-4 rounded border-white/20 bg-zinc-900 text-amber-500 focus:ring-amber-500/50">
                    <span>Domingo</span>
                </label>
            </div>
        </div>

        <!-- Tarifas -->
        <div>
            <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2">Tarifas por Noche (Bs.)</label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div>
                    <label for="c_default_pay" class="block text-[11px] font-semibold text-zinc-500 mb-1">Base</label>
                    <input type="number" step="5" name="default_pay" id="c_default_pay" required value="100"
                           class="glass-input w-full px-3 py-2.5 rounded-xl text-sm text-white font-mono font-bold focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                </div>
                <div>
                    <label for="c_friday_pay" class="block text-[11px] font-semibold text-zinc-500 mb-1">Viernes</label>
                    <input type="number" step="5" name="friday_pay" id="c_friday_pay" value="100"
                           class="glass-input w-full px-3 py-2.5 rounded-xl text-sm text-white font-mono font-bold focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                </div>
                <div>
                    <label for="c_saturday_pay" class="block text-[11px] font-semibold text-zinc-500 mb-1">Sábado</label>
                    <input type="number" step="5" name="saturday_pay" id="c_saturday_pay" value="110"
                           class="glass-input w-full px-3 py-2.5 rounded-xl text-sm text-white font-mono font-bold focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                </div>
                <div>
                    <label for="c_sunday_pay" class="block text-[11px] font-semibold text-zinc-500 mb-1">Domingo</label>
                    <input type="number" step="5" name="sunday_pay" id="c_sunday_pay" value="110"
                           class="glass-input w-full px-3 py-2.5 rounded-xl text-sm text-white font-mono font-bold focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                </div>
            </div>
        </div>

        <div>
            <label for="c_phone" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">
                Teléfono / Celular (Opcional)
            </label>
            <input type="text" name="phone" id="c_phone" placeholder="Ej. 70012345"
                   class="glass-input w-full px-4 py-3 rounded-xl text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
        </div>

        <div class="drawer-footer" style="margin: 0 -1.75rem -1.75rem; padding: 1.25rem 1.75rem;">
            <button type="button" onclick="closeStaffDrawer()" class="px-5 py-2.5 glass-card hover:bg-white/10 text-zinc-400 hover:text-white text-xs font-semibold rounded-xl transition-all cursor-pointer">
                Cancelar
            </button>
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-extrabold tracking-wider uppercase rounded-xl shadow-lg shadow-amber-500/20 active:scale-95 transition-all cursor-pointer">
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
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-1.5" id="edit-staff-badge">
                Ficha de Personal
            </span>
            <h2 class="text-lg font-black text-white tracking-tight" id="edit-staff-title">Editar Personal</h2>
            <p class="text-xs text-zinc-400 mt-0.5">Actualizar datos de cargo, días de turno o tarifas por noche.</p>
        </div>
        <button onclick="closeStaffDrawer()" class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 text-zinc-400 hover:text-white transition-all flex-shrink-0 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <form method="POST" id="edit-staff-form" action="" class="drawer-body space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="e_name" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">
                Nombre Completo / Apodo <span class="text-amber-400">*</span>
            </label>
            <input type="text" name="name" id="e_name" required
                   class="glass-input w-full px-4 py-3 rounded-xl text-sm uppercase text-white placeholder-zinc-500 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="e_role" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">
                    Rol / Cargo <span class="text-amber-400">*</span>
                </label>
                <select name="role" id="e_role" required
                        class="glass-input w-full px-4 py-3 rounded-xl text-sm text-white bg-zinc-900/90 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                    <option value="Staff / Mesero" class="bg-zinc-900">Staff / Mesero</option>
                    <option value="Staff / Limpieza" class="bg-zinc-900">Staff / Limpieza</option>
                    <option value="Seguridad" class="bg-zinc-900">Seguridad (S)</option>
                    <option value="Bartender Principal" class="bg-zinc-900">Bartender Principal</option>
                    <option value="Bartender Subterráneo" class="bg-zinc-900">Bartender Subterráneo</option>
                    <option value="DJ / Sonido" class="bg-zinc-900">DJ / Sonido</option>
                    <option value="Administrador" class="bg-zinc-900">Administrador</option>
                </select>
            </div>
            <div>
                <label for="e_assigned_bar" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">
                    Área Asignada
                </label>
                <select name="assigned_bar" id="e_assigned_bar"
                        class="glass-input w-full px-4 py-3 rounded-xl text-sm text-white bg-zinc-900/90 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                    <option value="Pista / Mozos" class="bg-zinc-900">Pista / Mozos</option>
                    <option value="Barra Principal" class="bg-zinc-900">Barra Principal</option>
                    <option value="Barra Subterráneo" class="bg-zinc-900">Barra Subterráneo</option>
                    <option value="Seguridad" class="bg-zinc-900">Seguridad / Puerta</option>
                    <option value="Tienda" class="bg-zinc-900">Tienda</option>
                    <option value="General" class="bg-zinc-900">General</option>
                </select>
            </div>
        </div>

        <!-- Días de Turno -->
        <div class="glass-card rounded-xl p-4 border border-white/10 space-y-2">
            <span class="block text-[10px] font-bold text-zinc-300 uppercase tracking-wider">Días de Turno Programados</span>
            <div class="grid grid-cols-3 gap-3 pt-2">
                <label class="flex items-center gap-2 text-xs font-medium text-zinc-200 cursor-pointer glass-card hover:bg-white/10 p-2.5 rounded-lg border border-white/5 transition-all">
                    <input type="checkbox" name="works_friday" id="e_works_friday" value="1"
                           class="w-4 h-4 rounded border-white/20 bg-zinc-900 text-amber-500 focus:ring-amber-500/50">
                    <span>Viernes</span>
                </label>
                <label class="flex items-center gap-2 text-xs font-medium text-zinc-200 cursor-pointer glass-card hover:bg-white/10 p-2.5 rounded-lg border border-white/5 transition-all">
                    <input type="checkbox" name="works_saturday" id="e_works_saturday" value="1"
                           class="w-4 h-4 rounded border-white/20 bg-zinc-900 text-amber-500 focus:ring-amber-500/50">
                    <span>Sábado</span>
                </label>
                <label class="flex items-center gap-2 text-xs font-medium text-zinc-200 cursor-pointer glass-card hover:bg-white/10 p-2.5 rounded-lg border border-white/5 transition-all">
                    <input type="checkbox" name="works_sunday" id="e_works_sunday" value="1"
                           class="w-4 h-4 rounded border-white/20 bg-zinc-900 text-amber-500 focus:ring-amber-500/50">
                    <span>Domingo</span>
                </label>
            </div>
        </div>

        <!-- Tarifas -->
        <div>
            <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2">Tarifas por Noche (Bs.)</label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div>
                    <label for="e_default_pay" class="block text-[11px] font-semibold text-zinc-500 mb-1">Base</label>
                    <input type="number" step="5" name="default_pay" id="e_default_pay" required
                           class="glass-input w-full px-3 py-2.5 rounded-xl text-sm text-white font-mono font-bold focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                </div>
                <div>
                    <label for="e_friday_pay" class="block text-[11px] font-semibold text-zinc-500 mb-1">Viernes</label>
                    <input type="number" step="5" name="friday_pay" id="e_friday_pay"
                           class="glass-input w-full px-3 py-2.5 rounded-xl text-sm text-white font-mono font-bold focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                </div>
                <div>
                    <label for="e_saturday_pay" class="block text-[11px] font-semibold text-zinc-500 mb-1">Sábado</label>
                    <input type="number" step="5" name="saturday_pay" id="e_saturday_pay"
                           class="glass-input w-full px-3 py-2.5 rounded-xl text-sm text-white font-mono font-bold focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                </div>
                <div>
                    <label for="e_sunday_pay" class="block text-[11px] font-semibold text-zinc-500 mb-1">Domingo</label>
                    <input type="number" step="5" name="sunday_pay" id="e_sunday_pay"
                           class="glass-input w-full px-3 py-2.5 rounded-xl text-sm text-white font-mono font-bold focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="e_phone" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">
                    Teléfono (Opcional)
                </label>
                <input type="text" name="phone" id="e_phone"
                       class="glass-input w-full px-4 py-3 rounded-xl text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
            </div>
            <div>
                <label for="e_is_active" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">
                    Estado
                </label>
                <select name="is_active" id="e_is_active"
                        class="glass-input w-full px-4 py-3 rounded-xl text-sm text-white bg-zinc-900/90 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                    <option value="1" class="bg-zinc-900">Activo (En turnos)</option>
                    <option value="0" class="bg-zinc-900">Inactivo / De Baja</option>
                </select>
            </div>
        </div>

        <div class="drawer-footer" style="margin: 0 -1.75rem -1.75rem; padding: 1.25rem 1.75rem;">
            <button type="button" onclick="confirmDeleteStaff()"
                    class="text-xs text-rose-400 hover:text-rose-300 font-bold uppercase tracking-wider hover:underline transition-all cursor-pointer">
                Eliminar Personal
            </button>
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeStaffDrawer()" class="px-5 py-2.5 glass-card hover:bg-white/10 text-zinc-400 hover:text-white text-xs font-semibold rounded-xl transition-all cursor-pointer">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-extrabold tracking-wider uppercase rounded-xl shadow-lg shadow-amber-500/20 active:scale-95 transition-all cursor-pointer">
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
        setTimeout(() => document.getElementById('c_name').focus(), 350);
    }

    function openEditStaffDrawer(id, name, role, assignedBar, worksFri, worksSat, worksSun, defPay, friPay, satPay, sunPay, phone, isActive) {
        const baseUrl = '{{ url("/staff") }}';
        document.getElementById('edit-staff-form').action = baseUrl + '/' + id;
        document.getElementById('delete-staff-inline-form').action = baseUrl + '/' + id;

        document.getElementById('edit-staff-title').textContent = name;
        document.getElementById('edit-staff-badge').textContent = 'Ficha de Personal #' + id;
        document.getElementById('e_name').value = name;
        document.getElementById('e_phone').value = phone;
        document.getElementById('e_default_pay').value = defPay;
        document.getElementById('e_friday_pay').value = friPay;
        document.getElementById('e_saturday_pay').value = satPay;
        document.getElementById('e_sunday_pay').value = sunPay;
        document.getElementById('e_is_active').value = isActive;
        document.getElementById('e_works_friday').checked = worksFri;
        document.getElementById('e_works_saturday').checked = worksSat;
        document.getElementById('e_works_sunday').checked = worksSun;

        // Set role select
        const roleSelect = document.getElementById('e_role');
        for (let opt of roleSelect.options) { opt.selected = opt.value === role; }

        // Set area select
        const areaSelect = document.getElementById('e_assigned_bar');
        for (let opt of areaSelect.options) { opt.selected = opt.value === assignedBar; }

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
