@extends('layouts.app')

@section('title', 'Nuevo Personal')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between border-b border-zinc-200 pb-4">
        <div>
            <h2 class="text-lg font-bold text-zinc-900 tracking-tight">Registrar Personal</h2>
            <p class="text-xs text-zinc-600">Nuevo integrante del equipo de trabajo de Don Ludo.</p>
        </div>
        <a href="{{ route('staff.index') }}" class="text-xs text-zinc-600 hover:text-zinc-900">&larr; Volver</a>
    </div>

    <form method="POST" action="{{ route('staff.store') }}" class="bg-white border border-zinc-200 rounded p-6 space-y-5">
        @csrf

        <div>
            <label for="name" class="block text-xs font-semibold text-zinc-700 mb-1">Nombre Completo / Apodo</label>
            <input type="text" name="name" id="name" required value="{{ old('name') }}"
                   placeholder="Ej. MAURI, ALEX(S), KELLY"
                   class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 uppercase focus:outline-none focus:ring-1 focus:ring-zinc-900">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="role" class="block text-xs font-semibold text-zinc-700 mb-1">Rol / Cargo</label>
                <select name="role" id="role" required class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
                    <option value="Staff / Mozo">Staff / Mozo</option>
                    <option value="Staff / Mesera">Staff / Mesera</option>
                    <option value="Seguridad">Seguridad (S)</option>
                    <option value="Bartender Principal">Bartender Principal</option>
                    <option value="Bartender Subterráneo">Bartender Subterráneo</option>
                    <option value="DJ / Sonido">DJ / Sonido</option>
                    <option value="Administrador">Administrador</option>
                </select>
            </div>

            <div>
                <label for="assigned_bar" class="block text-xs font-semibold text-zinc-700 mb-1">Barra / Área Asignada</label>
                <select name="assigned_bar" id="assigned_bar" class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
                    <option value="Pista / Mozos">Pista / Mozos</option>
                    <option value="Barra Principal">Barra Principal</option>
                    <option value="Barra Subterráneo">Barra Subterráneo</option>
                    <option value="Seguridad">Seguridad / Puerta</option>
                    <option value="Tienda">Tienda</option>
                    <option value="General">General</option>
                </select>
            </div>
        </div>

        <!-- Días que Asiste de Turno -->
        <div class="border border-zinc-200 rounded p-3 bg-zinc-50">
            <span class="block text-xs font-bold text-zinc-900 mb-1">Días de Turno Programados</span>
            <p class="text-[11px] text-zinc-500 mb-2">Marca los días de la semana en los que este trabajador asiste por defecto:</p>
            <div class="grid grid-cols-3 gap-3">
                <label class="flex items-center gap-2 text-xs text-zinc-800 font-medium cursor-pointer">
                    <input type="checkbox" name="works_friday" value="1" {{ old('works_friday', true) ? 'checked' : '' }} class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900">
                    <span>Viernes</span>
                </label>
                <label class="flex items-center gap-2 text-xs text-zinc-800 font-medium cursor-pointer">
                    <input type="checkbox" name="works_saturday" value="1" {{ old('works_saturday', true) ? 'checked' : '' }} class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900">
                    <span>Sábado (Fuerte)</span>
                </label>
                <label class="flex items-center gap-2 text-xs text-zinc-800 font-medium cursor-pointer">
                    <input type="checkbox" name="works_sunday" value="1" {{ old('works_sunday', true) ? 'checked' : '' }} class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900">
                    <span>Domingo</span>
                </label>
            </div>
        </div>

        <!-- Tarifas de Pago por Noche -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div>
                <label for="default_pay" class="block text-xs font-semibold text-zinc-700 mb-1">Tarifa Base (Bs.)</label>
                <input type="number" step="5" name="default_pay" id="default_pay" required value="{{ old('default_pay', 100.00) }}"
                       class="w-full text-xs border border-zinc-300 rounded px-2.5 py-1.5 text-zinc-900 font-mono font-bold focus:outline-none focus:ring-1 focus:ring-zinc-900">
            </div>
            <div>
                <label for="friday_pay" class="block text-xs font-semibold text-zinc-700 mb-1">Tarifa Vie (Bs.)</label>
                <input type="number" step="5" name="friday_pay" id="friday_pay" value="{{ old('friday_pay', 100.00) }}"
                       class="w-full text-xs border border-zinc-300 rounded px-2.5 py-1.5 text-zinc-900 font-mono focus:outline-none focus:ring-1 focus:ring-zinc-900">
            </div>
            <div>
                <label for="saturday_pay" class="block text-xs font-semibold text-zinc-700 mb-1">Tarifa Sáb (Bs.)</label>
                <input type="number" step="5" name="saturday_pay" id="saturday_pay" value="{{ old('saturday_pay', 110.00) }}"
                       class="w-full text-xs border border-zinc-300 rounded px-2.5 py-1.5 text-zinc-900 font-mono focus:outline-none focus:ring-1 focus:ring-zinc-900">
            </div>
            <div>
                <label for="sunday_pay" class="block text-xs font-semibold text-zinc-700 mb-1">Tarifa Dom (Bs.)</label>
                <input type="number" step="5" name="sunday_pay" id="sunday_pay" value="{{ old('sunday_pay', 110.00) }}"
                       class="w-full text-xs border border-zinc-300 rounded px-2.5 py-1.5 text-zinc-900 font-mono focus:outline-none focus:ring-1 focus:ring-zinc-900">
            </div>
        </div>

        <div>
            <label for="phone" class="block text-xs font-semibold text-zinc-700 mb-1">Teléfono / Celular (Opcional)</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="Ej. 70012345"
                   class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
        </div>

        <div class="pt-4 flex items-center justify-end gap-2 border-t border-zinc-200">
            <a href="{{ route('staff.index') }}" class="px-3 py-1.5 border border-zinc-300 text-xs font-medium rounded text-zinc-700 hover:bg-zinc-50">Cancelar</a>
            <button type="submit" class="px-4 py-1.5 bg-zinc-900 text-white text-xs font-semibold rounded hover:bg-zinc-800">Guardar Personal</button>
        </div>
    </form>
</div>
@endsection
