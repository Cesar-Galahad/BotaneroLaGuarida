@extends('layouts.app')

@section('titulo', 'Órdenes en cocina')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @forelse($pedidos as $pedido)
    <div class="bg-white rounded-2xl shadow p-5">

        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Mesa {{ $pedido->mesa->numero }}</h3>
            <span class="text-xs text-gray-400">{{ $pedido->fecha->format('H:i') }}</span>
        </div>

        <div class="space-y-2">
            @foreach($pedido->detalles as $detalle)
            <div class="flex justify-between items-center bg-gray-50 rounded-lg px-3 py-2">
                <span class="text-sm font-medium text-gray-700">{{ $detalle->producto->nombre }}</span>
                <span class="bg-red-600 text-white text-xs font-bold w-7 h-7 flex items-center justify-center rounded-full">
                    {{ $detalle->cantidad }}
                </span>
            </div>
            @endforeach
        </div>

    </div>
    @empty
    <div class="col-span-3 text-center py-16 text-gray-400">
        Sin órdenes pendientes por ahora.
    </div>
    @endforelse
</div>

@endsection