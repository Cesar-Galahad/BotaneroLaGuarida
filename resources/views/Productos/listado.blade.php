@extends('layouts.app')

@section('titulo', 'Productos')

@section('content')

@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded-lg">
        {{ session('success') }}
    </div>
@endif

{{-- Encabezado --}}
<div class="flex justify-between items-center mb-6">
    <h3 class="text-xl font-bold text-gray-800">Listado de productos</h3>
    @php $rol = Auth::guard('empleado')->user()->rol->nombre ?? ''; @endphp

    @if($rol === 'Administrador')
        <a href="{{ route('productos.create') }}"
        class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            + Nuevo producto
        </a>
    @endif
</div>
@php $rol = Auth::guard('empleado')->user()->rol->nombre ?? ''; @endphp

{{-- Filtros --}}
<form method="GET" action="{{ route('productos.index') }}"
    class="bg-white rounded-2xl shadow p-4 mb-6 flex flex-wrap gap-3 items-end">

    <div class="flex-1 min-w-48">
        <label class="block text-xs font-medium text-gray-600 mb-1">Buscar</label>
        <input type="text" name="buscar" value="{{ request('buscar') }}"
            placeholder="Nombre del producto..."
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
    </div>

    <div class="min-w-40">
        <label class="block text-xs font-medium text-gray-600 mb-1">Categoría</label>
        <select name="categoria_id"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            <option value="">Todas</option>
            @foreach($categorias as $categoria)
                <option value="{{ $categoria->id }}"
                    {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                    {{ $categoria->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    @if($rol === 'Administrador')
    <div class="min-w-36">
        <label class="block text-xs font-medium text-gray-600 mb-1">Estado</label>
        <select name="estado"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            <option value="">Todos</option>
            <option value="activo"   {{ request('estado') === 'activo'   ? 'selected' : '' }}>Activo</option>
            <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
        </select>
    </div>
    @endif

    <div class="flex gap-2">
        <button type="submit"
                class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            Filtrar
        </button>
        <a href="{{ route('productos.index') }}"
        class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg transition">
            Limpiar
        </a>
    </div>

</form>

{{-- Grid de cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6" x-data>
    @forelse($productos as $producto)
    <div class="bg-white rounded-2xl shadow hover:shadow-md transition flex flex-col overflow-hidden">

        {{-- Imagen --}}
        <div class="w-full h-40 bg-gray-100 flex items-center justify-center overflow-hidden">
            @if($producto->imagen)
                <img src="{{ asset('storage/' . $producto->imagen) }}"
                    class="w-full h-full object-cover">
            @else
                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 3h18M3 3v18M3 3l18 18"/>
                </svg>
            @endif
        </div>

        {{-- Datos --}}
        <div class="p-4 flex flex-col flex-1">

            <div class="flex justify-between items-start mb-1">
                <h4 class="font-bold text-gray-800 text-sm leading-tight">{{ $producto->nombre }}</h4>
                <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-semibold shrink-0
                    {{ $producto->estado === 'activo' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ ucfirst($producto->estado) }}
                </span>
            </div>

            <p class="text-xs text-gray-400 mb-1">{{ $producto->categoria->nombre ?? '—' }}</p>

            @if($producto->descripcion)
                <p class="text-xs text-gray-500 mb-3 line-clamp-2">{{ $producto->descripcion }}</p>
            @endif

            <div class="mt-auto flex justify-between items-center">
                <div>
                    @if($producto->precios->count() > 0)
                        <div class="space-y-0.5">
                            @foreach($producto->precios as $precio)
                                @php
                                    $tamanio  = $precio->tamanio;
                                    // CORREGIDO: vaso y litro sin número, pz con número
                                    $etiqueta = $tamanio
                                        ? (in_array($tamanio->unidad, ['vaso', 'litro'])
                                            ? ucfirst($tamanio->unidad)
                                            : $tamanio->cantidad . ' ' . $tamanio->unidad)
                                        : '—';
                                @endphp
                                <p class="text-sm font-bold text-red-600">
                                    {{ $etiqueta }}: ${{ number_format($precio->precio, 2) }}
                                </p>
                            @endforeach
                        </div>
                    @else
                        <p class="text-lg font-bold text-red-600">${{ number_format($producto->precio_base, 2) }}</p>
                    @endif
                    <p class="text-xs text-gray-400">Existencia: {{ $producto->existencia }}</p>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex gap-2 mt-3">
                @php $rol = Auth::guard('empleado')->user()->rol->nombre ?? ''; @endphp

                @if($rol === 'Administrador')
                    <a href="{{ route('productos.edit', $producto) }}"
                    class="flex-1 text-center bg-yellow-400 hover:bg-yellow-500 text-black text-xs font-semibold py-1.5 rounded-lg transition">
                        Editar
                    </a>
                @endif
                <form method="POST" action="{{ route('productos.destroy', $producto) }}"
                    id="form-producto-{{ $producto->id }}">
                    @if($rol === 'Administrador')
                    @csrf
                    @method('DELETE')
                    <button type="button"
                            @click="$dispatch('abrir-confirm', {
                            mensaje: '¿Cambiar estado de este producto?',
                            formId: 'form-producto-{{ $producto->id }}'
                        })"
                            class="text-xs font-semibold px-3 py-1 rounded-lg transition
                                {{ $producto->estado === 'activo'
                                    ? 'bg-red-600 hover:bg-red-700 text-white'
                                    : 'bg-green-600 hover:bg-green-700 text-white' }}">
                        {{ $producto->estado === 'activo' ? 'Desactivar' : 'Activar' }}
                    </button>
                    @endif
                </form>
            </div>

        </div>
    </div>
    @empty
        <div class="col-span-4 text-center py-16 text-gray-400">
            No hay productos registrados.
        </div>
    @endforelse
</div>

{{-- Paginación --}}
@if($productos->hasPages())
<div class="mt-6 flex justify-center">
    <div class="flex items-center gap-1">

        @if($productos->onFirstPage())
            <span class="px-3 py-2 text-sm text-gray-400 bg-white rounded-lg shadow cursor-not-allowed">←</span>
        @else
            <a href="{{ $productos->previousPageUrl() }}"
            class="px-3 py-2 text-sm bg-white rounded-lg shadow hover:bg-red-600 hover:text-white transition">←</a>
        @endif

        @foreach($productos->getUrlRange(1, $productos->lastPage()) as $page => $url)
            @if($page == $productos->currentPage())
                <span class="px-3 py-2 text-sm font-bold text-white rounded-lg shadow"
                    style="background-color: #ea0000;">{{ $page }}</span>
            @else
                <a href="{{ $url }}"
                class="px-3 py-2 text-sm bg-white rounded-lg shadow hover:bg-red-600 hover:text-white transition">
                    {{ $page }}
                </a>
            @endif
        @endforeach

        @if($productos->hasMorePages())
            <a href="{{ $productos->nextPageUrl() }}"
            class="px-3 py-2 text-sm bg-white rounded-lg shadow hover:bg-red-600 hover:text-white transition">→</a>
        @else
            <span class="px-3 py-2 text-sm text-gray-400 bg-white rounded-lg shadow cursor-not-allowed">→</span>
        @endif

    </div>
</div>
@endif

@endsection