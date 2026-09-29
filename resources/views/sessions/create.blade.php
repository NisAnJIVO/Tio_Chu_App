@extends('layouts.app')

@section('title', 'Nueva Noche')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between border-b border-white/10 pb-4">
        <div>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-1">
                Apertura Operativa
            </span>
            <h2 class="text-xl font-black text-white tracking-tight">Aperturar Nueva Noche</h2>
            <p class="text-xs text-zinc-400">Inicializa automáticamente los inventarios de las 3 barras y la planilla de asistencia.</p>
        </div>
        <a href="{{ route('sessions.index') }}" 
           class="glass-card hover:bg-white/10 text-zinc-300 hover:text-white px-3.5 py-2 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-all">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al Historial
        </a>
    </div>

    <!-- Form Panel -->
    <form method="POST" action="{{ route('sessions.store') }}" class="glass-panel p-6 sm:p-8 rounded-2xl border border-white/10 shadow-2xl space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label for="session_date" class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-2">
                    Fecha de la Noche <span class="text-amber-400">*</span>
                </label>
                <input type="date" name="session_date" id="session_date" required 
                       value="{{ old('session_date', \Carbon\Carbon::now()->format('Y-m-d')) }}"
                       class="glass-input w-full px-4 py-3 rounded-xl text-sm font-mono text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all [color-scheme:dark]">
                <p class="text-[11px] text-zinc-500 mt-1.5">Selecciona el día de inicio de la jornada nocturna.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-2">
                    Día Detectado (Automático)
                </label>
                <div class="flex items-center h-[46px] px-4 glass-card border border-white/10 rounded-xl text-sm font-bold text-amber-400 font-mono" id="detected_day_display">
                    Calculando...
                </div>
                <p class="text-[11px] text-zinc-500 mt-1.5">Detecta Viernes, Sábado o Domingo para fijar la tarifa de personal.</p>
            </div>
        </div>

        <div>
            <label for="pos_commission_rate" class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-2">
                Tasa de Comisión Tarjetero / POS (Decimal) <span class="text-amber-400">*</span>
            </label>
            <div class="flex items-center gap-3">
                <input type="number" step="0.0001" name="pos_commission_rate" id="pos_commission_rate" required value="{{ old('pos_commission_rate', 0.0130) }}"
                       class="glass-input w-full px-4 py-3 rounded-xl text-sm font-mono font-bold text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                <span class="text-xs font-mono font-bold text-amber-400 whitespace-nowrap bg-amber-500/10 px-3 py-3 rounded-xl border border-amber-500/20">
                    = 1.30%
                </span>
            </div>
            <p class="text-[11px] text-zinc-500 mt-1.5">Por defecto 0.0130 (1.3% bancario). Ajustable si la tasa varía.</p>
        </div>

        <div>
            <label for="notes" class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-2">
                Notas del Evento (Opcional)
            </label>
            <textarea name="notes" id="notes" rows="2" placeholder="Ej. Fiesta temática 80s, concierto en vivo, DJ invitado, feriado..."
                      class="glass-input w-full px-4 py-3 rounded-xl text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">{{ old('notes') }}</textarea>
        </div>

        <div class="pt-6 flex items-center justify-end gap-3 border-t border-white/10">
            <a href="{{ route('sessions.index') }}" 
               class="px-5 py-2.5 glass-card hover:bg-white/10 text-zinc-400 hover:text-white text-xs font-semibold rounded-xl transition-all">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-extrabold tracking-wider uppercase rounded-xl shadow-lg shadow-amber-500/20 active:scale-95 transition-all">
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
