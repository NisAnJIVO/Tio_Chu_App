@extends('layouts.app')

@section('title', 'Editar Producto')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between border-b border-zinc-200 pb-4">
        <div>
            <h2 class="text-lg font-bold text-zinc-900 tracking-tight">Editar Producto #{{ $product->id }}</h2>
            <p class="text-xs text-zinc-600">{{ $product->name }}</p>
        </div>
        <a href="{{ route('products.index') }}" class="text-xs text-zinc-600 hover:text-zinc-900">&larr; Volver</a>
    </div>

    <form method="POST" action="{{ route('products.update', $product) }}" class="bg-white border border-zinc-200 rounded p-6 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-xs font-semibold text-zinc-700 mb-1">Nombre del Producto</label>
            <input type="text" name="name" id="name" required value="{{ old('name', $product->name) }}"
                   class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="category" class="block text-xs font-semibold text-zinc-700 mb-1">Categoría</label>
                <select name="category" id="category" required class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
                    <option value="Licores" {{ $product->category === 'Licores' ? 'selected' : '' }}>Licores</option>
                    <option value="Mixers" {{ $product->category === 'Mixers' ? 'selected' : '' }}>Mixers / Extras</option>
                    <option value="Cervezas" {{ $product->category === 'Cervezas' ? 'selected' : '' }}>Cervezas</option>
                    <option value="Cigarros" {{ $product->category === 'Cigarros' ? 'selected' : '' }}>Cigarros</option>
                    <option value="Otros" {{ $product->category === 'Otros' ? 'selected' : '' }}>Otros</option>
                </select>
            </div>

            <div>
                <label for="unit" class="block text-xs font-semibold text-zinc-700 mb-1">Presentación / Unidad</label>
                <input type="text" name="unit" id="unit" required value="{{ old('unit', $product->unit) }}"
                       class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label for="sale_price" class="block text-xs font-semibold text-zinc-700 mb-1">Precio Venta (Bs.)</label>
                <input type="number" step="0.5" name="sale_price" id="sale_price" required value="{{ old('sale_price', $product->sale_price) }}"
                       class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 font-mono focus:outline-none focus:ring-1 focus:ring-zinc-900">
            </div>

            <div>
                <label for="cost_price" class="block text-xs font-semibold text-zinc-700 mb-1">Costo (Opcional)</label>
                <input type="number" step="0.5" name="cost_price" id="cost_price" value="{{ old('cost_price', $product->cost_price) }}"
                       class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 font-mono focus:outline-none focus:ring-1 focus:ring-zinc-900">
            </div>

            <div>
                <label for="units_per_package" class="block text-xs font-semibold text-zinc-700 mb-1">Unid. x Caja/Paquete</label>
                <input type="number" name="units_per_package" id="units_per_package" required value="{{ old('units_per_package', $product->units_per_package) }}"
                       class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 font-mono focus:outline-none focus:ring-1 focus:ring-zinc-900">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="stock_warehouse" class="block text-xs font-semibold text-zinc-700 mb-1">Stock Almacén</label>
                <input type="number" name="stock_warehouse" id="stock_warehouse" required value="{{ old('stock_warehouse', $product->stock_warehouse) }}"
                       class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 font-mono focus:outline-none focus:ring-1 focus:ring-zinc-900">
            </div>

            <div>
                <label for="is_active" class="block text-xs font-semibold text-zinc-700 mb-1">Estado</label>
                <select name="is_active" id="is_active" class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
                    <option value="1" {{ $product->is_active ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ !$product->is_active ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
        </div>

        <div class="pt-4 flex items-center justify-between border-t border-zinc-200">
            <button type="button" onclick="if(confirm('¿Eliminar producto?')) document.getElementById('delete-prod-form').submit();" class="text-xs text-red-600 hover:text-red-800">
                Eliminar Producto
            </button>
            <div class="flex items-center gap-2">
                <a href="{{ route('products.index') }}" class="px-3 py-1.5 border border-zinc-300 text-xs font-medium rounded text-zinc-700 hover:bg-zinc-50">Cancelar</a>
                <button type="submit" class="px-4 py-1.5 bg-zinc-900 text-white text-xs font-semibold rounded hover:bg-zinc-800">Actualizar</button>
            </div>
        </div>
    </form>

    <form id="delete-prod-form" method="POST" action="{{ route('products.destroy', $product) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection
