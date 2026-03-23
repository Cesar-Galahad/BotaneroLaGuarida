@extends('layouts.app')

@section('titulo', 'Tamaños')

@section('content')

@php $rol = Auth::guard('empleado')->user()->rol->nombre ?? ''; @endphp

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

<div class="flex justify-between items-center mb-6">
    <h3 class="text-xl font-bold text-gray-800">Listado de tamaños</h3>
    @if($rol === 'Administrador')
        <a href="{{ route('tamanios.create') }}"
           class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            + Nuevo tamaño
        </a>
    @endif
</div>

<div class="bg-white rounded-2xl shadow overflow-hidden" x-data>
    <table class="w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-900 text-gray-200 text-xs uppercase">
            <tr>
                <th class="px-4 py-3 w-10">#</th>
                <th class="px-4 py-3">Cantidad</th>
                <th class="px-4 py-3">Unidad</th>
                <th class="px-4 py-3 w-full">Vista previa</th>
                <th class="px-4 py-3">Estado</th>
                @if($rol === 'Administrador')
                    <th class="px-4 py-3 text-center whitespace-nowrap">Acciones</th>
                @else
                    <th class="px-4 py-3"></th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($tamanios as $tamanio)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 text-gray-400">{{ $loop->iteration }}</td>
                <td class="px-4 py-3 font-medium">{{ $tamanio->cantidad }}</td>
                <td class="px-4 py-3">{{ $tamanio->unidad }}</td>
                <td class="px-4 py-3">
                    <span class="bg-gray-100 text-gray-700 text-xs font-semibold px-3 py-1 rounded-full">
                        {{ $tamanio->nombre_completo }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                        {{ $tamanio->estado === 'activo' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ ucfirst($tamanio->estado) }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex justify-center gap-2">
                        @if($rol === 'Administrador')
                            <a href="{{ route('tamanios.edit', $tamanio) }}"
                               class="bg-yellow-400 hover:bg-yellow-500 text-black text-xs font-semibold px-3 py-1 rounded-lg transition">
                                Editar
                            </a>
                            <form method="POST" action="{{ route('tamanios.destroy', $tamanio) }}"
                                id="form-tamanio-{{ $tamanio->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                        @click="$dispatch('abrir-confirm', {
                                            mensaje: '¿Cambiar estado de este tamaño?',
                                            formId: 'form-tamanio-{{ $tamanio->id }}'
                                        })"
                                        class="text-xs font-semibold px-3 py-1 rounded-lg transition
                                            {{ $tamanio->estado === 'activo'
                                                ? 'bg-red-600 hover:bg-red-700 text-white'
                                                : 'bg-green-600 hover:bg-green-700 text-white' }}">
                                    {{ $tamanio->estado === 'activo' ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                    No hay tamaños registrados.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection