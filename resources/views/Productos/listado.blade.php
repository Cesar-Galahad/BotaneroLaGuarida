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

{{-- Grid de cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
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
                    <p class="text-lg font-bold text-red-600">${{ number_format($producto->precio_base, 2) }}</p>
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
                      onsubmit="return confirm('¿Eliminar este producto?')">
                    @php $rol = Auth::guard('empleado')->user()->rol->nombre ?? ''; @endphp

                    @if($rol === 'Administrador')
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                        Eliminar
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

@endsection