@extends('layouts.app')

@section('title', 'Nuevo Producto')

@section('content')
<div class="max-w-2xl mx-auto space-y-5 pb-16">
    <!-- Header -->
    <div class="notoria-card px-6 py-4 flex items-center justify-between shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-0.5">
                <span class="text-[10px] font-mono font-bold text-[#F5B81C] uppercase tracking-wider">
                    Catálogo Maestro
                </span>
                <span class="theme-text-muted text-xs">•</span>
                <span class="text-[10px] theme-text-secondary font-mono">Bodega & Barras</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight theme-text-primary font-sans">
                Agregar Bebida o Insumo
            </h1>
            <p class="text-xs theme-text-secondary mt-0.5">
                Registra un nuevo ítem para inventario de barras y almacén central.
            </p>
        </div>
        <a href="{{ route('products.index') }}" 
           class="notoria-subtile hover:border-amber-500 theme-text-primary px-3.5 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    <!-- Form Panel -->
    <form method="POST" action="{{ route('products.store') }}" class="notoria-card p-6 sm:p-7 shadow-lg space-y-5">
        @csrf

        <div>
            <label for="name" class="block text-xs font-bold theme-text-secondary uppercase tracking-wider mb-2">
                Nombre de la Bebida / Insumo <span class="text-amber-500">*</span>
            </label>
            <input type="text" name="name" id="name" required value="{{ old('name') }}"
                   placeholder="Ej. Fernet Branca 750ml, Singani San Pedro Oro"
                   class="notoria-input w-full px-4 py-2.5 text-sm theme-text-primary placeholder-zinc-400 focus:outline-none">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="category" class="block text-xs font-bold theme-text-secondary uppercase tracking-wider mb-2">
                    Categoría <span class="text-amber-500">*</span>
                </label>
                <select name="category" id="category" required 
                        class="notoria-input w-full px-4 py-2.5 text-sm theme-text-primary focus:outline-none cursor-pointer">
                    <option value="Licores" class="bg-slate-900 text-white">Licores (Botellas)</option>
                    <option value="Mixers" class="bg-slate-900 text-white">Mixers / Gaseosas / Aguas</option>
                    <option value="Cervezas" class="bg-slate-900 text-white">Cervezas</option>
                    <option value="Cigarros" class="bg-slate-900 text-white">Cigarros</option>
                    <option value="Otros" class="bg-slate-900 text-white">Otros / Insumos</option>
                </select>
            </div>

            <div>
                <label for="unit" class="block text-xs font-bold theme-text-secondary uppercase tracking-wider mb-2">
                    Presentación / Volumen <span class="text-amber-500">*</span>
                </label>
                <input type="text" name="unit" id="unit" required value="{{ old('unit', 'Botella 750ml') }}"
                       placeholder="Botella 750ml, 2.0L, Lata"
                       class="notoria-input w-full px-4 py-2.5 text-sm theme-text-primary placeholder-zinc-400 focus:outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="sale_price" class="block text-xs font-bold theme-text-secondary uppercase tracking-wider mb-2">
                    Precio Venta (Bs.) <span class="text-amber-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-2.5 text-xs font-mono font-bold text-[#F5B81C]">Bs.</span>
                    <input type="number" step="0.5" name="sale_price" id="sale_price" required value="{{ old('sale_price', 0) }}"
                           class="notoria-input w-full pl-10 pr-3 py-2.5 text-sm font-mono font-black theme-text-primary focus:outline-none">
                </div>
            </div>

            <div>
                <label for="cost_price" class="block text-xs font-bold theme-text-secondary uppercase tracking-wider mb-2">
                    Costo Compra (Bs.)
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-2.5 text-xs font-mono font-bold theme-text-muted">Bs.</span>
                    <input type="number" step="0.5" name="cost_price" id="cost_price" value="{{ old('cost_price', 0) }}"
                           class="notoria-input w-full pl-10 pr-3 py-2.5 text-sm font-mono font-bold theme-text-secondary focus:outline-none">
                </div>
            </div>

            <div>
                <label for="units_per_package" class="block text-xs font-bold theme-text-secondary uppercase tracking-wider mb-2">
                    Unid. x Caja / Paq.
                </label>
                <input type="number" name="units_per_package" id="units_per_package" required value="{{ old('units_per_package', 6) }}"
                       class="notoria-input w-full px-4 py-2.5 text-sm font-mono font-bold theme-text-primary focus:outline-none">
            </div>
        </div>

        <div>
            <label for="stock_warehouse" class="block text-xs font-bold theme-text-secondary uppercase tracking-wider mb-2">
                Stock Inicial en Almacén
            </label>
            <input type="number" name="stock_warehouse" id="stock_warehouse" required value="{{ old('stock_warehouse', 0) }}"
                   class="notoria-input w-full px-4 py-2.5 text-sm font-mono font-black text-[#F5B81C] focus:outline-none">
            <p class="text-[11px] theme-text-muted mt-1.5 font-sans">Cantidad física disponible en almacén antes del despacho a barras.</p>
        </div>

        <div class="pt-5 flex items-center justify-end gap-3 border-t notoria-border">
            <a href="{{ route('products.index') }}" 
               class="px-5 py-2.5 notoria-subtile hover:border-zinc-400 theme-text-secondary text-xs font-bold rounded-xl transition-all">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-6 py-2.5 bg-[#F5B81C] hover:bg-[#e5ac18] text-black text-xs font-black tracking-wider uppercase rounded-xl shadow-md active:scale-95 transition-all cursor-pointer">
                Guardar Bebida
            </button>
        </div>
    </form>
</div>
@endsection