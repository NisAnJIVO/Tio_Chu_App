@extends('layouts.app')

@section('title', 'Nuevo Personal')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between border-b border-white/10 pb-4">
        <div>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-1">
                Planilla de Personal
            </span>
            <h2 class="text-xl font-black text-white tracking-tight">Registrar Personal</h2>
            <p class="text-xs text-zinc-400">Nuevo integrante del equipo de trabajo y cuadrilla de Don Ludo.</p>
        </div>
        <a href="{{ route('staff.index') }}" 
           class="glass-card hover:bg-white/10 text-zinc-300 hover:text-white px-3.5 py-2 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-all">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a la Lista
        </a>
    </div>

    <!-- Form Panel -->
    <form method="POST" action="{{ route('staff.store') }}" class="glass-panel p-6 sm:p-8 rounded-2xl border border-white/10 shadow-2xl space-y-6">
        @csrf

        <div>
            <label for="name" class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-2">
                Nombre Completo / Apodo de Turno <span class="text-amber-400">*</span>
            </label>
            <input type="text" name="name" id="name" required value="{{ old('name') }}"
                   placeholder="Ej. MAURI, ALEX(S), KELLY, ARIEL"
                   class="glass-input w-full px-4 py-3 rounded-xl text-sm uppercase text-white placeholder-zinc-500 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label for="role" class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-2">
                    Rol / Cargo <span class="text-amber-400">*</span>
                </label>
                <select name="role" id="role" required 
                        class="glass-input w-full px-4 py-3 rounded-xl text-sm text-white bg-zinc-900/90 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                    <option value="Staff / Mozo" class="bg-zinc-900 text-white">Staff / Mozo</option>
                    <option value="Staff / Mesera" class="bg-zinc-900 text-white">Staff / Mesera</option>
                    <option value="Seguridad" class="bg-zinc-900 text-white">Seguridad (S)</option>
                    <option value="Bartender Principal" class="bg-zinc-900 text-white">Bartender Principal</option>
                    <option value="Bartender Subterráneo" class="bg-zinc-900 text-white">Bartender Subterráneo</option>
                    <option value="DJ / Sonido" class="bg-zinc-900 text-white">DJ / Sonido</option>
                    <option value="Administrador" class="bg-zinc-900 text-white">Administrador</option>
                </select>
            </div>

            <div>
                <label for="assigned_bar" class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-2">
                    Barra / Área Asignada
                </label>
                <select name="assigned_bar" id="assigned_bar" 
                        class="glass-input w-full px-4 py-3 rounded-xl text-sm text-white bg-zinc-900/90 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                    <option value="Pista / Mozos" class="bg-zinc-900 text-white">Pista / Mozos</option>
                    <option value="Barra Principal" class="bg-zinc-900 text-white">Barra Principal</option>
                    <option value="Barra Subterráneo" class="bg-zinc-900 text-white">Barra Subterráneo</option>
                    <option value="Seguridad" class="bg-zinc-900 text-white">Seguridad / Puerta</option>
                    <option value="Tienda" class="bg-zinc-900 text-white">Tienda</option>
                    <option value="General" class="bg-zinc-900 text-white">General</option>
                </select>
            </div>
        </div>

        <!-- Días que Asiste de Turno -->
        <div class="glass-card rounded-xl p-4 border border-white/10 space-y-2">
            <span class="block text-xs font-bold text-zinc-200 uppercase tracking-wider">Días de Turno Programados</span>
            <p class="text-[11px] text-zinc-400">Marca los días de la semana en los que este integrante asiste por defecto:</p>
            <div class="grid grid-cols-3 gap-3 pt-2">
                <label class="flex items-center gap-2 text-xs font-medium text-zinc-200 cursor-pointer glass-card hover:bg-white/10 p-2.5 rounded-lg border border-white/5 transition-all">
                    <input type="checkbox" name="works_friday" value="1" {{ old('works_friday', true) ? 'checked' : '' }} 
                           class="w-4 h-4 rounded border-white/20 bg-zinc-900 text-amber-500 focus:ring-amber-500/50 focus:ring-offset-0">
                    <span>Viernes</span>
                </label>
                <label class="flex items-center gap-2 text-xs font-medium text-zinc-200 cursor-pointer glass-card hover:bg-white/10 p-2.5 rounded-lg border border-white/5 transition-all">
                    <input type="checkbox" name="works_saturday" value="1" {{ old('works_saturday', true) ? 'checked' : '' }} 
                           class="w-4 h-4 rounded border-white/20 bg-zinc-900 text-amber-500 focus:ring-amber-500/50 focus:ring-offset-0">
                    <span>Sábado (Fuerte)</span>
                </label>
                <label class="flex items-center gap-2 text-xs font-medium text-zinc-200 cursor-pointer glass-card hover:bg-white/10 p-2.5 rounded-lg border border-white/5 transition-all">
                    <input type="checkbox" name="works_sunday" value="1" {{ old('works_sunday', true) ? 'checked' : '' }} 
                           class="w-4 h-4 rounded border-white/20 bg-zinc-900 text-amber-500 focus:ring-amber-500/50 focus:ring-offset-0">
                    <span>Domingo</span>
                </label>
            </div>
        </div>

        <!-- Tarifas de Pago por Noche -->
        <div>
            <label class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-2">
                Tarifas de Pago por Noche (Bs.)
            </label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div>
                    <label for="default_pay" class="block text-[11px] font-semibold text-zinc-400 mb-1">Base (Bs.)</label>
                    <input type="number" step="5" name="default_pay" id="default_pay" required value="{{ old('default_pay', 100.00) }}"
                           class="glass-input w-full px-3 py-2.5 rounded-xl text-sm text-white font-mono font-bold focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                </div>
                <div>
                    <label for="friday_pay" class="block text-[11px] font-semibold text-zinc-400 mb-1">Viernes (Bs.)</label>
                    <input type="number" step="5" name="friday_pay" id="friday_pay" value="{{ old('friday_pay', 100.00) }}"
                           class="glass-input w-full px-3 py-2.5 rounded-xl text-sm text-white font-mono font-bold focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                </div>
                <div>
                    <label for="saturday_pay" class="block text-[11px] font-semibold text-zinc-400 mb-1">Sábado (Bs.)</label>
                    <input type="number" step="5" name="saturday_pay" id="saturday_pay" value="{{ old('saturday_pay', 110.00) }}"
                           class="glass-input w-full px-3 py-2.5 rounded-xl text-sm text-white font-mono font-bold focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                </div>
                <div>
                    <label for="sunday_pay" class="block text-[11px] font-semibold text-zinc-400 mb-1">Domingo (Bs.)</label>
                    <input type="number" step="5" name="sunday_pay" id="sunday_pay" value="{{ old('sunday_pay', 110.00) }}"
                           class="glass-input w-full px-3 py-2.5 rounded-xl text-sm text-white font-mono font-bold focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                </div>
            </div>
        </div>

        <div>
            <label for="phone" class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-2">
                Teléfono / Celular (Opcional)
            </label>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="Ej. 70012345"
                   class="glass-input w-full px-4 py-3 rounded-xl text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
        </div>

        <div class="pt-6 flex items-center justify-end gap-3 border-t border-white/10">
            <a href="{{ route('staff.index') }}" 
               class="px-5 py-2.5 glass-card hover:bg-white/10 text-zinc-400 hover:text-white text-xs font-semibold rounded-xl transition-all">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-extrabold tracking-wider uppercase rounded-xl shadow-lg shadow-amber-500/20 active:scale-95 transition-all">
                Guardar Personal
            </button>
        </div>
    </form>
</div>
@endsection
