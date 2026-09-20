@extends('layouts.app')

@section('title', 'Editar Personal')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between border-b border-zinc-200 pb-4">
        <div>
            <h2 class="text-lg font-bold text-zinc-900 tracking-tight">Editar Personal: {{ $staff->name }}</h2>
            <p class="text-xs text-zinc-600">Actualizar datos de cargo, días de turno o tarifas por noche trabajada.</p>
        </div>
        <a href="{{ route('staff.index') }}" class="text-xs text-zinc-600 hover:text-zinc-900">&larr; Volver</a>
    </div>

    <form method="POST" action="{{ route('staff.update', $staff) }}" class="bg-white border border-zinc-200 rounded p-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-xs font-semibold text-zinc-700 mb-1">Nombre Completo / Apodo</label>
            <input type="text" name="name" id="name" required value="{{ old('name', $staff->name) }}"
                   class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 uppercase focus:outline-none focus:ring-1 focus:ring-zinc-900">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="role" class="block text-xs font-semibold text-zinc-700 mb-1">Rol / Cargo</label>
                <select name="role" id="role" required class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
                    <option value="Staff / Mozo" {{ $staff->role === 'Staff / Mozo' ? 'selected' : '' }}>Staff / Mozo</option>
                    <option value="Staff / Mesera" {{ $staff->role === 'Staff / Mesera' ? 'selected' : '' }}>Staff / Mesera</option>
                    <option value="Seguridad" {{ $staff->role === 'Seguridad' ? 'selected' : '' }}>Seguridad (S)</option>
                    <option value="Bartender Principal" {{ $staff->role === 'Bartender Principal' ? 'selected' : '' }}>Bartender Principal</option>
                    <option value="Bartender Subterráneo" {{ $staff->role === 'Bartender Subterráneo' ? 'selected' : '' }}>Bartender Subterráneo</option>
                    <option value="DJ / Sonido" {{ $staff->role === 'DJ / Sonido' ? 'selected' : '' }}>DJ / Sonido</option>
                    <option value="Administrador" {{ $staff->role === 'Administrador' ? 'selected' : '' }}>Administrador</option>
                </select>
            </div>

            <div>
                <label for="assigned_bar" class="block text-xs font-semibold text-zinc-700 mb-1">Barra / Área Asignada</label>
                <select name="assigned_bar" id="assigned_bar" class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
                    <option value="Pista / Mozos" {{ $staff->assigned_bar === 'Pista / Mozos' ? 'selected' : '' }}>Pista / Mozos</option>
                    <option value="Barra Principal" {{ $staff->assigned_bar === 'Barra Principal' ? 'selected' : '' }}>Barra Principal</option>
                    <option value="Barra Subterráneo" {{ $staff->assigned_bar === 'Barra Subterráneo' ? 'selected' : '' }}>Barra Subterráneo</option>
                    <option value="Seguridad" {{ $staff->assigned_bar === 'Seguridad' ? 'selected' : '' }}>Seguridad / Puerta</option>
                    <option value="Tienda" {{ $staff->assigned_bar === 'Tienda' ? 'selected' : '' }}>Tienda</option>
                    <option value="General" {{ $staff->assigned_bar === 'General' ? 'selected' : '' }}>General</option>
                </select>
            </div>
        </div>

        <!-- Días que Asiste de Turno -->
        <div class="border border-zinc-200 rounded p-3 bg-zinc-50">
            <span class="block text-xs font-bold text-zinc-900 mb-1">Días de Turno Programados</span>
            <p class="text-[11px] text-zinc-500 mb-2">Marca los días de la semana en los que este trabajador asiste por defecto:</p>
            <div class="grid grid-cols-3 gap-3">
                <label class="flex items-center gap-2 text-xs text-zinc-800 font-medium cursor-pointer">
                    <input type="checkbox" name="works_friday" value="1" {{ old('works_friday', $staff->works_friday) ? 'checked' : '' }} class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900">
                    <span>Viernes</span>
                </label>
                <label class="flex items-center gap-2 text-xs text-zinc-800 font-medium cursor-pointer">
                    <input type="checkbox" name="works_saturday" value="1" {{ old('works_saturday', $staff->works_saturday) ? 'checked' : '' }} class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900">
                    <span>Sábado (Fuerte)</span>
                </label>
                <label class="flex items-center gap-2 text-xs text-zinc-800 font-medium cursor-pointer">
                    <input type="checkbox" name="works_sunday" value="1" {{ old('works_sunday', $staff->works_sunday) ? 'checked' : '' }} class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900">
                    <span>Domingo</span>
                </label>
            </div>
        </div>

        <!-- Tarifas de Pago por Noche -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div>
                <label for="default_pay" class="block text-xs font-semibold text-zinc-700 mb-1">Tarifa Base (Bs.)</label>
                <input type="number" step="5" name="default_pay" id="default_pay" required value="{{ old('default_pay', $staff->default_pay) }}"
                       class="w-full text-xs border border-zinc-300 rounded px-2.5 py-1.5 text-zinc-900 font-mono font-bold focus:outline-none focus:ring-1 focus:ring-zinc-900">
            </div>
            <div>
                <label for="friday_pay" class="block text-xs font-semibold text-zinc-700 mb-1">Tarifa Vie (Bs.)</label>
                <input type="number" step="5" name="friday_pay" id="friday_pay" value="{{ old('friday_pay', $staff->friday_pay) }}"
                       class="w-full text-xs border border-zinc-300 rounded px-2.5 py-1.5 text-zinc-900 font-mono focus:outline-none focus:ring-1 focus:ring-zinc-900">
            </div>
            <div>
                <label for="saturday_pay" class="block text-xs font-semibold text-zinc-700 mb-1">Tarifa Sáb (Bs.)</label>
                <input type="number" step="5" name="saturday_pay" id="saturday_pay" value="{{ old('saturday_pay', $staff->saturday_pay) }}"
                       class="w-full text-xs border border-zinc-300 rounded px-2.5 py-1.5 text-zinc-900 font-mono focus:outline-none focus:ring-1 focus:ring-zinc-900">
            </div>
            <div>
                <label for="sunday_pay" class="block text-xs font-semibold text-zinc-700 mb-1">Tarifa Dom (Bs.)</label>
                <input type="number" step="5" name="sunday_pay" id="sunday_pay" value="{{ old('sunday_pay', $staff->sunday_pay) }}"
                       class="w-full text-xs border border-zinc-300 rounded px-2.5 py-1.5 text-zinc-900 font-mono focus:outline-none focus:ring-1 focus:ring-zinc-900">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="phone" class="block text-xs font-semibold text-zinc-700 mb-1">Teléfono / Celular (Opcional)</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $staff->phone) }}"
                       class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
            </div>

            <div>
                <label for="is_active" class="block text-xs font-semibold text-zinc-700 mb-1">Estado</label>
                <select name="is_active" id="is_active" class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
                    <option value="1" {{ $staff->is_active ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ !$staff->is_active ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
        </div>

        <div class="pt-4 flex items-center justify-between border-t border-zinc-200">
            <button type="button" onclick="if(confirm('¿Eliminar personal?')) document.getElementById('delete-staff-form').submit();" class="text-xs text-red-600 hover:text-red-800">
                Eliminar Personal
            </button>
            <div class="flex items-center gap-2">
                <a href="{{ route('staff.index') }}" class="px-3 py-1.5 border border-zinc-300 text-xs font-medium rounded text-zinc-700 hover:bg-zinc-50">Cancelar</a>
                <button type="submit" class="px-4 py-1.5 bg-zinc-900 text-white text-xs font-semibold rounded hover:bg-zinc-800">Guardar Cambios</button>
            </div>
        </div>
    </form>

    <form id="delete-staff-form" method="POST" action="{{ route('staff.destroy', $staff) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection
