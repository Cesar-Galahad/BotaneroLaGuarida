@extends('layouts.app')

@section('titulo', isset($tamanio) ? 'Editar Tamaño' : 'Nuevo Tamaño')

@section('content')

<div class="max-w-md mx-auto">
    <div class="bg-white rounded-2xl shadow p-8">

        <h3 class="text-xl font-bold text-gray-800 mb-6">
            {{ isset($tamanio) ? 'Editar tamaño' : 'Registrar tamaño' }}
        </h3>

        <form method="POST"
              action="{{ isset($tamanio) ? route('tamanios.update', $tamanio) : route('tamanios.store') }}">
            @csrf
            @if(isset($tamanio)) @method('PUT') @endif

            {{-- Cantidad --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Cantidad <span class="text-red-500">*</span>
                    <span class="text-gray-400 font-normal text-xs ml-1">(solo números, máx. 999)</span>
                </label>
                <input type="number" name="cantidad" min="1" max="999"
                       value="{{ old('cantidad', $tamanio->cantidad ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500
                              @error('cantidad') border-red-500 @enderror">
                @error('cantidad') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Unidad --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Unidad <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach(['pz' => 'Piezas', 'litro' => 'Litro', 'vaso' => 'Vaso'] as $valor => $etiqueta)
                    <label class="cursor-pointer">
                        <input type="radio" name="unidad" value="{{ $valor }}"
                               class="hidden peer"
                               {{ old('unidad', $tamanio->unidad ?? '') === $valor ? 'checked' : '' }}>
                        <div class="peer-checked:bg-red-600 peer-checked:text-white peer-checked:border-red-600
                                    border-2 border-gray-200 rounded-xl p-3 text-center hover:border-red-300 transition">
                            <p class="text-sm font-semibold">{{ $etiqueta }}</p>
                            <p class="text-xs text-gray-400 peer-checked:text-red-200">{{ $valor }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('unidad') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Botones --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('tamanios.index') }}"
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold px-5 py-2 rounded-lg transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
                    {{ isset($tamanio) ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>

        </form>
    </div>
</div>

@endsection