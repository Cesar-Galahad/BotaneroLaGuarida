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
        'precio'       => (float) $p->precio_base,
        'categoria_id' => $p->categoria_id,
        'imagen'       => $p->imagen,
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
        ])->values(),
    ])->values();

    $detallesJs = $detalles->map(fn($d) => [
        'producto_id'  => $d->producto_id,
        'nombre'       => $d->producto->nombre,
        'precio'       => (float) $d->precio_unitario,
        'descuento'    => (float) $d->descuento_aplicado,
        'cantidad'     => $d->cantidad,
        'subtotal'     => (float) (($d->precio_unitario - $d->descuento_aplicado) * $d->cantidad),
        'es_promo'     => false,
        'promocion_id' => null,
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
                        <p class="text-red-600 font-bold text-sm mt-1" x-text="'$' + producto.precio.toFixed(2)"></p>
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
                                    @click="agregarProducto(prod, promo)"
                                    class="w-full flex justify-between items-center bg-white hover:bg-yellow-100 border border-yellow-200 rounded-lg px-3 py-2 transition">
                                    <span class="text-sm text-gray-700" x-text="prod.nombre"></span>
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
    <div class="w-1/3 bg-white shadow rounded-2xl p-6 flex flex-col">

        <h3 class="text-lg font-bold mb-1">Pedido — Mesa {{ $pedido->mesa->numero }}</h3>
        <p class="text-xs text-gray-400 mb-4">{{ $pedido->fecha->format('d/m/Y H:i') }}</p>

        {{-- Lista productos --}}
        <div class="flex-1 overflow-y-auto space-y-2 mb-4">
            <template x-for="item in pedido" :key="item.producto_id">
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

        cantidadEn(productoId) {
            const item = this.pedido.find(p => p.producto_id === productoId);
            return item ? item.cantidad : 0;
        },

        calcularPrecioPromo(precio, promo) {
            const descuento = promo.tipo === 'porcentaje'
                ? Math.round(precio * promo.valor / 100 * 100) / 100
                : Math.min(promo.valor, precio);
            return precio - descuento;
        },

        async agregarProducto(producto, promo) {
            const descuento = promo
                ? (promo.tipo === 'porcentaje'
                    ? Math.round(producto.precio * promo.valor / 100 * 100) / 100
                    : Math.min(promo.valor, producto.precio))
                : 0;

            const existente = this.pedido.find(p => p.producto_id === producto.id);

            if (existente) {
                existente.cantidad++;
                existente.descuento = descuento;
                existente.subtotal  = (existente.precio - descuento) * existente.cantidad;
                existente.es_promo  = !!promo;
            } else {
                this.pedido.push({
                    producto_id:  producto.id,
                    nombre:       producto.nombre,
                    precio:       producto.precio,
                    descuento:    descuento,
                    cantidad:     1,
                    subtotal:     producto.precio - descuento,
                    es_promo:     !!promo,
                    promocion_id: promo ? promo.id : null,
                });
            }

            await fetch(`/pedidos/${this.pedidoId}/agregar`, {
                method:  'POST',
                headers: {
                    'Content-Type':  'application/json',
                    'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    producto_id:  producto.id,
                    cantidad:     1,
                    promocion_id: promo ? promo.id : null,
                }),
            });
        },

        async aumentar(item) {
            item.cantidad++;
            item.subtotal = (item.precio - item.descuento) * item.cantidad;

            await fetch(`/pedidos/${this.pedidoId}/cantidad`, {
                method:  'PATCH',
                headers: {
                    'Content-Type':  'application/json',
                    'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ producto_id: item.producto_id, cantidad: item.cantidad }),
            });
        },

        async disminuir(item) {
            if (item.cantidad > 1) {
                item.cantidad--;
                item.subtotal = (item.precio - item.descuento) * item.cantidad;

                await fetch(`/pedidos/${this.pedidoId}/cantidad`, {
                    method:  'PATCH',
                    headers: {
                        'Content-Type':  'application/json',
                        'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ producto_id: item.producto_id, cantidad: item.cantidad }),
                });
            } else {
                this.eliminar(item);
            }
        },

        async eliminar(item) {
            this.pedido = this.pedido.filter(p => p !== item);

            await fetch(`/pedidos/${this.pedidoId}/eliminar`, {
                method:  'DELETE',
                headers: {
                    'Content-Type':  'application/json',
                    'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ producto_id: item.producto_id }),
            });
        },
    }
}
</script>

@endsection