@extends('layouts.app')

@section('titulo', 'Mesas')

@section('content')

@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded-lg">
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-4 gap-6">
    @foreach($mesas as $mesa)
        @php
            $color = match($mesa->estado) {
                'libre'   => 'bg-green-100 text-green-700 border-green-300',
                'ocupada' => 'bg-red-100 text-red-700 border-red-300',
                default   => 'bg-gray-100 text-gray-700 border-gray-300',
            };
            $icono = $mesa->estado === 'libre' ? '🟢' : '🔴';
        @endphp

        <a href="{{ route('mesas.seleccionar', $mesa) }}"
           class="bg-white shadow-lg rounded-2xl p-6 flex flex-col items-center justify-center
                  hover:scale-105 transition transform border-2 {{ $color }}">

            <div class="w-16 h-16 bg-gray-100 rounded-full mb-4 flex items-center justify-center text-3xl">
                🪑
            </div>

            <h3 class="text-xl font-bold text-gray-700">
                Mesa {{ $mesa->numero }}
            </h3>

            <p class="text-xs text-gray-400 mb-2">Cap. {{ $mesa->capacidad }} personas</p>

            <span class="mt-1 px-3 py-1 text-sm rounded-full font-semibold {{ $color }}">
                {{ ucfirst($mesa->estado) }}
            </span>

            @if($mesa->pedidoAbierto)
                <p class="text-xs text-gray-400 mt-2">
                    Desde {{ $mesa->pedidoAbierto->fecha->format('H:i') }}
                </p>
            @endif

        </a>
    @endforeach
</div>

@endsection