@extends('layouts.app')

@section('titulo', isset($promocion) ? 'Editar Promoción' : 'Nueva Promoción')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow p-8">

        <h3 class="text-xl font-bold text-gray-800 mb-6">
            {{ isset($promocion) ? 'Editar promoción' : 'Registrar promoción' }}
        </h3>

        <form method="POST"
              action="{{ isset($promocion) ? route('promociones.update', $promocion) : route('promociones.store') }}">
            @csrf
            @if(isset($promocion)) @method('PUT') @endif

            {{-- Nombre --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nombre <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nombre_p"
                       value="{{ old('nombre_p', $promocion->nombre_p ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500
                              @error('nombre_p') border-red-500 @enderror">
                @error('nombre_p') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tipo y Valor --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tipo <span class="text-red-500">*</span>
                    </label>
                    <select name="tipo"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500
                                   @error('tipo') border-red-500 @enderror">
                        <option value="">-- Selecciona --</option>
                        <option value="porcentaje" {{ old('tipo', $promocion->tipo ?? '') === 'porcentaje' ? 'selected' : '' }}>Porcentaje (%)</option>
                        <option value="monto"      {{ old('tipo', $promocion->tipo ?? '') === 'monto'      ? 'selected' : '' }}>Monto fijo ($)</option>
                    </select>
                    @error('tipo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Valor <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="valor" step="0.01" min="0"
                           value="{{ old('valor', $promocion->valor ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500
                                  @error('valor') border-red-500 @enderror">
                    @error('valor') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Fechas --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Fecha inicio <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="fecha_inicio"
                           value="{{ old('fecha_inicio', isset($promocion) ? $promocion->fecha_inicio->format('Y-m-d') : '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500
                                  @error('fecha_inicio') border-red-500 @enderror">
                    @error('fecha_inicio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Fecha fin <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="fecha_fin"
                           value="{{ old('fecha_fin', isset($promocion) ? $promocion->fecha_fin->format('Y-m-d') : '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500
                                  @error('fecha_fin') border-red-500 @enderror">
                    @error('fecha_fin') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Estado --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado <span class="text-red-500">*</span></label>
                <select name="estado"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="activa"   {{ old('estado', $promocion->estado ?? 'activa') === 'activa'   ? 'selected' : '' }}>Activa</option>
                    <option value="inactiva" {{ old('estado', $promocion->estado ?? '') === 'inactiva' ? 'selected' : '' }}>Inactiva</option>
                </select>
            </div>

            {{-- Productos --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Productos aplicables
                    <span class="text-gray-400 font-normal">(selecciona uno o varios)</span>
                </label>
                <div class="border border-gray-300 rounded-lg p-3 max-h-48 overflow-y-auto space-y-2">
                    @foreach($productos as $producto)
                    <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 rounded p-1">
                        <input type="checkbox" name="productos[]" value="{{ $producto->id }}"
                               class="w-4 h-4 accent-red-600"
                               {{ in_array($producto->id, old('productos', $productosAsignados ?? [])) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">{{ $producto->nombre }}</span>
                        <span class="ml-auto text-xs text-gray-400">${{ number_format($producto->precio_base, 2) }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('promociones.index') }}"
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold px-5 py-2 rounded-lg transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
                    {{ isset($promocion) ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>

        </form>
    </div>
</div>

@endsection