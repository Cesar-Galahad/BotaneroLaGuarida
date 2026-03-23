@extends('layouts.app')

@section('titulo', isset($categoria) ? 'Editar Categoría' : 'Nueva Categoría')

@section('content')

<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-2xl shadow p-8">

        <h3 class="text-xl font-bold text-gray-800 mb-6">
            {{ isset($categoria) ? 'Editar categoría' : 'Registrar categoría' }}
        </h3>

        <form method="POST" action="{{ isset($categoria) ? route('categorias.update', $categoria) : route('categorias.store') }}" enctype="multipart/form-data">
            @csrf
            @if(isset($categoria)) @method('PUT') @endif

            {{-- Imagen --}}
            <div class="flex justify-center mb-6">
                <label class="cursor-pointer group">
                    <div class="w-24 h-24 rounded-xl bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden relative group-hover:border-red-400 transition">
                        @if(isset($categoria) && $categoria->imagen)
                            <img id="preview" src="{{ asset('storage/' . $categoria->imagen) }}" class="w-full h-full object-cover">
                        @else
                            <img id="preview" src="" class="w-full h-full object-cover hidden">
                            <svg id="icono" class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909"/>
                            </svg>
                        @endif
                        <div class="absolute inset-0 bg-black bg-opacity-30 rounded-xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                            <span class="text-white text-xs font-semibold">Cambiar</span>
                        </div>
                    </div>
                    <input type="file" name="imagen" accept="image/jpg,image/jpeg,image/png"
                        class="hidden" onchange="previewImagen(this, 'preview', 'icono')">
                    <p class="text-center text-xs text-gray-400 mt-2">PNG o JPG, máx. 2MB</p>
                </label>
            </div>

            {{-- Nombre --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nombre <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nombre"
                       value="{{ old('nombre', $categoria->nombre ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500
                              @error('nombre') border-red-500 @enderror">
                @error('nombre')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Botones --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('categorias.index') }}"
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold px-5 py-2 rounded-lg transition">
                    Cancelar
                </a>
                <button type="submit"
                        onclick="this.disabled=true; this.innerText='Guardando...'; this.form.submit();"
                        class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
                    {{ isset($categoria) ? 'Actualizar' : 'Guardar' }}
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