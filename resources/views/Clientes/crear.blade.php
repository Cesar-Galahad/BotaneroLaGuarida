@extends('layouts.app')

@section('titulo', isset($cliente) ? 'Editar Cliente' : 'Nuevo Cliente')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow p-8">

        <h3 class="text-xl font-bold text-gray-800 mb-6">
            {{ isset($cliente) ? 'Editar cliente' : 'Registrar cliente' }}
        </h3>

        <form method="POST" action="{{ isset($cliente) ? route('clientes.update', $cliente) : route('clientes.store') }}" enctype="multipart/form-data">
            @csrf
            @if(isset($cliente)) @method('PUT') @endif

            {{-- Foto --}}
            <div class="flex justify-center mb-6">
                <label class="cursor-pointer group">
                    <div class="w-24 h-24 rounded-full bg-gray-200 border-4 border-gray-300 flex items-center justify-center overflow-hidden relative group-hover:border-red-400 transition">
                        @if(isset($cliente) && $cliente->imagen)
                            <img id="preview" src="{{ asset('storage/' . $cliente->imagen) }}" class="w-full h-full object-cover">
                        @else
                            <img id="preview" src="" class="w-full h-full object-cover hidden">
                            <svg id="icono" class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                            </svg>
                        @endif
                        <div class="absolute inset-0 bg-black bg-opacity-30 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                            <span class="text-white text-xs font-semibold">Cambiar</span>
                        </div>
                    </div>
                    <input type="file" name="imagen" accept="image/jpg,image/jpeg,image/png"
                        class="hidden" onchange="previewImagen(this, 'preview', 'icono')">
                    <p class="text-center text-xs text-gray-400 mt-2">JPG o PNG, máx. 2MB</p>
                </label>
            </div>

            {{-- Nombre y apellidos --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $cliente->nombre ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500
                                  @error('nombre') border-red-500 @enderror">
                    @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Apellido paterno <span class="text-red-500">*</span></label>
                    <input type="text" name="apellidop" value="{{ old('apellidop', $cliente->apellidop ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500
                                  @error('apellidop') border-red-500 @enderror">
                    @error('apellidop') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Apellido materno</label>
                    <input type="text" name="apellidom" value="{{ old('apellidom', $cliente->apellidom ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
            </div>

            {{-- Teléfono y Estado --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $cliente->telefono ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado <span class="text-red-500">*</span></label>
                    <select name="estado"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="activo"   {{ old('estado', $cliente->estado ?? 'activo') === 'activo'   ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ old('estado', $cliente->estado ?? '') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
            </div>

            {{-- Puntos (solo lectura en edición) --}}
            @if(isset($cliente))
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Puntos acumulados</label>
                <div class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-500">
                    ★ {{ $cliente->puntos }} puntos
                </div>
                <p class="text-xs text-gray-400 mt-1">Los puntos se asignan automáticamente al cerrar pedidos.</p>
            </div>
            @endif

            {{-- Botones --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('clientes.index') }}"
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold px-5 py-2 rounded-lg transition">
                    Cancelar
                </a>
                <button type="submit"
                        onclick="this.disabled=true; this.innerText='Guardando...'; this.form.submit();"
                        class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
                    {{ isset($cliente) ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>

        </form>
    </div>
</div>
<script>
function previewImagen(input, previewId, iconoId) {
    const preview = document.getElementById(previewId);
    const icono   = document.getElementById(iconoId);
    if (input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (icono) icono.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection