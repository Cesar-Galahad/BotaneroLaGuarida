@extends('layouts.app')

@section('titulo', 'Productos Canjeables')

@section('content')

@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded-lg">
        {{ session('success') }}
    </div>
@endif

<div class="flex justify-between items-center mb-6">
    <h3 class="text-xl font-bold text-gray-800">Catálogo de canje</h3>
    <a href="{{ route('canjes.create') }}"
       class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
        + Nuevo canje
    </a>
</div>

<div class="bg-white rounded-2xl shadow overflow-hidden" x-data>
    <table class="w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-900 text-gray-200 text-xs uppercase">
            <tr>
                <th class="px-4 py-3">#</th>
                <th class="px-4 py-3">Producto</th>
                <th class="px-4 py-3">Tamaño</th>
                <th class="px-4 py-3">Categoría</th>
                <th class="px-4 py-3 text-center">Puntos</th>
                <th class="px-4 py-3">Estado</th>
                <th class="px-4 py-3 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($canjes as $canje)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 text-gray-400">{{ $loop->iteration }}</td>
                <td class="px-4 py-3 font-medium">{{ $canje->producto->nombre }}</td>
                <td class="px-4 py-3">
                    @if($canje->tamanio)
                        @if(in_array($canje->tamanio->unidad, ['vaso', 'litro']))
                            {{ ucfirst($canje->tamanio->unidad) }}
                        @else
                            {{ $canje->tamanio->cantidad }} {{ $canje->tamanio->unidad }}
                        @endif
                    @else
                        —
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-400">{{ $canje->producto->categoria->nombre ?? '—' }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="bg-yellow-100 text-yellow-700 text-xs font-semibold px-3 py-1 rounded-full">
                        ★ {{ $canje->puntos_costo }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                        {{ $canje->estado === 'activo' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ ucfirst($canje->estado) }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('canjes.edit', $canje) }}"
                           class="bg-yellow-400 hover:bg-yellow-500 text-black text-xs font-semibold px-3 py-1 rounded-lg transition">
                            Editar
                        </a>
                        <form method="POST" action="{{ route('canjes.destroy', $canje) }}"
                              id="form-canje-{{ $canje->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button"
                                    @click="$dispatch('abrir-confirm', {
                                        mensaje: '¿Cambiar estado de este producto de canje?',
                                        formId: 'form-canje-{{ $canje->id }}'
                                    })"
                                    class="text-xs font-semibold px-3 py-1 rounded-lg transition
                                        {{ $canje->estado === 'activo'
                                            ? 'bg-red-600 hover:bg-red-700 text-white'
                                            : 'bg-green-600 hover:bg-green-700 text-white' }}">
                                {{ $canje->estado === 'activo' ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                    No hay productos de canje registrados.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection