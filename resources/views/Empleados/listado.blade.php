@extends('layouts.app')

@section('titulo', 'Empleados')

@section('content')

{{-- Alerta de éxito --}}
@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded-lg">
        {{ session('success') }}
    </div>
@endif

{{-- Encabezado --}}
<div class="flex justify-between items-center mb-6">
    <h3 class="text-xl font-bold text-gray-800">Listado de empleados</h3>
    <a href="{{ route('empleados.create') }}"
       class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
        + Nuevo empleado
    </a>
</div>

{{-- Tabla --}}
<div class="bg-white rounded-2xl shadow overflow-hidden">
    <table class="w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-900 text-gray-200 text-xs uppercase">
            <tr>
                <th class="px-4 py-3">#</th>
                <th class="px-4 py-3">Foto</th>
                <th class="px-4 py-3">Nombre</th>
                <th class="px-4 py-3">Correo</th>
                <th class="px-4 py-3">Rol</th>
                <th class="px-4 py-3">Estado</th>
                <th class="px-4 py-3 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($empleados as $empleado)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 text-gray-400">{{ $loop->iteration }}</td>

                {{-- Foto (placeholder por ahora) --}}
                <td class="px-4 py-3">
                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                        @if($empleado->imagen)
                            <img src="{{ asset('storage/' . $empleado->imagen) }}"
                                 class="w-full h-full object-cover">
                        @else
                            <span class="text-gray-400 text-xs font-bold">
                                {{ strtoupper(substr($empleado->nombre, 0, 1)) }}{{ strtoupper(substr($empleado->apellidop, 0, 1)) }}
                            </span>
                        @endif
                    </div>
                </td>

                <td class="px-4 py-3 font-medium">
                    {{ $empleado->nombre }} {{ $empleado->apellidop }} {{ $empleado->apellidom }}
                </td>
                <td class="px-4 py-3">{{ $empleado->correo }}</td>
                <td class="px-4 py-3">{{ $empleado->rol->nombre ?? '—' }}</td>

                {{-- Badge estado --}}
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                        {{ $empleado->estado === 'activo'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-red-100 text-red-700' }}">
                        {{ ucfirst($empleado->estado) }}
                    </span>
                </td>

                {{-- Acciones --}}
                <td class="px-4 py-3">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('empleados.edit', $empleado) }}"
                           class="bg-yellow-400 hover:bg-yellow-500 text-black text-xs font-semibold px-3 py-1 rounded-lg transition">
                            Editar
                        </a>

                        <form method="POST" action="{{ route('empleados.destroy', $empleado) }}"
                              onsubmit="return confirm('¿Dar de baja a este empleado?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-3 py-1 rounded-lg transition">
                                Baja
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                    No hay empleados registrados.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection