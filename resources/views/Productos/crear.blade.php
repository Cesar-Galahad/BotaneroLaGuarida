@extends('layouts.app')

@section('titulo', isset($producto) ? 'Editar Producto' : 'Nuevo Producto')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow p-8">

        <h3 class="text-xl font-bold text-gray-800 mb-6">
            {{ isset($producto) ? 'Editar producto' : 'Registrar producto' }}
        </h3>

        <form method="POST" action="{{ isset($producto) ? route('productos.update', $producto) : route('productos.store') }}" enctype="multipart/form-data">
            @csrf
            @if(isset($producto)) @method('PUT') @endif

            {{-- Imagen --}}
            <div class="flex justify-center mb-6">
                <label class="cursor-pointer group">
                    <div class="w-32 h-32 rounded-xl bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden relative group-hover:border-red-400 transition">
                        @if(isset($producto) && $producto->imagen)
                            <img id="preview" src="{{ asset('storage/' . $producto->imagen) }}" class="w-full h-full object-cover">
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
                    <p class="text-center text-xs text-gray-400 mt-2">JPG o PNG, máx. 2MB</p>
                </label>
            </div>

            {{-- Nombre --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500
                              @error('nombre') border-red-500 @enderror">
                @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Descripción --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea name="descripcion" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
            </div>

            {{-- Precio y Existencia --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-400 text-sm">$</span>
                        <input type="number" name="precio_base" step="0.01" min="0"
                               value="{{ old('precio_base', $producto->precio_base ?? '') }}"
                               class="w-full border border-gray-300 rounded-lg pl-7 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500
                                      @error('precio_base') border-red-500 @enderror">
                    </div>
                    @error('precio_base') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Existencia <span class="text-red-500">*</span></label>
                    <input type="number" name="existencia" min="0"
                           value="{{ old('existencia', $producto->existencia ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500
                                  @error('existencia') border-red-500 @enderror">
                    @error('existencia') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Categoría y Estado --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoría <span class="text-red-500">*</span></label>
                    <select name="categoria_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500
                                   @error('categoria_id') border-red-500 @enderror">
                        <option value="">-- Selecciona --</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}"
                                {{ old('categoria_id', $producto->categoria_id ?? '') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('categoria_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado <span class="text-red-500">*</span></label>
                    <select name="estado"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="activo"   {{ old('estado', $producto->estado ?? 'activo') === 'activo'   ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ old('estado', $producto->estado ?? '') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('productos.index') }}"
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold px-5 py-2 rounded-lg transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
                    {{ isset($producto) ? 'Actualizar' : 'Guardar' }}
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