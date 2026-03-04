@extends('layouts.app')

@section('titulo', 'Promociones')

@section('content')

@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded-lg">
        {{ session('success') }}
    </div>
@endif

<div class="flex justify-between items-center mb-6">
    <h3 class="text-xl font-bold text-gray-800">Listado de promociones</h3>
    <a href="{{ route('promociones.create') }}"
       class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
        + Nueva promoción
    </a>
</div>

<div class="bg-white rounded-2xl shadow overflow-hidden">
    <table class="w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-900 text-gray-200 text-xs uppercase">
            <tr>
                <th class="px-4 py-3">#</th>
                <th class="px-4 py-3">Nombre</th>
                <th class="px-4 py-3">Tipo</th>
                <th class="px-4 py-3">Valor</th>
                <th class="px-4 py-3">Vigencia</th>
                <th class="px-4 py-3">Estado</th>
                <th class="px-4 py-3 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($promociones as $promocion)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 text-gray-400">{{ $loop->iteration }}</td>
                <td class="px-4 py-3 font-medium">{{ $promocion->nombre_p }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                        {{ $promocion->tipo === 'porcentaje' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                        {{ ucfirst($promocion->tipo) }}
                    </span>
                </td>
                <td class="px-4 py-3 font-bold">
                    {{ $promocion->tipo === 'porcentaje' ? $promocion->valor . '%' : '$' . number_format($promocion->valor, 2) }}
                </td>
                <td class="px-4 py-3 text-xs text-gray-500">
                    {{ $promocion->fecha_inicio->format('d/m/Y') }} — {{ $promocion->fecha_fin->format('d/m/Y') }}
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                        {{ $promocion->estado === 'activa' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ ucfirst($promocion->estado) }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('promociones.edit', $promocion) }}"
                           class="bg-yellow-400 hover:bg-yellow-500 text-black text-xs font-semibold px-3 py-1 rounded-lg transition">
                            Editar
                        </a>
                        <form method="POST" action="{{ route('promociones.destroy', $promocion) }}"
                              onsubmit="return confirm('¿Eliminar esta promoción?')">
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
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                    No hay promociones registradas.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection