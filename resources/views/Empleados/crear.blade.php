@extends('layouts.app')  {{-- ajusta esto según tu ruta --}}

@section('titulo', isset($empleado) ? 'Editar Empleado' : 'Nuevo Empleado')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow p-8">

        <h3 class="text-xl font-bold text-gray-800 mb-6">
            {{ isset($empleado) ? 'Editar empleado' : 'Registrar empleado' }}
        </h3>

        <form method="POST" action="{{ isset($empleado) ? route('empleados.update', $empleado) : route('empleados.store') }}" enctype="multipart/form-data">
            @csrf
            @if(isset($empleado)) @method('PUT') @endif

            {{-- Foto --}}
            <div class="flex justify-center mb-6">
                <label class="cursor-pointer group">
                    <div class="w-24 h-24 rounded-full bg-gray-200 border-4 border-gray-300 flex items-center justify-center overflow-hidden relative group-hover:border-red-400 transition">
                        {{-- Preview si ya tiene imagen --}}
                        @if(isset($empleado) && $empleado->imagen)
                            <img id="preview"
                                src="{{ asset('storage/' . $empleado->imagen) }}"
                                class="w-full h-full object-cover">
                        @else
                            <img id="preview" src="" class="w-full h-full object-cover hidden">
                            <svg id="icono" class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                            </svg>
                        @endif

                        {{-- Overlay hover --}}
                        <div class="absolute inset-0 bg-black bg-opacity-30 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                            <span class="text-white text-xs font-semibold">Cambiar</span>
                        </div>
                    </div>
                    <input type="file" name="imagen" accept="image/jpg,image/jpeg,image/png"
                        class="hidden" onchange="previewImagen(this, 'preview', 'icono')">
                    <p class="text-center text-xs text-gray-400 mt-2">JPG o PNG</p>
                </label>
            </div>

            {{-- Nombre y apellidos --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $empleado->nombre ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 @error('nombre') border-red-500 @enderror">
                    @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Apellido paterno <span class="text-red-500">*</span></label>
                    <input type="text" name="apellidop" value="{{ old('apellidop', $empleado->apellidop ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 @error('apellidop') border-red-500 @enderror">
                    @error('apellidop') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Apellido materno</label>
                    <input type="text" name="apellidom" value="{{ old('apellidom', $empleado->apellidom ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
            </div>

            {{-- Correo --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico <span class="text-red-500">*</span></label>
                <input type="email" name="correo" value="{{ old('correo', $empleado->correo ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 @error('correo') border-red-500 @enderror">
                @error('correo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Contraseña --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ isset($empleado) ? 'Nueva contraseña' : 'Contraseña' }}
                    @if(isset($empleado))
                        <span class="text-gray-400 font-normal">(dejar vacío para no cambiar)</span>
                    @else
                        <span class="text-red-500">*</span>
                    @endif
                </label>
                <input type="password" name="contrasena"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 @error('contrasena') border-red-500 @enderror">
                @error('contrasena') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Rol y Estado --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rol <span class="text-red-500">*</span></label>
                    <select name="rol_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 @error('rol_id') border-red-500 @enderror">
                        <option value="">-- Selecciona --</option>
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id }}"
                                {{ old('rol_id', $empleado->rol_id ?? '') == $rol->id ? 'selected' : '' }}>
                                {{ $rol->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('rol_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado <span class="text-red-500">*</span></label>
                    <select name="estado"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="activo"   {{ old('estado', $empleado->estado ?? 'activo') === 'activo'   ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ old('estado', $empleado->estado ?? '')       === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('empleados.index') }}"
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold px-5 py-2 rounded-lg transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
                    {{ isset($empleado) ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>

        </form>
    </div>
</div>
<script>
function previewImagen(input, previewId, iconoId) {
    const preview = document.getElementById(previewId);
    const icono   = document.getElementById(iconoId);
    const file    = input.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (icono) icono.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endsection