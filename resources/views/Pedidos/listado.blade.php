@extends('layouts.app')

@section('titulo', 'Pedidos Abiertos')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h3 class="text-xl font-bold text-gray-800">Pedidos abiertos</h3>
</div>

<div class="bg-white rounded-2xl shadow overflow-hidden">
    <table class="w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-900 text-gray-200 text-xs uppercase">
            <tr>
                <th class="px-4 py-3">#</th>
                <th class="px-4 py-3">Mesa</th>
                <th class="px-4 py-3">Empleado</th>
                <th class="px-4 py-3">Hora</th>
                <th class="px-4 py-3">Productos</th>
                <th class="px-4 py-3">Total</th>
                <th class="px-4 py-3 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($pedidos as $pedido)
            @php
                $total = $pedido->detalles->sum(fn($d) => ($d->precio_unitario - $d->descuento_aplicado) * $d->cantidad);
            @endphp
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 text-gray-400">{{ $pedido->id }}</td>
                <td class="px-4 py-3 font-bold">Mesa {{ $pedido->mesa->numero }}</td>
                <td class="px-4 py-3">{{ $pedido->empleado->nombre }} {{ $pedido->empleado->apellidop }}</td>
                <td class="px-4 py-3">{{ $pedido->fecha->format('H:i') }}</td>
                <td class="px-4 py-3">{{ $pedido->detalles->count() }} productos</td>
                <td class="px-4 py-3 font-bold text-red-600">${{ number_format($total, 2) }}</td>
                <td class="px-4 py-3">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('pos', $pedido) }}"
                           class="bg-yellow-400 hover:bg-yellow-500 text-black text-xs font-semibold px-3 py-1 rounded-lg transition">
                            Ver POS
                        </a>
                        <form method="POST" action="{{ route('pedidos.cancelar', $pedido) }}"
                              onsubmit="return confirm('¿Cancelar este pedido?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-3 py-1 rounded-lg transition">
                                Cancelar
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                    No hay pedidos abiertos.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection