@extends('layouts.app')

@section('titulo', isset($producto) ? 'Editar Producto' : 'Nuevo Producto')

@section('content')

{{-- Todo el x-data vive en este div que envuelve form + modal --}}
<div class="max-w-2xl mx-auto"
     x-data="{
        categoriaSeleccionada: '{{ old('categoria_nombre', isset($producto) ? ($producto->categoria->nombre ?? '') : '') }}',
        categoriasConTamanios: ['Alitas', 'Cocteleria'],
        get esTamanios() {
            return this.categoriasConTamanios.includes(this.categoriaSeleccionada);
        },
        precios: {{ isset($producto) && $producto->precios->count()
            ? $producto->precios->map(fn($p) => ['tamanio_id' => (string) $p->tamanio_id, 'precio' => $p->precio])
            : '[]' }},
        modalError: false,
        mensajeError: '',
        mostrarError(msg) {
            this.mensajeError = msg;
            this.modalError = true;
        },
        intentarGuardar() {
            if (this.esTamanios) {
                if (this.precios.length === 0) {
                    this.mostrarError('Agrega al menos un tamaño con precio antes de guardar.');
                    return;
                }
                for (let i = 0; i < this.precios.length; i++) {
                    if (!this.precios[i].tamanio_id) {
                        this.mostrarError('Selecciona el tamaño en la fila ' + (i + 1) + '.');
                        return;
                    }
                    if (!this.precios[i].precio || parseFloat(this.precios[i].precio) <= 0) {
                        this.mostrarError('Ingresa un precio válido en la fila ' + (i + 1) + '.');
                        return;
                    }
                }
            }
            document.getElementById('form-producto').submit();
        }
     }">

    <div class="bg-white rounded-2xl shadow p-8">

        <h3 class="text-xl font-bold text-gray-800 mb-6">
            {{ isset($producto) ? 'Editar producto' : 'Registrar producto' }}
        </h3>

        <form method="POST"
              action="{{ isset($producto) ? route('productos.update', $producto) : route('productos.store') }}"
              enctype="multipart/form-data"
              id="form-producto">
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

            {{-- Categoría --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Categoría <span class="text-red-500">*</span></label>
                <select name="categoria_id"
                        @change="categoriaSeleccionada = $event.target.options[$event.target.selectedIndex].text"
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

            {{-- Precio base y Existencia --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Precio base
                        <span x-show="esTamanios" class="text-gray-400 font-normal text-xs">(desactivado por tamaños)</span>
                        <span x-show="!esTamanios" class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-400 text-sm">$</span>
                        <input type="number" name="precio_base" step="0.01" min="0"
                               :value="esTamanios ? '0' : '{{ old('precio_base', $producto->precio_base ?? '') }}'"
                               :disabled="esTamanios"
                               :class="esTamanios ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : ''"
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

            {{-- Estado --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado <span class="text-red-500">*</span></label>
                <select name="estado"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="activo"   {{ old('estado', $producto->estado ?? 'activo') === 'activo'   ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ old('estado', $producto->estado ?? '') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>

            {{-- Precios por tamaño --}}
            <div class="mb-6" x-show="esTamanios" x-transition>
                <div class="flex justify-between items-center mb-3">
                    <label class="block text-sm font-medium text-gray-700">
                        Precios por tamaño <span class="text-red-500">*</span>
                    </label>
                    <button type="button"
                            @click="precios.push({ tamanio_id: '', precio: '' })"
                            class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-3 py-1 rounded-lg transition">
                        + Agregar tamaño
                    </button>
                </div>

                <div class="space-y-2">
                    <template x-for="(precio, index) in precios" :key="index">
                        <div class="flex gap-3 items-center">

                            <select :name="'precios[' + index + '][tamanio_id]'"
                                    x-model="precio.tamanio_id"
                                    class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="">-- Tamaño --</option>
                                @foreach($tamanios as $tamanio)
                                <option value="{{ $tamanio->id }}">
                                    @if(in_array($tamanio->unidad, ['vaso', 'litro']))
                                        {{ ucfirst($tamanio->unidad) }}
                                    @else
                                        {{ $tamanio->cantidad }} {{ $tamanio->unidad }}
                                    @endif
                                </option>
                                @endforeach
                            </select>

                            <div class="relative w-32">
                                <span class="absolute left-3 top-2 text-gray-400 text-sm">$</span>
                                <input type="number"
                                       :name="'precios[' + index + '][precio]'"
                                       x-model="precio.precio"
                                       step="0.01" min="0.01"
                                       placeholder="0.00"
                                       class="w-full border border-gray-300 rounded-lg pl-7 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            </div>

                            <button type="button"
                                    @click="precios.splice(index, 1)"
                                    class="text-red-400 hover:text-red-600 transition text-lg font-bold">✕</button>
                        </div>
                    </template>
                </div>

                <p x-show="precios.length === 0" class="text-xs text-red-400 mt-2">
                    ⚠ Agrega al menos un tamaño con precio.
                </p>
            </div>

            {{-- Botones --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('productos.index') }}"
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold px-5 py-2 rounded-lg transition">
                    Cancelar
                </a>
                <button type="button"
                        @click="intentarGuardar()"
                        class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
                    {{ isset($producto) ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>

        </form>
    </div>

    {{-- Modal de error de validación --}}
    <div x-show="modalError"
         x-cloak
         @keydown.escape.window="modalError = false"
         class="fixed inset-0 flex items-center justify-center z-50"
         style="background-color: rgba(0,0,0,0.4);">
        <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm mx-4">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-full flex items-center justify-center shrink-0"
                     style="background-color: #fef2f2;">
                    <svg class="w-6 h-6" style="color: #ea0000;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">Campo requerido</h4>
                    <p class="text-sm text-gray-500 mt-0.5" x-text="mensajeError"></p>
                </div>
            </div>
            <div class="flex justify-end">
                <button @click="modalError = false"
                        class="text-white font-semibold px-4 py-2 rounded-lg transition text-sm"
                        style="background-color: #ea0000;"
                        onmouseover="this.style.backgroundColor='#5d0c03'"
                        onmouseout="this.style.backgroundColor='#ea0000'">
                    Entendido
                </button>
            </div>
        </div>
    </div>

</div>{{-- fin x-data principal --}}

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