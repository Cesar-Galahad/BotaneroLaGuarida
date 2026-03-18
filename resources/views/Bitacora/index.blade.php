@extends('layouts.app')

@section('titulo', 'Bitácora del Sistema')

@section('content')

<div class="mb-6">
    <h3 class="text-xl font-bold text-gray-800">Bitácora del Sistema</h3>
    <p class="text-sm text-gray-500 mt-1">Historial de operaciones registradas automáticamente</p>
</div>

{{-- Pestañas --}}
<div class="flex gap-2 mb-6 border-b border-gray-200">
    <a href="{{ route('bitacora.index', array_merge(request()->except(['tab','page_prod','page_ped']), ['tab' => 'productos'])) }}"
       class="px-5 py-2.5 text-sm font-medium rounded-t-lg transition
              {{ $tab === 'productos'
                  ? 'bg-white border border-b-white border-gray-200 text-red-600 -mb-px'
                  : 'text-gray-500 hover:text-gray-700' }}">
        📦 Productos
    </a>
    <a href="{{ route('bitacora.index', array_merge(request()->except(['tab','page_prod','page_ped']), ['tab' => 'pedidos'])) }}"
       class="px-5 py-2.5 text-sm font-medium rounded-t-lg transition
              {{ $tab === 'pedidos'
                  ? 'bg-white border border-b-white border-gray-200 text-red-600 -mb-px'
                  : 'text-gray-500 hover:text-gray-700' }}">
        🧾 Pedidos
    </a>
</div>

{{-- Filtros --}}
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('bitacora.index') }}" class="flex flex-wrap gap-4 items-end">
        <input type="hidden" name="tab" value="{{ $tab }}">

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Operación</label>
            <select name="operacion"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400">
                <option value="">Todas</option>
                <option value="INSERT" {{ request('operacion') === 'INSERT' ? 'selected' : '' }}>INSERT</option>
                <option value="UPDATE" {{ request('operacion') === 'UPDATE' ? 'selected' : '' }}>UPDATE</option>
                <option value="DELETE" {{ request('operacion') === 'DELETE' ? 'selected' : '' }}>DELETE</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Fecha inicio</label>
            <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}"
                   class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Fecha fin</label>
            <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}"
                   class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400">
        </div>

        <div class="flex gap-2">
            <button type="submit"
                    class="bg-red-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-red-700 transition">
                Filtrar
            </button>
            <a href="{{ route('bitacora.index', ['tab' => $tab]) }}"
               class="bg-gray-100 text-gray-700 text-sm px-4 py-2 rounded-lg hover:bg-gray-200 transition">
                Limpiar
            </a>
        </div>
    </form>
</div>

{{-- ── PESTAÑA PRODUCTOS ── --}}
@if($tab === 'productos')
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <span class="text-sm font-semibold text-gray-700">Movimientos en Productos</span>
        <span class="text-xs text-gray-400">{{ $registrosProductos->total() }} registros</span>
    </div>

    @if($registrosProductos->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <p class="text-4xl mb-3">📭</p>
            <p class="text-sm">Sin registros en esta sección</p>
        </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500 tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">#</th>
                    <th class="px-6 py-3 text-left">Operación</th>
                    <th class="px-6 py-3 text-left">ID Registro</th>
                    <th class="px-6 py-3 text-left">Empleado</th>
                    <th class="px-6 py-3 text-left">Descripción</th>
                    <th class="px-6 py-3 text-left">Fecha</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($registrosProductos as $reg)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-400">{{ $reg->id }}</td>
                    <td class="px-6 py-4">
                        @if($reg->operacion === 'INSERT')
                            <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                ＋ INSERT
                            </span>
                        @elseif($reg->operacion === 'UPDATE')
                            <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                ✎ UPDATE
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                ✕ DELETE
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $reg->id_registro }}</td>
                    <td class="px-6 py-4 text-gray-700">
                        {{ trim($reg->empleado_nombre) ?: '—' }}
                    </td>
                    <td class="px-6 py-4 text-gray-600 max-w-xs truncate" title="{{ $reg->descripcion }}">
                        {{ $reg->descripcion }}
                    </td>
                    <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($reg->fecha)->format('d/m/Y H:i') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if($registrosProductos->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $registrosProductos->links() }}
    </div>
    @endif
    @endif
</div>
@endif

{{-- ── PESTAÑA PEDIDOS ── --}}
@if($tab === 'pedidos')
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <span class="text-sm font-semibold text-gray-700">Movimientos en Pedidos</span>
        <span class="text-xs text-gray-400">{{ $registrosPedidos->total() }} registros</span>
    </div>

    @if($registrosPedidos->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <p class="text-4xl mb-3">📭</p>
            <p class="text-sm">Sin registros en esta sección</p>
        </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500 tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">#</th>
                    <th class="px-6 py-3 text-left">Operación</th>
                    <th class="px-6 py-3 text-left">ID Pedido</th>
                    <th class="px-6 py-3 text-left">Empleado</th>
                    <th class="px-6 py-3 text-left">Descripción</th>
                    <th class="px-6 py-3 text-left">Fecha</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($registrosPedidos as $reg)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-400">{{ $reg->id }}</td>
                    <td class="px-6 py-4">
                        @if($reg->operacion === 'INSERT')
                            <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                ＋ INSERT
                            </span>
                        @elseif($reg->operacion === 'UPDATE')
                            <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                ✎ UPDATE
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                ✕ DELETE
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $reg->id_registro }}</td>
                    <td class="px-6 py-4 text-gray-700">
                        {{ trim($reg->empleado_nombre) ?: '—' }}
                    </td>
                    <td class="px-6 py-4 text-gray-600 max-w-xs truncate" title="{{ $reg->descripcion }}">
                        {{ $reg->descripcion }}
                    </td>
                    <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($reg->fecha)->format('d/m/Y H:i') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if($registrosPedidos->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $registrosPedidos->links() }}
    </div>
    @endif
    @endif
</div>
@endif

@endsection
