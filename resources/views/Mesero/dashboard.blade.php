@extends('layouts.app')

@section('titulo', 'Mi turno')

@section('content')

<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">

    <div class="bg-white rounded-2xl shadow p-6 flex items-center gap-4">
        <div class="w-14 h-14 bg-yellow-100 rounded-xl flex items-center justify-center text-2xl">🧾</div>
        <div>
            <p class="text-xs text-gray-400 uppercase font-semibold">Pedidos abiertos</p>
            <p class="text-2xl font-bold text-gray-800">{{ $pedidosAbiertos }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-6 flex items-center gap-4">
        <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center text-2xl">🔴</div>
        <div>
            <p class="text-xs text-gray-400 uppercase font-semibold">Mesas ocupadas</p>
            <p class="text-2xl font-bold text-gray-800">{{ $mesasOcupadas }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-6 flex items-center gap-4">
        <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center text-2xl">🟢</div>
        <div>
            <p class="text-xs text-gray-400 uppercase font-semibold">Mesas libres</p>
            <p class="text-2xl font-bold text-gray-800">{{ $mesasLibres }}</p>
        </div>
    </div>

</div>

<div class="flex gap-4">
    <a href="{{ route('mesas.index') }}"
       class="bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-3 rounded-xl transition">
        Ver Mesas
    </a>
    <a href="{{ route('pedidos.index') }}"
       class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-6 py-3 rounded-xl transition">
        Pedidos abiertos
    </a>
</div>

@endsection