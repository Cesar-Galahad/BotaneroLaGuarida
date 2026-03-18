@extends('layouts.app')

@section('titulo', 'Mesa ' . $pedido->mesa->numero)

@section('content')

<div class="mb-4">
    <a href="{{ route('mesas.index') }}"
       class="inline-flex items-center gap-2 text-gray-600 hover:text-red-600 transition">
        ← Volver a Mesas
    </a>
</div>

@php
    $productosJs = $productos->map(fn($p) => [
        'id'           => $p->id,
        'nombre'       => $p->nombre,
        'precio'       => $p->precios->count() > 0 ? (float) $p->precios->first()->precio : (float) $p->precio_base,
        'categoria_id' => $p->categoria_id,
        'imagen'       => $p->imagen,
        'precios'      => $p->precios->map(fn($pr) => [
            'nombre' => $pr->nombre,
            'precio' => (float) $pr->precio,
        ])->values(),
    ])->values();

    $promocionesJs = $promociones->map(fn($pr) => [
        'id'        => $pr->id,
        'nombre'    => $pr->nombre_p,
        'tipo'      => $pr->tipo,
        'valor'     => (float) $pr->valor,
        'productos' => $pr->productos->map(fn($p) => [
            'id'     => $p->id,
            'nombre' => $p->nombre,
            'precio' => (float) $p->precio_base,
            'tamano' => $p->pivot->tamano,
            'precios'=> $p->precios->map(fn($pr) => [
                'nombre' => $pr->nombre,
                'precio' => (float) $pr->precio,
            ])->values(),
        ])->values(),
    ])->values();

    $detallesJs = $detalles->map(fn($d) => [
        'producto_id'  => $d->producto_id,
        'nombre'       => $d->producto->nombre . ($d->tamano ? ' (' . $d->tamano . ')' : ''),
        'precio'       => (float) $d->precio_unitario,
        'descuento'    => (float) $d->descuento_aplicado,
        'cantidad'     => $d->cantidad,
        'subtotal'     => (float) (($d->precio_unitario - $d->descuento_aplicado) * $d->cantidad),
        'es_promo'     => false,
        'promocion_id' => null,
        'tamano'       => $d->tamano,
    ])->values();
@endphp

<div x-data="pos({{ $pedido->id }}, {{ $productosJs->toJson() }}, {{ $promocionesJs->toJson() }}, {{ $detallesJs->toJson() }})"
     class="flex gap-6">

    <!-- PRODUCTOS -->
    <div class="w-2/3 bg-white shadow rounded-2xl p-6">

        {{-- Filtro categorías --}}
        <div class="flex flex-wrap gap-2 mb-6">
            <button @click="categoriaActiva = null"
                    :class="categoriaActiva === null ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700'"
                    class="px-4 py-2 rounded-lg text-sm font-semibold transition">
                Todos
            </button>

            <button @click="categoriaActiva = 'promos'"
                    :class="categoriaActiva === 'promos' ? 'bg-yellow-400 text-black' : 'bg-yellow-100 text-yellow-700'"
                    class="px-4 py-2 rounded-lg text-sm font-semibold transition">
                🏷️ Promociones
            </button>

            @foreach($categorias as $categoria)
            <button @click="categoriaActiva = {{ $categoria->id }}"
                    :class="categoriaActiva === {{ $categoria->id }} ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700'"
                    class="px-4 py-2 rounded-lg text-sm font-semibold transition">
                {{ $categoria->nombre }}
            </button>
            @endforeach
        </div>

        {{-- Grid productos --}}
        <div class="grid grid-cols-3 gap-4">

            {{-- Vista normal --}}
            <template x-if="categoriaActiva !== 'promos'">
                <template x-for="producto in productosFiltrados" :key="producto.id">
                    <button
                        @click="agregarProducto(producto, null)"
                        class="relative bg-gray-100 rounded-xl p-4 hover:bg-red-50 hover:shadow transition text-left">

                        <span x-show="cantidadEn(producto.id) > 0"
                              x-text="cantidadEn(producto.id)"
                              class="absolute top-2 right-2 bg-red-600 text-white text-xs w-6 h-6 flex items-center justify-center rounded-full font-bold">
                        </span>

                        <div class="w-full h-24 bg-gray-200 rounded-lg mb-3 flex items-center justify-center overflow-hidden">
                            <template x-if="producto.imagen">
                                <img :src="'/storage/' + producto.imagen" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!producto.imagen">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909"/>
                                </svg>
                            </template>
                        </div>

                        <p class="font-semibold text-sm text-gray-800 leading-tight" x-text="producto.nombre"></p>
                        {{-- Precio --}}
                        <template x-if="producto.precios && producto.precios.length > 0">
                            <p class="text-red-600 font-bold text-xs mt-1">
                                Desde $<span x-text="Math.min(...producto.precios.map(p => p.precio)).toFixed(2)"></span>
                            </p>
                        </template>
                        <template x-if="!producto.precios || producto.precios.length === 0">
                            <p class="text-red-600 font-bold text-sm mt-1" x-text="'$' + producto.precio.toFixed(2)"></p>
                        </template>
                    </button>
                </template>
            </template>

            {{-- Vista promociones --}}
            <template x-if="categoriaActiva === 'promos'">
                <template x-for="promo in promociones" :key="promo.id">
                    <div class="bg-yellow-50 border border-yellow-300 rounded-xl p-4">
                        <div class="mb-3">
                            <p class="font-bold text-sm text-gray-800" x-text="promo.nombre"></p>
                            <p class="text-xs text-yellow-600 font-semibold mt-1"
                            x-text="promo.tipo === 'porcentaje' ? promo.valor + '% de descuento' : '$' + promo.valor.toFixed(2) + ' de descuento'">
                            </p>
                        </div>
                        <p class="text-xs text-gray-500 mb-2">Productos incluidos:</p>
                        <div class="space-y-1">
                            <template x-for="prod in promo.productos" :key="prod.id">
                                <button
                                    @click="agregarConPromo(prod, promo)"
                                    class="w-full flex justify-between items-center bg-white hover:bg-yellow-100 border border-yellow-200 rounded-lg px-3 py-2 transition">
                                    <div class="text-left">
                                        <span class="text-sm text-gray-700" x-text="prod.nombre"></span>
                                        <span x-show="prod.tamano" class="text-xs text-gray-400 ml-1" x-text="'(' + prod.tamano + ')'"></span>
                                    </div>
                                    <div class="text-right ml-2">
                                        <p class="text-xs line-through text-gray-400" x-text="'$' + prod.precio.toFixed(2)"></p>
                                        <p class="text-sm font-bold text-green-600"
                                        x-text="'$' + calcularPrecioPromo(prod.precio, promo).toFixed(2)">
                                        </p>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </template>

        </div>
    </div>

    <!-- PEDIDO ACTUAL -->
    <div class="w-1/3 bg-white shadow rounded-2xl p-6 flex flex-col" style="max-height: calc(100vh - 120px); min-height: 300px;">

        <h3 class="text-lg font-bold mb-1">Pedido — Mesa {{ $pedido->mesa->numero }}</h3>
        <p class="text-xs text-gray-400 mb-4">{{ $pedido->fecha->format('d/m/Y H:i') }}</p>

        {{-- Lista productos --}}
        <div class="overflow-y-auto space-y-2 mb-4" style="max-height: 400px;">
            <template x-for="item in pedido" :key="item.producto_id + '_' + (item.promocion_id ?? 'normal') + '_' + (item.tamano ?? '')">
                <div class="bg-gray-50 rounded-lg px-3 py-2">
                    <div class="flex justify-between items-start">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate" x-text="item.nombre"></p>
                            <p class="text-xs text-gray-500" x-text="'$' + item.precio.toFixed(2) + ' c/u'"></p>
                            <p x-show="item.es_promo"
                            class="text-xs text-yellow-600 font-semibold">
                                🏷️ Con promoción
                            </p>
                            <p x-show="item.descuento > 0"
                            class="text-xs text-green-600 font-semibold"
                            x-text="'Descuento: -$' + item.descuento.toFixed(2)">
                            </p>
                        </div>
                        <div class="flex items-center gap-1 ml-2">
                            <button @click="disminuir(item)"
                                    class="w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded text-sm font-bold">−</button>
                            <span class="w-6 text-center text-sm font-bold" x-text="item.cantidad"></span>
                            <button @click="aumentar(item)"
                                    class="w-6 h-6 bg-green-500 hover:bg-green-600 text-white rounded text-sm font-bold">+</button>
                            <button @click="eliminar(item)"
                                    class="w-6 h-6 bg-gray-400 hover:bg-gray-500 text-white rounded text-sm ml-1">✕</button>
                        </div>
                    </div>
                    <p class="text-xs text-right font-bold text-gray-700 mt-1"
                    x-text="'Subtotal: $' + item.subtotal.toFixed(2)">
                    </p>
                </div>
            </template>

            <div x-show="pedido.length === 0" class="text-center text-gray-400 text-sm py-8">
                Sin productos aún
            </div>
        </div>

        {{-- Total --}}
        <div class="border-t pt-4 flex justify-between font-bold text-lg mb-4">
            <span>Total</span>
            <span class="text-red-600" x-text="'$' + total.toFixed(2)"></span>
        </div>
        {{-- Asignar cliente --}}
        <div class="mb-4 border-t pt-4"
            x-data="{
                busqueda: '',
                resultados: [],
                clienteAsignado: null,
                buscando: false,
                async buscar() {
                    if (this.busqueda.length < 2) { this.resultados = []; return; }
                    this.buscando = true;
                    const res = await fetch('/clientes/buscar?q=' + encodeURIComponent(this.busqueda));
                    this.resultados = await res.json();
                    this.buscando = false;
                },
                async asignar(cliente) {
                    this.clienteAsignado = cliente;
                    this.resultados = [];
                    this.busqueda = '';
                    await fetch('/pedidos/{{ $pedido->id }}/cliente', {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                        body: JSON.stringify({ cliente_id: cliente.id }),
                    });
                },
                async quitar() {
                    this.clienteAsignado = null;
                    await fetch('/pedidos/{{ $pedido->id }}/cliente', {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                        body: JSON.stringify({ cliente_id: null }),
                    });
                }
            }">

            <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Cliente</p>

            {{-- Cliente asignado --}}
            <template x-if="clienteAsignado">
                <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg px-3 py-2 mb-2">
                    <div>
                        <p class="text-sm font-semibold text-gray-800" x-text="clienteAsignado.nombre + ' ' + clienteAsignado.apellidop"></p>
                        <p class="text-xs text-green-600" x-text="'★ ' + clienteAsignado.puntos + ' puntos'"></p>
                    </div>
                    <button @click="quitar()" class="text-red-400 hover:text-red-600 text-xs">✕</button>
                </div>
            </template>

            {{-- Buscador --}}
            <template x-if="!clienteAsignado">
                <div class="relative">
                    <input type="text"
                        x-model="busqueda"
                        @input.debounce.300ms="buscar()"
                        placeholder="Buscar cliente..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">

                    <div x-show="resultados.length > 0"
                        class="absolute w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 z-10">
                        <template x-for="cliente in resultados" :key="cliente.id">
                            <button @click="asignar(cliente)"
                                    class="w-full text-left px-3 py-2 hover:bg-gray-50 transition border-b border-gray-100 last:border-0">
                                <p class="text-sm font-medium text-gray-800" x-text="cliente.nombre + ' ' + cliente.apellidop"></p>
                                <p class="text-xs text-gray-400" x-text="'★ ' + cliente.puntos + ' puntos'"></p>
                            </button>
                        </template>
                    </div>
                </div>
            </template>

        </div>
        {{-- Botón cerrar cuenta --}}
        <button @click="$dispatch('abrir-pago')"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-lg transition mb-2">
            Cerrar Cuenta
        </button>

        {{-- Botón cancelar pedido --}}
        <form method="POST" action="{{ route('pedidos.cancelar', $pedido) }}"
              onsubmit="return confirm('¿Cancelar el pedido?')">
            @csrf
            @method('PATCH')
            <button type="submit"
                    class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 rounded-lg transition text-sm">
                Cancelar Pedido
            </button>
        </form>

    </div>

</div>
{{-- Modal selección de tamaño --}}
<div x-data="{ modalTamano: false, productoSeleccionado: null, promoSeleccionada: null }"
     @abrir-tamano.window="modalTamano = true; productoSeleccionado = $event.detail.producto; promoSeleccionada = $event.detail.promo"
     x-show="modalTamano"
     x-cloak
     @keydown.escape.window="modalTamano = false"
     class="fixed inset-0 bg-black bg-opacity-20 flex items-center justify-center z-50">

    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm mx-4">
        <h4 class="font-bold text-gray-800 text-lg mb-1">Selecciona el tamaño</h4>
        <p class="text-sm text-gray-400 mb-4" x-text="productoSeleccionado?.nombre"></p>

        <div class="space-y-2 mb-6">
            <template x-if="productoSeleccionado">
                <template x-for="tamano in productoSeleccionado.precios" :key="tamano.nombre">
                    <button
                        @click="$dispatch('confirmar-tamano', { tamano, producto: productoSeleccionado, promo: promoSeleccionada }); modalTamano = false"
                        class="w-full flex justify-between items-center bg-gray-50 hover:bg-red-50 hover:border-red-300 border border-gray-200 rounded-xl px-4 py-3 transition">
                        <span class="font-semibold text-gray-700" x-text="tamano.nombre"></span>
                        <span class="font-bold text-red-600" x-text="'$' + tamano.precio.toFixed(2)"></span>
                    </button>
                </template>
            </template>
        </div>

        <button @click="modalTamano = false"
                class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 rounded-lg transition text-sm">
            Cancelar
        </button>
    </div>
</div>
{{-- Modal método de pago --}}
<div x-data="{ modalPago: false, metodo: '' }"
     x-show="modalPago"
     x-cloak
     @abrir-pago.window="modalPago = true"
     @keydown.escape.window="modalPago = false"
     class="fixed inset-0 bg-white bg-opacity-10 flex items-center justify-center z-50">

    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm mx-4">

        <h4 class="font-bold text-gray-800 text-lg mb-1">Cerrar cuenta</h4>
        <p class="text-sm text-gray-400 mb-5">Selecciona el método de pago</p>

        <form method="POST" action="{{ route('pedidos.cerrar', $pedido) }}">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-2 gap-3 mb-6">

                <label class="cursor-pointer">
                    <input type="radio" name="metodo_pago" value="efectivo"
                           x-model="metodo" class="hidden peer">
                    <div class="peer-checked:bg-red-600 peer-checked:text-white peer-checked:border-red-600
                                border-2 border-gray-200 rounded-xl p-4 text-center hover:border-red-300 transition">
                        <p class="text-2xl mb-1">💵</p>
                        <p class="text-sm font-semibold">Efectivo</p>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="metodo_pago" value="tarjeta"
                           x-model="metodo" class="hidden peer">
                    <div class="peer-checked:bg-red-600 peer-checked:text-white peer-checked:border-red-600
                                border-2 border-gray-200 rounded-xl p-4 text-center hover:border-red-300 transition">
                        <p class="text-2xl mb-1">💳</p>
                        <p class="text-sm font-semibold">Tarjeta</p>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="metodo_pago" value="transferencia"
                           x-model="metodo" class="hidden peer">
                    <div class="peer-checked:bg-red-600 peer-checked:text-white peer-checked:border-red-600
                                border-2 border-gray-200 rounded-xl p-4 text-center hover:border-red-300 transition">
                        <p class="text-2xl mb-1">📱</p>
                        <p class="text-sm font-semibold">Transferencia</p>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="metodo_pago" value="otro"
                           x-model="metodo" class="hidden peer">
                    <div class="peer-checked:bg-red-600 peer-checked:text-white peer-checked:border-red-600
                                border-2 border-gray-200 rounded-xl p-4 text-center hover:border-red-300 transition">
                        <p class="text-2xl mb-1">🔄</p>
                        <p class="text-sm font-semibold">Otro</p>
                    </div>
                </label>

            </div>

            @error('metodo_pago')
                <p class="text-red-500 text-xs mb-4">{{ $message }}</p>
            @enderror

            <div class="flex gap-3">
                <button type="button"
                        @click="modalPago = false; metodo = ''"
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 rounded-lg transition text-sm">
                    Cancelar
                </button>
                <button type="submit"
                        :disabled="metodo === ''"
                        :class="metodo === '' ? 'opacity-50 cursor-not-allowed' : 'hover:bg-red-700'"
                        class="flex-1 bg-red-600 text-white font-semibold py-2 rounded-lg transition text-sm">
                    Confirmar
                </button>
            </div>

        </form>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('modal', {
        abierto: false,
        producto: null,
        promocionSeleccionada: null,
        _callback: null,

        abrir(producto, callback) {
            this.producto = producto;
            this.promocionSeleccionada = null;
            this._callback = callback;
            this.abierto = true;
        },

        cerrar() {
            this.abierto = false;
            this.producto = null;
            this._callback = null;
        },

        confirmar() {
            if (this._callback) this._callback(this.promocionSeleccionada);
            this.cerrar();
        }
    });
});

function pos(pedidoId, productos, promociones, itemsIniciales) {
    return {
        pedidoId,
        productos,
        promociones,
        pedido: itemsIniciales,
        categoriaActiva: null,

        get productosFiltrados() {
            if (this.categoriaActiva === null) return this.productos;
            if (this.categoriaActiva === 'promos') return [];
            return this.productos.filter(p => p.categoria_id === this.categoriaActiva);
        },

        get total() {
            return this.pedido.reduce((sum, item) => sum + item.subtotal, 0);
        },

        claveItem(productoId, promocionId, tamano) {
            return `${productoId}_${promocionId ?? 'normal'}_${tamano ?? ''}`;
        },

        cantidadEn(productoId) {
            const item = this.pedido.find(p => p.producto_id === productoId && !p.es_promo);
            return item ? item.cantidad : 0;
        },

        calcularPrecioPromo(precio, promo) {
            const descuento = promo.tipo === 'porcentaje'
                ? Math.round(precio * promo.valor / 100 * 100) / 100
                : Math.min(promo.valor, precio);
            return precio - descuento;
        },

        agregarProducto(producto, promo) {
            // Si tiene tamaños, abrir modal
            if (producto.precios && producto.precios.length > 0) {
                this.$dispatch('abrir-tamano', { producto, promo });
                return;
            }
            this._agregar(producto, promo, null);
        },
        agregarConPromo(prod, promo) {
            // Si tiene tamaño específico en la promo, ir directo
            if (prod.tamano) {
                const precio = prod.precios.find(p => p.nombre === prod.tamano);
                if (precio) {
                    const productoConTamano = { ...prod, precio: precio.precio };
                    this._agregar(productoConTamano, promo, prod.tamano);
                    return;
                }
            }
            // Si no tiene tamaño específico pero tiene precios, abrir modal
            if (prod.precios && prod.precios.length > 0) {
                this.$dispatch('abrir-tamano', { producto: prod, promo });
                return;
            }
            // Producto simple
            this._agregar(prod, promo, null);
        },
        init() {
            // Escuchar cuando se confirma un tamaño
            window.addEventListener('confirmar-tamano', (e) => {
                const { tamano, producto, promo } = e.detail;
                const productoConTamano = { ...producto, precio: tamano.precio };
                this._agregar(productoConTamano, promo, tamano.nombre);
            });
        },

        async _agregar(producto, promo, tamano) {
            let descuento = 0;
            let cantidad  = 1;

            if (promo) {
                if (promo.nombre.includes('2x1')) cantidad = 2;
                else if (promo.nombre.includes('3x2')) cantidad = 3;

                descuento = promo.tipo === 'porcentaje'
                    ? Math.round(producto.precio * promo.valor / 100 * 100) / 100
                    : Math.min(promo.valor, producto.precio);
            }

            const clave     = this.claveItem(producto.id, promo ? promo.id : null, tamano);
            const existente = this.pedido.find(p => this.claveItem(p.producto_id, p.promocion_id, p.tamano) === clave);

            const nombreMostrar = tamano ? `${producto.nombre} (${tamano})` : producto.nombre;

            if (existente) {
                existente.cantidad += cantidad;
                existente.subtotal  = (existente.precio - existente.descuento) * existente.cantidad;
            } else {
                this.pedido.push({
                    producto_id:  producto.id,
                    nombre:       nombreMostrar,
                    precio:       producto.precio,
                    descuento:    descuento,
                    cantidad:     cantidad,
                    subtotal:     (producto.precio - descuento) * cantidad,
                    es_promo:     !!promo,
                    promocion_id: promo ? promo.id : null,
                    tamano:       tamano,
                });
            }

            await fetch(`/pedidos/${this.pedidoId}/agregar`, {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    producto_id:  producto.id,
                    cantidad:     cantidad,
                    promocion_id: promo ? promo.id : null,
                    precio:       producto.precio,
                    tamano:       tamano ?? null,
                }),
            });
        },

        async aumentar(item) {
            item.cantidad++;
            item.subtotal = (item.precio - item.descuento) * item.cantidad;
            await fetch(`/pedidos/${this.pedidoId}/cantidad`, {
                method:  'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ producto_id: item.producto_id, cantidad: item.cantidad, tamano: item.tamano ?? null }),
            });
        },

        async disminuir(item) {
            if (item.cantidad > 1) {
                item.cantidad--;
                item.subtotal = (item.precio - item.descuento) * item.cantidad;
                await fetch(`/pedidos/${this.pedidoId}/cantidad`, {
                    method:  'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ producto_id: item.producto_id, cantidad: item.cantidad, tamano: item.tamano ?? null }),
                });
            } else {
                this.eliminar(item);
            }
        },

        async eliminar(item) {
            this.pedido = this.pedido.filter(p =>
                this.claveItem(p.producto_id, p.promocion_id, p.tamano) !== this.claveItem(item.producto_id, item.promocion_id, item.tamano)
            );
            await fetch(`/pedidos/${this.pedidoId}/eliminar`, {
                method:  'DELETE',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ producto_id: item.producto_id, tamano: item.tamano ?? null }),
            });
        },
    }
}
</script>

@endsection