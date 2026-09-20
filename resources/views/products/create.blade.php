@extends('layouts.app')

@section('title', 'Nuevo Producto')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between border-b border-zinc-200 pb-4">
        <div>
            <h2 class="text-lg font-bold text-zinc-900 tracking-tight">Agregar Bebida o Insumo</h2>
            <p class="text-xs text-zinc-600">Registra un nuevo ítem al catálogo maestro.</p>
        </div>
        <a href="{{ route('products.index') }}" class="text-xs text-zinc-600 hover:text-zinc-900">&larr; Volver</a>
    </div>

    <form method="POST" action="{{ route('products.store') }}" class="bg-white border border-zinc-200 rounded p-6 space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-xs font-semibold text-zinc-700 mb-1">Nombre del Producto</label>
            <input type="text" name="name" id="name" required value="{{ old('name') }}"
                   placeholder="Ej. Fernet Branca 750ml"
                   class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="category" class="block text-xs font-semibold text-zinc-700 mb-1">Categoría</label>
                <select name="category" id="category" required class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
                    <option value="Licores">Licores</option>
                    <option value="Mixers">Mixers / Extras</option>
                    <option value="Cervezas">Cervezas</option>
                    <option value="Cigarros">Cigarros</option>
                    <option value="Otros">Otros</option>
                </select>
            </div>

            <div>
                <label for="unit" class="block text-xs font-semibold text-zinc-700 mb-1">Presentación / Unidad</label>
                <input type="text" name="unit" id="unit" required value="{{ old('unit', 'Botella') }}"
                       placeholder="Botella, 2.0L, Lata"
                       class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label for="sale_price" class="block text-xs font-semibold text-zinc-700 mb-1">Precio Venta (Bs.)</label>
                <input type="number" step="0.5" name="sale_price" id="sale_price" required value="{{ old('sale_price', 0) }}"
                       class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 font-mono focus:outline-none focus:ring-1 focus:ring-zinc-900">
            </div>

            <div>
                <label for="cost_price" class="block text-xs font-semibold text-zinc-700 mb-1">Costo (Opcional)</label>
                <input type="number" step="0.5" name="cost_price" id="cost_price" value="{{ old('cost_price', 0) }}"
                       class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 font-mono focus:outline-none focus:ring-1 focus:ring-zinc-900">
            </div>

            <div>
                <label for="units_per_package" class="block text-xs font-semibold text-zinc-700 mb-1">Unid. x Caja/Paquete</label>
                <input type="number" name="units_per_package" id="units_per_package" required value="{{ old('units_per_package', 6) }}"
                       class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 font-mono focus:outline-none focus:ring-1 focus:ring-zinc-900">
            </div>
        </div>

        <div>
            <label for="stock_warehouse" class="block text-xs font-semibold text-zinc-700 mb-1">Stock Inicial en Almacén</label>
            <input type="number" name="stock_warehouse" id="stock_warehouse" required value="{{ old('stock_warehouse', 0) }}"
                   class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 font-mono focus:outline-none focus:ring-1 focus:ring-zinc-900">
        </div>

        <div class="pt-4 flex items-center justify-end gap-2 border-t border-zinc-200">
            <a href="{{ route('products.index') }}" class="px-3 py-1.5 border border-zinc-300 text-xs font-medium rounded text-zinc-700 hover:bg-zinc-50">Cancelar</a>
            <button type="submit" class="px-4 py-1.5 bg-zinc-900 text-white text-xs font-semibold rounded hover:bg-zinc-800">Guardar Producto</button>
        </div>
    </form>
</div>
@endsection
