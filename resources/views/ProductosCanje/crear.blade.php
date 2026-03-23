@extends('layouts.app')

@section('titulo', isset($canje) ? 'Editar Canje' : 'Nuevo Canje')

@section('content')

<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-2xl shadow p-8">

        <h3 class="text-xl font-bold text-gray-800 mb-6">
            {{ isset($canje) ? 'Editar producto de canje' : 'Registrar producto de canje' }}
        </h3>

        @php
        // Construir lista de productos con etiquetas correctas de tamaño
        $productosJs = $productos->map(fn($p) => [
            'id'     => $p->id,
            'precios' => $p->precios->map(fn($pr) => [
                'id'     => $pr->tamanio_id,
                'nombre' => $pr->tamanio
                    ? (in_array($pr->tamanio->unidad, ['vaso', 'litro'])
                        ? ucfirst($pr->tamanio->unidad)
                        : $pr->tamanio->cantidad . ' ' . $pr->tamanio->unidad)
                    : '',
            ])->values(),
        ])->values();

        // Precios del producto actual en edición
        $preciosActuales = isset($canje) && $canje->producto
            ? $canje->producto->precios->map(fn($pr) => [
                'id'     => $pr->tamanio_id,
                'nombre' => $pr->tamanio
                    ? (in_array($pr->tamanio->unidad, ['vaso', 'litro'])
                        ? ucfirst($pr->tamanio->unidad)
                        : $pr->tamanio->cantidad . ' ' . $pr->tamanio->unidad)
                    : '',
            ])->values()
            : collect();
        @endphp

        <form method="POST"
              action="{{ isset($canje) ? route('canjes.update', $canje) : route('canjes.store') }}"
              x-data="{
                  productoSeleccionado: {{ isset($canje) ? $canje->producto_id : 'null' }},
                  precios: {{ $preciosActuales->toJson() }},
                  productos: {{ $productosJs->toJson() }},
                  tamanioActual: {{ isset($canje) ? ($canje->tamanio_id ?? 'null') : 'null' }},
                  cambiarProducto(id) {
                      this.productoSeleccionado = id;
                      const prod = this.productos.find(p => p.id == id);
                      this.precios = prod ? prod.precios : [];
                      this.tamanioActual = null;
                  }
              }">
            @csrf
            @if(isset($canje)) @method('PUT') @endif

            {{-- Producto --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Producto <span class="text-red-500">*</span>
                </label>
                <select name="producto_id"
                        @change="cambiarProducto($event.target.value)"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500
                               @error('producto_id') border-red-500 @enderror">
                    <option value="">-- Selecciona --</option>
                    @foreach($productos as $producto)
                        <option value="{{ $producto->id }}"
                            {{ old('producto_id', $canje->producto_id ?? '') == $producto->id ? 'selected' : '' }}>
                            {{ $producto->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('producto_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tamaño — solo aparece si el producto tiene precios por tamaño --}}
            <div class="mb-4" x-show="precios.length > 0" x-cloak>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tamaño</label>
                <select name="tamanio_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">Todos los tamaños</option>
                    <template x-for="precio in precios" :key="precio.id">
                        <option :value="precio.id"
                                :selected="precio.id == tamanioActual"
                                x-text="precio.nombre">
                        </option>
                    </template>
                </select>
            </div>

            {{-- Puntos --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Costo en puntos <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-yellow-500 text-sm">★</span>
                    <input type="number" name="puntos_costo" min="1"
                           value="{{ old('puntos_costo', $canje->puntos_costo ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg pl-7 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500
                                  @error('puntos_costo') border-red-500 @enderror">
                </div>
                @error('puntos_costo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Estado --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="estado"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="activo"   {{ old('estado', $canje->estado ?? 'activo') === 'activo'   ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ old('estado', $canje->estado ?? '') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>

            {{-- Botones --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('canjes.index') }}"
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold px-5 py-2 rounded-lg transition">
                    Cancelar
                </a>
                <button type="submit"
                        onclick="this.disabled=true; this.innerText='Guardando...'; this.form.submit();"
                        class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
                    {{ isset($canje) ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>

        </form>
    </div>
</div>

@endsection