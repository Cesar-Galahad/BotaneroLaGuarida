@extends('layouts.app')

@section('titulo', 'Mi perfil')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow p-8">

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        {{-- Foto y datos --}}
        <div class="flex items-center gap-6 mb-8 pb-6" style="border-bottom: 2px solid #ea0000;">
            <div class="w-20 h-20 rounded-full overflow-hidden flex items-center justify-center shrink-0"
                 style="background-color: #ea0000;">
                @if($empleado->imagen)
                    <img src="{{ asset('storage/' . $empleado->imagen) }}"
                         class="w-full h-full object-cover">
                @else
                    <span class="text-white text-2xl font-bold">
                        {{ strtoupper(substr($empleado->nombre, 0, 1)) }}{{ strtoupper(substr($empleado->apellidop, 0, 1)) }}
                    </span>
                @endif
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    {{ $empleado->nombre }} {{ $empleado->apellidop }} {{ $empleado->apellidom }}
                </h2>
                <p class="text-sm text-gray-500">{{ $empleado->correo }}</p>
                <div class="flex items-center gap-2 mt-2">
                    <span class="text-xs px-2 py-1 rounded-full font-semibold"
                          style="background-color: #fef2f2; color: #ea0000;">
                        {{ $empleado->rol->nombre ?? '—' }}
                    </span>
                    <span class="text-xs px-2 py-1 rounded-full font-semibold
                          {{ $empleado->estado === 'activo' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ ucfirst($empleado->estado) }}
                    </span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('perfil.update') }}" enctype="multipart/form-data">
            @csrf

            {{-- Cambiar foto --}}
            <div class="mb-6">
                <h3 class="text-sm font-bold text-gray-700 mb-3">Cambiar foto de perfil</h3>
                <div class="flex items-center gap-4">
                    <label class="cursor-pointer group">
                        <div class="w-16 h-16 rounded-full bg-gray-100 border-2 border-dashed border-gray-300
                                    flex items-center justify-center overflow-hidden relative
                                    group-hover:border-red-400 transition">
                            @if($empleado->imagen)
                                <img id="preview" src="{{ asset('storage/' . $empleado->imagen) }}"
                                     class="w-full h-full object-cover">
                            @else
                                <img id="preview" src="" class="w-full h-full object-cover hidden">
                                <svg id="icono" class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909"/>
                                </svg>
                            @endif
                        </div>
                        <input type="file" name="imagen" accept="image/jpg,image/jpeg,image/png"
                               class="hidden" onchange="previewImagen(this, 'preview', 'icono')">
                    </label>
                    <p class="text-xs text-gray-400">JPG o PNG, máx. 5MB</p>
                </div>
                @error('imagen') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Cambiar contraseña --}}
            <div class="mb-6" style="border-top: 1px solid #e5e7eb; padding-top: 24px;">
                <h3 class="text-sm font-bold text-gray-700 mb-3">Cambiar contraseña</h3>
                <p class="text-xs text-gray-400 mb-4">Deja en blanco si no deseas cambiarla.</p>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña actual</label>
                    <input type="password" name="contrasena_actual"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500
                                  @error('contrasena_actual') border-red-500 @enderror">
                    @error('contrasena_actual') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nueva contraseña</label>
                    <input type="password" name="contrasena_nueva"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500
                                  @error('contrasena_nueva') border-red-500 @enderror">
                    @error('contrasena_nueva') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
                    <input type="password" name="contrasena_confirmar"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500
                                  @error('contrasena_confirmar') border-red-500 @enderror">
                    @error('contrasena_confirmar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Botón --}}
            <div class="flex justify-end">
                <button type="submit"
                        class="text-white font-semibold px-6 py-2 rounded-lg transition text-sm"
                        style="background-color: #ea0000;"
                        onmouseover="this.style.backgroundColor='#5d0c03'"
                        onmouseout="this.style.backgroundColor='#ea0000'">
                    Guardar cambios
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