@extends('layouts.app')

@section('title', 'Nueva Noche')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between border-b border-zinc-200 pb-4">
        <div>
            <h2 class="text-lg font-bold text-zinc-900 tracking-tight">Aperturar Nueva Noche</h2>
            <p class="text-xs text-zinc-600">Inicializa automáticamente el inventario de las barras y la lista de personal para la fecha.</p>
        </div>
        <a href="{{ route('sessions.index') }}" class="text-xs text-zinc-600 hover:text-zinc-900">&larr; Volver</a>
    </div>

    <form method="POST" action="{{ route('sessions.store') }}" class="bg-white border border-zinc-200 rounded p-6 space-y-4">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="session_date" class="block text-xs font-semibold text-zinc-700 mb-1">Fecha de la Noche</label>
                <input type="date" name="session_date" id="session_date" required 
                       min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                       value="{{ old('session_date', \Carbon\Carbon::now()->format('Y-m-d')) }}"
                       class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
                <p class="text-[10px] text-zinc-500 mt-1">Solo fechas de hoy en adelante.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-zinc-700 mb-1">Día Detectado Automático</label>
                <div class="flex items-center h-[38px] px-3 bg-zinc-50 border border-zinc-200 rounded text-xs font-semibold text-zinc-900" id="detected_day_display">
                    Calculando...
                </div>
                <p class="text-[10px] text-zinc-500 mt-1">Se calcula solo según el calendario.</p>
            </div>
        </div>

        <div>
            <label for="pos_commission_rate" class="block text-xs font-semibold text-zinc-700 mb-1">
                Tasa de Comisión Tarjetero / POS (Decimal)
            </label>
            <div class="flex items-center gap-2">
                <input type="number" step="0.0001" name="pos_commission_rate" id="pos_commission_rate" required value="{{ old('pos_commission_rate', 0.0130) }}"
                       class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 font-mono focus:outline-none focus:ring-1 focus:ring-zinc-900">
                <span class="text-xs text-zinc-600 font-medium whitespace-nowrap">(0.013 = 1.3%)</span>
            </div>
            <p class="text-[11px] text-zinc-600 mt-1">Don Ludo puede ajustar este valor si el banco cobra otra tarifa.</p>
        </div>

        <div>
            <label for="notes" class="block text-xs font-semibold text-zinc-700 mb-1">Notas del Evento (Opcional)</label>
            <textarea name="notes" id="notes" rows="2" placeholder="Ej. Fiesta temática, aniversario, DJ invitado..."
                      class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">{{ old('notes') }}</textarea>
        </div>

        <div class="pt-4 flex items-center justify-end gap-2 border-t border-zinc-200">
            <a href="{{ route('sessions.index') }}" class="px-3 py-1.5 border border-zinc-300 text-xs font-medium rounded text-zinc-700 hover:bg-zinc-50">Cancelar</a>
            <button type="submit" class="px-4 py-1.5 bg-zinc-900 text-white text-xs font-semibold rounded hover:bg-zinc-800">
                Aperturar Noche
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dateInput = document.getElementById('session_date');
    const dayDisplay = document.getElementById('detected_day_display');
    const dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

    function updateDay() {
        if (!dateInput.value) return;
        // Parse date considering local timezone
        const parts = dateInput.value.split('-');
        if (parts.length === 3) {
            const dateObj = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
            const dayName = dias[dateObj.getDay()];
            dayDisplay.textContent = dayName;
        }
    }

    dateInput.addEventListener('input', updateDay);
    dateInput.addEventListener('change', updateDay);
    updateDay();
});
</script>
@endsection
