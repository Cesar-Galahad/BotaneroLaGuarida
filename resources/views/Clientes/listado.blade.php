@extends('layouts.app')

@section('titulo', 'Clientes')

@section('content')
@php $rol = Auth::guard('empleado')->user()->rol->nombre ?? ''; @endphp

@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded-lg">
        {{ session('success') }}
    </div>
@endif

{{-- Encabezado --}}
<div class="flex justify-between items-center mb-6">
    <h3 class="text-xl font-bold text-gray-800">Listado de clientes</h3>
    @if($rol === 'Administrador')
        <a href="{{ route('clientes.create') }}"
        class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            + Nuevo cliente
        </a>
    @endif
</div>
{{-- Buscador --}}
<form method="GET" action="{{ route('clientes.index') }}"
      class="bg-white rounded-2xl shadow p-4 mb-6 flex flex-wrap gap-3 items-end">

    <div class="flex-1 min-w-48">
        <label class="block text-xs font-medium text-gray-600 mb-1">Buscar</label>
        <input type="text" name="buscar" value="{{ request('buscar') }}"
               placeholder="Nombre, apellido o teléfono..."
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
    </div>

    @php $rol = Auth::guard('empleado')->user()->rol->nombre ?? ''; @endphp
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
            Buscar
        </button>
        <a href="{{ route('clientes.index') }}"
           class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg transition">
            Limpiar
        </a>
    </div>

</form>
{{-- Tabla --}}
<div class="bg-white rounded-2xl shadow overflow-hidden" x-data>
    <table class="w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-900 text-gray-200 text-xs uppercase">
            <tr>
                <th class="px-4 py-3">#</th>
                <th class="px-4 py-3">Foto</th>
                <th class="px-4 py-3">Nombre</th>
                <th class="px-4 py-3">Teléfono</th>
                <th class="px-4 py-3">Puntos</th>
                <th class="px-4 py-3">Estado</th>
                @if($rol === 'Administrador')
                    <th class="px-4 py-3 text-center">Acciones</th>
                @else
                    <th class="px-4 py-3"></th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($clientes as $cliente)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 text-gray-400">{{ $loop->iteration }}</td>

                <td class="px-4 py-3">
                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                        @if($cliente->imagen)
                            <img src="{{ asset('storage/' . $cliente->imagen) }}"
                                 class="w-full h-full object-cover">
                        @else
                            <span class="text-gray-400 text-xs font-bold">
                                {{ strtoupper(substr($cliente->nombre, 0, 1)) }}{{ strtoupper(substr($cliente->apellidop, 0, 1)) }}
                            </span>
                        @endif
                    </div>
                </td>

                <td class="px-4 py-3 font-medium">
                    {{ $cliente->nombre }} {{ $cliente->apellidop }} {{ $cliente->apellidom }}
                </td>

                <td class="px-4 py-3">{{ $cliente->telefono ?? '—' }}</td>

                <td class="px-4 py-3">
                    <span class="bg-yellow-100 text-yellow-700 text-xs font-semibold px-2 py-1 rounded-full">
                        ★ {{ $cliente->puntos }}
                    </span>
                </td>

                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                        {{ $cliente->estado === 'activo' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ ucfirst($cliente->estado) }}
                    </span>
                </td>

                <td class="px-4 py-3">
                    <div class="flex justify-center gap-2">
                        @if($rol === 'Administrador')
                        <a href="{{ route('clientes.edit', $cliente) }}"
                           class="bg-yellow-400 hover:bg-yellow-500 text-black text-xs font-semibold px-3 py-1 rounded-lg transition">
                            Editar
                        </a>
                        <form method="POST" action="{{ route('clientes.destroy', $cliente) }}"
                              id="form-cliente-{{ $cliente->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button"
                                    @click="$dispatch('abrir-confirm', {
                                        mensaje: '¿Cambiar estado de este cliente?',
                                        formId: 'form-cliente-{{ $cliente->id }}'
                                    })"
                                    class="text-xs font-semibold px-3 py-1 rounded-lg transition
                                        {{ $cliente->estado === 'activo'
                                            ? 'bg-red-600 hover:bg-red-700 text-white'
                                            : 'bg-green-600 hover:bg-green-700 text-white' }}">
                                {{ $cliente->estado === 'activo' ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                    No hay clientes registrados.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($clientes->hasPages())
<div class="mt-6 flex justify-center">
    <div class="flex items-center gap-1">
        @if($clientes->onFirstPage())
            <span class="px-3 py-2 text-sm text-gray-400 bg-white rounded-lg shadow cursor-not-allowed">←</span>
        @else
            <a href="{{ $clientes->previousPageUrl() }}"
               class="px-3 py-2 text-sm bg-white rounded-lg shadow hover:bg-red-600 hover:text-white transition">←</a>
        @endif

        @foreach($clientes->getUrlRange(1, $clientes->lastPage()) as $page => $url)
            @if($page == $clientes->currentPage())
                <span class="px-3 py-2 text-sm font-bold text-white rounded-lg shadow"
                      style="background-color: #ea0000;">{{ $page }}</span>
            @else
                <a href="{{ $url }}"
                   class="px-3 py-2 text-sm bg-white rounded-lg shadow hover:bg-red-600 hover:text-white transition">
                    {{ $page }}
                </a>
            @endif
        @endforeach

        @if($clientes->hasMorePages())
            <a href="{{ $clientes->nextPageUrl() }}"
               class="px-3 py-2 text-sm bg-white rounded-lg shadow hover:bg-red-600 hover:text-white transition">→</a>
        @else
            <span class="px-3 py-2 text-sm text-gray-400 bg-white rounded-lg shadow cursor-not-allowed">→</span>
        @endif
    </div>
</div>
@endif
@endsection