@extends('layouts.app')

@section('titulo', 'Dashboard')

@section('content')

{{-- Tarjetas resumen --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

    {{-- Total vendido hoy --}}
    <div class="bg-white rounded-2xl shadow p-6 flex items-center gap-4">
        <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center text-2xl">
            💰
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase font-semibold">Vendido hoy</p>
            <p class="text-2xl font-bold text-gray-800">${{ number_format($totalHoy, 2) }}</p>
        </div>
    </div>

    {{-- Pedidos abiertos --}}
    <div class="bg-white rounded-2xl shadow p-6 flex items-center gap-4">
        <div class="w-14 h-14 bg-yellow-100 rounded-xl flex items-center justify-center text-2xl">
            🧾
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase font-semibold">Pedidos abiertos</p>
            <p class="text-2xl font-bold text-gray-800">{{ $pedidosAbiertos }}</p>
        </div>
    </div>

    {{-- Mesas ocupadas --}}
    <div class="bg-white rounded-2xl shadow p-6 flex items-center gap-4">
        <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center text-2xl">
            🔴
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase font-semibold">Mesas ocupadas</p>
            <p class="text-2xl font-bold text-gray-800">{{ $mesasOcupadas }}</p>
        </div>
    </div>

    {{-- Mesas libres --}}
    <div class="bg-white rounded-2xl shadow p-6 flex items-center gap-4">
        <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center text-2xl">
            🟢
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase font-semibold">Mesas libres</p>
            <p class="text-2xl font-bold text-gray-800">{{ $mesasLibres }}</p>
        </div>
    </div>

</div>

{{-- Productos con pocas existencias --}}
<div class="bg-white rounded-2xl shadow overflow-hidden">

    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
        <span class="text-lg"></span>
        <h3 class="font-bold text-gray-800">Productos con pocas existencias</h3>
        <span class="ml-auto text-xs text-gray-400">10 unidades o menos</span>
    </div>

    <table class="w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-50 text-xs uppercase text-gray-400">
            <tr>
                <th class="px-6 py-3">Producto</th>
                <th class="px-6 py-3">Categoría</th>
                <th class="px-6 py-3">Precio</th>
                <th class="px-6 py-3">Existencia</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($productosBajos as $producto)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-3 font-medium">{{ $producto->nombre }}</td>
                <td class="px-6 py-3 text-gray-400">{{ $producto->categoria->nombre ?? '—' }}</td>
                <td class="px-6 py-3">${{ number_format($producto->precio_base, 2) }}</td>
                <td class="px-6 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                        {{ $producto->existencia <= 3
                            ? 'bg-red-100 text-red-700'
                            : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $producto->existencia }} unidades
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                    Todos los productos tienen existencia suficiente.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

</div>

@endsection