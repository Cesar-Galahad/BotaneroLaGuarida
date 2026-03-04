@extends('layouts.app')

@section('titulo', 'Categorías')

@section('content')

@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded-lg">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-800 rounded-lg">
        {{ session('error') }}
    </div>
@endif

{{-- Encabezado --}}
<div class="flex justify-between items-center mb-6">
    <h3 class="text-xl font-bold text-gray-800">Listado de categorías</h3>
    <a href="{{ route('categorias.create') }}"
       class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
        + Nueva categoría
    </a>
</div>

{{-- Tabla --}}
<div class="bg-white rounded-2xl shadow overflow-hidden">
    <table class="w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-900 text-gray-200 text-xs uppercase">
            <tr>
                <th class="px-4 py-3">#</th>
                <th class="px-4 py-3">Imagen</th>
                <th class="px-4 py-3">Nombre</th>
                <th class="px-4 py-3 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($categorias as $categoria)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 text-gray-400">{{ $loop->iteration }}</td>

                {{-- Imagen --}}
                <td class="px-4 py-3">
                    <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden">
                        @if($categoria->imagen)
                            <img src="{{ asset('storage/' . $categoria->imagen) }}"
                                 class="w-full h-full object-cover">
                        @else
                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 3h18M3 3v18M3 3l18 18"/>
                            </svg>
                        @endif
                    </div>
                </td>

                <td class="px-4 py-3 font-medium">{{ $categoria->nombre }}</td>


                <td class="px-4 py-3">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('categorias.edit', $categoria) }}"
                           class="bg-yellow-400 hover:bg-yellow-500 text-black text-xs font-semibold px-3 py-1 rounded-lg transition">
                            Editar
                        </a>
                        <form method="POST" action="{{ route('categorias.destroy', $categoria) }}"
                              onsubmit="return confirm('¿Eliminar esta categoría?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-3 py-1 rounded-lg transition">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-4 py-8 text-center text-gray-400">
                    No hay categorías registradas.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection