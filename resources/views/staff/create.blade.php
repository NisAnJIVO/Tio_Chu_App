@extends('layouts.app')

@section('title', 'Nuevo Personal')

@section('content')
<div class="max-w-2xl mx-auto space-y-5 pb-16">
    <!-- Header -->
    <div class="notoria-card px-6 py-4 flex items-center justify-between shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-0.5">
                <span class="text-[10px] font-mono font-bold text-[#F5B81C] uppercase tracking-wider">
                    Gestión Operativa
                </span>
                <span class="theme-text-muted text-xs">•</span>
                <span class="text-[10px] theme-text-secondary font-mono">Equipo Tío Chu</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight theme-text-primary font-sans">
                Registrar Personal
            </h1>
            <p class="text-xs theme-text-secondary mt-0.5">
                Alta de trabajador, asignación de turnos y tarifa de pago por noche.
            </p>
        </div>
        <a href="{{ route('staff.index') }}" 
           class="notoria-subtile hover:border-amber-500 theme-text-primary px-3.5 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    <!-- Form Panel -->
    <form method="POST" action="{{ route('staff.store') }}" class="notoria-card p-6 sm:p-7 shadow-lg space-y-5">
        @csrf

        <div>
            <label for="name" class="block text-xs font-bold theme-text-secondary uppercase tracking-wider mb-2">
                Nombre Completo o Apodo <span class="text-amber-500">*</span>
            </label>
            <input type="text" name="name" id="name" required value="{{ old('name') }}"
                   placeholder="Ej. MAURI, ALEX, KELLY, ARIEL"
                   class="notoria-input w-full px-4 py-2.5 text-sm theme-text-primary uppercase placeholder-zinc-400 focus:outline-none">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="role" class="block text-xs font-bold theme-text-secondary uppercase tracking-wider mb-2">
                    Cargo / Rol <span class="text-amber-500">*</span>
                </label>
                <select name="role" id="role" required 
                        class="notoria-input w-full px-4 py-2.5 text-sm theme-text-primary focus:outline-none cursor-pointer">
                    <option value="Mesero" class="bg-slate-900 text-white">Mesero</option>
                    <option value="Limpieza" class="bg-slate-900 text-white">Limpieza</option>
                    <option value="Seguridad" class="bg-slate-900 text-white">Seguridad</option>
                    <option value="Bartender Principal" class="bg-slate-900 text-white">Bartender Principal</option>
                    <option value="Bartender Subterráneo" class="bg-slate-900 text-white">Bartender Subterráneo</option>
                    <option value="DJ / Sonido" class="bg-slate-900 text-white">DJ / Sonido</option>
                    <option value="Administrador" class="bg-slate-900 text-white">Administrador</option>
                </select>
            </div>

            <div>
                <label for="assigned_bar" class="block text-xs font-bold theme-text-secondary uppercase tracking-wider mb-2">
                    Barra / Área Asignada
                </label>
                <select name="assigned_bar" id="assigned_bar" 
                        class="notoria-input w-full px-4 py-2.5 text-sm theme-text-primary focus:outline-none cursor-pointer">
                    <option value="Meseros" class="bg-slate-900 text-white">Meseros</option>
                    <option value="Barra Principal" class="bg-slate-900 text-white">Barra Principal</option>
                    <option value="Barra Subterráneo" class="bg-slate-900 text-white">Barra Subterráneo</option>
                    <option value="Seguridad" class="bg-slate-900 text-white">Seguridad</option>
                    <option value="Tienda" class="bg-slate-900 text-white">Tienda</option>
                    <option value="General" class="bg-slate-900 text-white">General</option>
                </select>
            </div>
        </div>

        <!-- Días que Asiste de Turno -->
        <div class="p-4 rounded-xl notoria-subtile space-y-2">
            <span class="block text-[11px] font-black uppercase tracking-wider theme-text-secondary">Días de Turno Programados</span>
            <div class="grid grid-cols-3 gap-2.5 pt-1">
                <label class="flex items-center gap-2 p-2.5 rounded-lg notoria-card hover:border-amber-500 cursor-pointer transition-all">
                    <input type="checkbox" name="works_friday" value="1" {{ old('works_friday', true) ? 'checked' : '' }} 
                           class="w-4 h-4 rounded text-[#F5B81C] cursor-pointer" style="accent-color: #F5B81C !important;">
                    <span class="text-xs font-bold theme-text-primary">Viernes</span>
                </label>
                <label class="flex items-center gap-2 p-2.5 rounded-lg notoria-card hover:border-amber-500 cursor-pointer transition-all">
                    <input type="checkbox" name="works_saturday" value="1" {{ old('works_saturday', true) ? 'checked' : '' }} 
                           class="w-4 h-4 rounded text-[#F5B81C] cursor-pointer" style="accent-color: #F5B81C !important;">
                    <span class="text-xs font-bold theme-text-primary">Sábado</span>
                </label>
                <label class="flex items-center gap-2 p-2.5 rounded-lg notoria-card hover:border-amber-500 cursor-pointer transition-all">
                    <input type="checkbox" name="works_sunday" value="1" {{ old('works_sunday', true) ? 'checked' : '' }} 
                           class="w-4 h-4 rounded text-[#F5B81C] cursor-pointer" style="accent-color: #F5B81C !important;">
                    <span class="text-xs font-bold theme-text-primary">Domingo</span>
                </label>
            </div>
        </div>

        <!-- Tarifas de Pago por Noche -->
        <div>
            <label class="block text-xs font-bold theme-text-secondary uppercase tracking-wider mb-2">
                Tarifas de Pago por Noche (Bs.)
            </label>
            <div class="grid grid-cols-4 gap-2.5">
                <div>
                    <label for="default_pay" class="block text-[10px] font-mono theme-text-muted mb-1 uppercase font-bold">Base</label>
                    <input type="number" step="5" name="default_pay" id="default_pay" required value="{{ old('default_pay', 100) }}"
                           class="w-full px-2.5 py-2 rounded-xl text-xs font-mono font-bold notoria-input theme-text-primary focus:outline-none">
                </div>
                <div>
                    <label for="friday_pay" class="block text-[10px] font-mono theme-text-muted mb-1 uppercase font-bold">Vie</label>
                    <input type="number" step="5" name="friday_pay" id="friday_pay" value="{{ old('friday_pay', 100) }}"
                           class="w-full px-2.5 py-2 rounded-xl text-xs font-mono font-bold notoria-input theme-text-primary focus:outline-none">
                </div>
                <div>
                    <label for="saturday_pay" class="block text-[10px] font-mono theme-text-muted mb-1 uppercase font-bold">Sáb</label>
                    <input type="number" step="5" name="saturday_pay" id="saturday_pay" value="{{ old('saturday_pay', 110) }}"
                           class="w-full px-2.5 py-2 rounded-xl text-xs font-mono font-bold notoria-input theme-text-primary focus:outline-none">
                </div>
                <div>
                    <label for="sunday_pay" class="block text-[10px] font-mono theme-text-muted mb-1 uppercase font-bold">Dom</label>
                    <input type="number" step="5" name="sunday_pay" id="sunday_pay" value="{{ old('sunday_pay', 110) }}"
                           class="w-full px-2.5 py-2 rounded-xl text-xs font-mono font-bold notoria-input theme-text-primary focus:outline-none">
                </div>
            </div>
        </div>

        <div>
            <label for="phone" class="block text-xs font-bold theme-text-secondary uppercase tracking-wider mb-2">
                Teléfono / Celular (Opcional)
            </label>
            <div class="flex items-center rounded-xl notoria-input px-3.5 py-2 focus-within:border-[#F5B81C] transition-all">
                <svg class="w-4 h-4 theme-text-muted mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="Ej. 70012345"
                       class="w-full text-sm font-mono bg-transparent border-0 theme-text-primary placeholder-zinc-400 focus:outline-none p-0">
            </div>
        </div>

        <div class="pt-5 border-t notoria-border flex items-center justify-end gap-3">
            <a href="{{ route('staff.index') }}" 
               class="px-5 py-2.5 rounded-xl notoria-subtile hover:border-zinc-400 theme-text-secondary text-xs font-bold transition-all">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-6 py-2.5 rounded-xl bg-[#F5B81C] hover:bg-[#e5ac18] text-black text-xs font-black uppercase tracking-wider transition-all cursor-pointer shadow-md active:scale-95">
                Guardar Personal
            </button>
        </div>
    </form>
</div>
@endsection