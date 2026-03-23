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
    $etiquetaTamanio = fn($tamanio) => $tamanio
        ? (in_array($tamanio->unidad, ['vaso', 'litro'])
            ? ucfirst($tamanio->unidad)
            : $tamanio->cantidad . ' ' . $tamanio->unidad)
        : null;

    $productosJs = $productos->map(fn($p) => [
        'id'           => $p->id,
        'nombre'       => $p->nombre,
        'precio'       => $p->precios->count() > 0 ? (float) $p->precios->first()->precio : (float) $p->precio_base,
        'categoria_id' => $p->categoria_id,
        'imagen'       => $p->imagen,
        'precios'      => $p->precios->map(fn($pr) => [
            'id'     => $pr->tamanio_id,
            'nombre' => $etiquetaTamanio($pr->tamanio),
            'precio' => (float) $pr->precio,
        ])->values(),
    ])->values();

    $promocionesJs = $promociones->map(fn($pr) => [
        'id'        => $pr->id,
        'nombre'    => $pr->nombre_p,
        'tipo'      => $pr->tipo,
        'valor'     => (float) $pr->valor,
        'productos' => $pr->productos->map(fn($p) => [
            'id'         => $p->id,
            'nombre'     => $p->nombre,
            'precio'     => (function() use ($p) {
                $tamanioId = $p->pivot->tamanio_id ?? null;
                if ($tamanioId) {
                    $precioTamanio = $p->precios->firstWhere('tamanio_id', $tamanioId);
                    if ($precioTamanio) return (float) $precioTamanio->precio;
                }
                return $p->precios->count() > 0 ? (float) $p->precios->first()->precio : (float) $p->precio_base;
            })(),
            'tamano'     => $etiquetaTamanio($p->pivot->tamanio_id ? $p->precios->firstWhere('tamanio_id', $p->pivot->tamanio_id)?->tamanio : null),
            'tamanio_id' => $p->pivot->tamanio_id ?? null,
            'precios'    => $p->precios->map(fn($pr) => [
                'id'     => $pr->tamanio_id,
                'nombre' => $etiquetaTamanio($pr->tamanio),
                'precio' => (float) $pr->precio,
            ])->values(),
        ])->values(),
    ])->values();

    $detallesJs = $detalles->map(fn($d) => [
        'producto_id'  => $d->producto_id,
        'nombre'       => $d->producto->nombre . ($d->tamanio ? ' (' . $etiquetaTamanio($d->tamanio) . ')' : ''),
        'precio'       => (float) $d->precio_unitario,
        'descuento'    => (float) $d->descuento_aplicado,
        'cantidad'     => $d->cantidad,
        'subtotal'     => (float) $d->subtotal,
        'es_promo'     => false,
        'promocion_id' => null,
        'tamanio_id'   => $d->tamanio_id,
    ])->values();

    $canjesJs = $canjes->map(fn($c) => [
        'id'           => $c->id,
        'nombre'       => $c->producto->nombre ?? '',
        'tamano'       => $etiquetaTamanio($c->tamanio),
        'puntos_costo' => $c->puntos_costo,
    ])->values();
@endphp

<div x-data="{
        modalCanje: false,
        mensajeCanje: '',
     }"
     @abrir-modal-canje.window="modalCanje = true; mensajeCanje = $event.detail.msg">

<div x-data="pos({{ $pedido->id }}, {{ $productosJs->toJson() }}, {{ $promocionesJs->toJson() }}, {{ $detallesJs->toJson() }})"
     class="flex gap-6">

    {{-- PANEL IZQUIERDO --}}
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

            <template x-if="categoriaActiva !== 'promos'">
                <template x-for="producto in productosFiltrados" :key="producto.id">
                    <button @click="agregarProducto(producto, null)"
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

            <template x-if="categoriaActiva === 'promos'">
                <template x-for="promo in promociones" :key="promo.id">
                    <div class="bg-yellow-50 border border-yellow-300 rounded-xl p-4">
                        <div class="mb-3">
                            <p class="font-bold text-sm text-gray-800" x-text="promo.nombre"></p>
                            <p class="text-xs text-yellow-600 font-semibold mt-1" x-text="etiquetaPromo(promo)"></p>
                        </div>
                        <p class="text-xs text-gray-500 mb-2">Productos incluidos:</p>
                        <div class="space-y-1">
                            <template x-for="prod in promo.productos" :key="prod.id">
                                <button @click="agregarConPromo(prod, promo)"
                                        class="w-full flex justify-between items-center bg-white hover:bg-yellow-100 border border-yellow-200 rounded-lg px-3 py-2 transition">
                                    <div class="text-left">
                                        <span class="text-sm text-gray-700" x-text="prod.nombre"></span>
                                        <span x-show="prod.tamano" class="text-xs text-gray-400 ml-1" x-text="'(' + prod.tamano + ')'"></span>
                                    </div>
                                    <div class="text-right ml-2">
                                        <p class="text-xs line-through text-gray-400"
                                           x-text="'$' + (prod.precio * (promo.nombre.toLowerCase().includes('2x1') ? 2 : promo.nombre.toLowerCase().includes('3x2') ? 3 : 1)).toFixed(2)"></p>
                                        <p class="text-sm font-bold text-green-600" x-text="'$' + calcularPrecioPromo(prod.precio, promo).toFixed(2)"></p>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </template>

        </div>
    </div>

    {{-- PANEL DERECHO --}}
    <div class="w-1/3 bg-white shadow rounded-2xl p-6 flex flex-col"
         style="max-height: calc(100vh - 80px); overflow-y: auto;">

        <h3 class="text-lg font-bold mb-1">Pedido — Mesa {{ $pedido->mesa->numero }}</h3>
        <p class="text-xs text-gray-400 mb-4">{{ $pedido->fecha->format('d/m/Y H:i') }}</p>

        <div class="space-y-2 mb-4">
            <template x-for="item in pedido" :key="String(item.producto_id) + '_' + String(item.promocion_id ?? 'n') + '_' + String(item.tamanio_id ?? '')">
                <div class="bg-gray-50 rounded-lg px-3 py-2">
                    <div class="flex justify-between items-start">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate" x-text="item.nombre"></p>
                            <p class="text-xs text-gray-500" x-text="'$' + item.precio.toFixed(2) + ' c/u'"></p>
                            <p x-show="item.es_promo" class="text-xs text-yellow-600 font-semibold">🏷️ Con promoción</p>
                            <p x-show="item.descuento > 0"
                               class="text-xs text-green-600 font-semibold"
                               x-text="'Descuento: -$' + item.descuento.toFixed(2)"></p>
                        </div>
                        <div class="flex items-center gap-1 ml-2">
                            <button @click="disminuir(item)" class="w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded text-sm font-bold">−</button>
                            <span class="w-6 text-center text-sm font-bold" x-text="item.cantidad"></span>
                            <button @click="aumentar(item)" class="w-6 h-6 bg-green-500 hover:bg-green-600 text-white rounded text-sm font-bold">+</button>
                            <button @click="eliminar(item)" class="w-6 h-6 bg-gray-400 hover:bg-gray-500 text-white rounded text-sm ml-1">✕</button>
                        </div>
                    </div>
                    <p class="text-xs text-right font-bold text-gray-700 mt-1" x-text="'Subtotal: $' + item.subtotal.toFixed(2)"></p>
                </div>
            </template>
            <div x-show="pedido.length === 0" class="text-center text-gray-400 text-sm py-8">
                Sin productos aún
            </div>
        </div>

        <div class="border-t pt-4 flex justify-between font-bold text-lg mb-4">
            <span>Total</span>
            <span class="text-red-600" x-text="'$' + total.toFixed(2)"></span>
        </div>

        {{-- Cliente --}}
        <div class="mb-4 border-t pt-4"
             x-data="{
                 busqueda: '',
                 resultados: [],
                 clienteAsignado: null,
                 buscando: false,
                 canjes: {{ $canjesJs->toJson() }},
                 canjesDisponibles() {
                     if (!this.clienteAsignado) return [];
                     return this.canjes.filter(c => c.puntos_costo <= this.clienteAsignado.puntos);
                 },
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
                 },
                 async canjear(canje) {
                     const res = await fetch('/pedidos/{{ $pedido->id }}/canjear', {
                         method: 'POST',
                         headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                         body: JSON.stringify({ canje_id: canje.id }),
                     });
                     const data = await res.json();
                     if (data.ok) {
                         this.clienteAsignado.puntos = data.puntos_restantes;
                         window.dispatchEvent(new CustomEvent('producto-canjeado', {
                             detail: {
                                 producto_id: data.producto_id,
                                 nombre:      data.producto + (data.tamanio ? ' (' + data.tamanio + ')' : ''),
                                 precio:      0,
                                 tamanio_id:  data.tamanio_id ?? null,
                             }
                         }));
                         window.dispatchEvent(new CustomEvent('abrir-modal-canje', {
                             detail: { msg: '✅ ' + data.producto + (data.tamanio ? ' (' + data.tamanio + ')' : '') + ' canjeado correctamente.' }
                         }));
                     } else {
                         window.dispatchEvent(new CustomEvent('abrir-modal-canje', {
                             detail: { msg: '❌ ' + data.error }
                         }));
                     }
                 }
             }">

            <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Cliente</p>

            <template x-if="clienteAsignado">
                <div>
                    <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg px-3 py-2 mb-2">
                        <div>
                            <p class="text-sm font-semibold text-gray-800" x-text="clienteAsignado.nombre + ' ' + clienteAsignado.apellidop"></p>
                            <p class="text-xs text-green-600" x-text="'★ ' + clienteAsignado.puntos + ' puntos'"></p>
                            <p class="text-xs text-yellow-600 font-semibold" x-text="'+ ' + Math.floor(total * 0.05) + ' puntos por esta compra'"></p>
                        </div>
                        <button @click="quitar()" class="text-red-400 hover:text-red-600 text-xs">✕</button>
                    </div>
                    <template x-if="canjesDisponibles().length > 0">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-3 py-2 mb-2">
                            <p class="text-xs font-semibold text-yellow-700 mb-2">🎁 Canjeables</p>
                            <template x-for="canje in canjesDisponibles()" :key="canje.id">
                                <button @click="canjear(canje)"
                                        class="w-full flex justify-between items-center bg-white hover:bg-yellow-100 border border-yellow-200 rounded-lg px-3 py-2 mb-1 transition text-left">
                                    <div>
                                        <span class="text-sm text-gray-700" x-text="canje.nombre"></span>
                                        <span x-show="canje.tamano" class="text-xs text-gray-400 ml-1" x-text="'(' + canje.tamano + ')'"></span>
                                    </div>
                                    <span class="text-xs font-bold text-yellow-600" x-text="'★ ' + canje.puntos_costo"></span>
                                </button>
                            </template>
                        </div>
                    </template>
                </div>
            </template>

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

        <button @click="$dispatch('abrir-pago')"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-lg transition mb-2">
            Cerrar Cuenta
        </button>

        <form method="POST" action="{{ route('pedidos.cancelar', $pedido) }}"
              id="form-cancelar-{{ $pedido->id }}" x-data>
            @csrf
            @method('PATCH')
            <button type="button"
                    @click="$dispatch('abrir-confirm', {
                        mensaje: '¿Cancelar el pedido? Esta acción no se puede deshacer.',
                        formId: 'form-cancelar-{{ $pedido->id }}'
                    })"
                    class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 rounded-lg transition text-sm">
                Cancelar Pedido
            </button>
        </form>

    </div>
</div>

{{-- Modal tamaño --}}
<div x-data="{ modalTamano: false, productoSeleccionado: null, promoSeleccionada: null }"
     @abrir-tamano.window="modalTamano = true; productoSeleccionado = $event.detail.producto; promoSeleccionada = $event.detail.promo"
     x-show="modalTamano"
     x-cloak
     @keydown.escape.window="modalTamano = false"
     class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm mx-4">
        <h4 class="font-bold text-gray-800 text-lg mb-1">Selecciona el tamaño</h4>
        <p class="text-sm text-gray-400 mb-4" x-text="productoSeleccionado?.nombre"></p>
        <div class="space-y-2 mb-6">
            <template x-if="productoSeleccionado">
                <template x-for="tamano in productoSeleccionado.precios" :key="tamano.id">
                    <button @click="$dispatch('confirmar-tamano', { tamano: { id: tamano.id, nombre: tamano.nombre, precio: tamano.precio }, producto: productoSeleccionado, promo: promoSeleccionada }); modalTamano = false"
                            class="w-full flex justify-between items-center bg-gray-50 hover:bg-red-50 border border-gray-200 rounded-xl px-4 py-3 transition">
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

{{-- Modal pago --}}
<div x-data="{ modalPago: false, metodo: '' }"
     x-show="modalPago"
     x-cloak
     @abrir-pago.window="modalPago = true"
     @keydown.escape.window="modalPago = false"
     class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm mx-4">
        <h4 class="font-bold text-gray-800 text-lg mb-1">Cerrar cuenta</h4>
        <p class="text-sm text-gray-400 mb-5">Selecciona el método de pago</p>
        <form method="POST" action="{{ route('pedidos.cerrar', $pedido) }}">
            @csrf
            @method('PATCH')
            <div class="grid grid-cols-2 gap-3 mb-6">
                @foreach(['efectivo' => '💵', 'tarjeta' => '💳', 'transferencia' => '📱', 'otro' => '🔄'] as $valor => $icono)
                <label class="cursor-pointer">
                    <input type="radio" name="metodo_pago" value="{{ $valor }}" x-model="metodo" class="hidden peer">
                    <div class="peer-checked:bg-red-600 peer-checked:text-white peer-checked:border-red-600
                                border-2 border-gray-200 rounded-xl p-4 text-center hover:border-red-300 transition">
                        <p class="text-2xl mb-1">{{ $icono }}</p>
                        <p class="text-sm font-semibold">{{ ucfirst($valor) }}</p>
                    </div>
                </label>
                @endforeach
            </div>
            @error('metodo_pago')
                <p class="text-red-500 text-xs mb-4">{{ $message }}</p>
            @enderror
            <div class="flex gap-3">
                <button type="button" @click="modalPago = false; metodo = ''"
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

{{-- Modal canje --}}
<div x-show="modalCanje"
     x-cloak
     @keydown.escape.window="modalCanje = false"
     class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm mx-4">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-full bg-yellow-50 flex items-center justify-center shrink-0">
                <span class="text-2xl">🎁</span>
            </div>
            <div>
                <h4 class="font-bold text-gray-800">Canje de puntos</h4>
                <p class="text-sm text-gray-500 mt-0.5" x-text="mensajeCanje"></p>
            </div>
        </div>
        <div class="flex justify-end">
            <button @click="modalCanje = false"
                    class="text-white font-semibold px-4 py-2 rounded-lg transition text-sm"
                    style="background-color: #ea0000;"
                    onmouseover="this.style.backgroundColor='#5d0c03'"
                    onmouseout="this.style.backgroundColor='#ea0000'">
                Entendido
            </button>
        </div>
    </div>
</div>

</div>{{-- fin wrapper --}}

<script>
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

        claveItem(productoId, promocionId, tamanioId) {
            return `${productoId}_${promocionId ?? 'n'}_${tamanioId ?? ''}`;
        },

        cantidadEn(productoId) {
            const item = this.pedido.find(p => p.producto_id === productoId && !p.es_promo);
            return item ? item.cantidad : 0;
        },

        etiquetaPromo(promo) {
            const n = promo.nombre.toLowerCase();
            if (n.includes('2x1')) return '2x1 — Lleva 2, paga 1';
            if (n.includes('3x2')) return '3x2 — Lleva 3, paga 2';
            if (promo.tipo === 'porcentaje') return promo.valor + '% de descuento';
            return '$' + promo.valor.toFixed(2) + ' de descuento';
        },

        calcularPrecioPromo(precio, promo) {
            const n = promo.nombre.toLowerCase();
            if (n.includes('2x1')) return precio;
            if (n.includes('3x2')) return precio;
            const descuento = promo.tipo === 'porcentaje'
                ? Math.round(precio * promo.valor / 100 * 100) / 100
                : Math.min(promo.valor, precio);
            return precio - descuento;
        },

        agregarProducto(producto, promo) {
            if (producto.precios && producto.precios.length > 0) {
                this.$dispatch('abrir-tamano', { producto, promo });
                return;
            }
            this._agregar(producto, promo, null, null);
        },

        agregarConPromo(prod, promo) {
            if (prod.tamanio_id) {
                const precio = prod.precios.find(p => p.id === prod.tamanio_id);
                if (precio) {
                    this._agregar({ ...prod, precio: precio.precio }, promo, precio.nombre, prod.tamanio_id);
                    return;
                }
            }
            if (prod.precios && prod.precios.length > 0) {
                this.$dispatch('abrir-tamano', { producto: prod, promo });
                return;
            }
            this._agregar(prod, promo, null, null);
        },

        init() {
            window.addEventListener('confirmar-tamano', (e) => {
                const { tamano, producto, promo } = e.detail;
                this._agregar({ ...producto, precio: tamano.precio }, promo, tamano.nombre, tamano.id);
            });

            window.addEventListener('producto-canjeado', (e) => {
                const { producto_id, nombre, tamanio_id } = e.detail;
                const existente = this.pedido.find(p =>
                    p.producto_id === producto_id && p.tamanio_id === tamanio_id && !p.es_promo
                );
                if (existente) {
                    existente.cantidad++;
                    existente.subtotal = 0;
                } else {
                    this.pedido.push({
                        producto_id, nombre,
                        precio: 0, descuento: 0, cantidad: 1, subtotal: 0,
                        es_promo: false, promocion_id: null, tamanio_id,
                    });
                }
            });
        },

        async _agregar(producto, promo, tamanoNombre, tamanioId) {
            let descuento = 0;
            let cantidad  = 1;
            let subtotal  = 0;

            if (promo) {
                const n = promo.nombre.toLowerCase();
                if (n.includes('2x1')) {
                    cantidad  = 2;
                    descuento = producto.precio;
                    subtotal  = producto.precio; // pagas 1
                } else if (n.includes('3x2')) {
                    cantidad  = 3;
                    descuento = producto.precio;
                    subtotal  = producto.precio * 2; // pagas 2
                } else {
                    descuento = promo.tipo === 'porcentaje'
                        ? Math.round(producto.precio * promo.valor / 100 * 100) / 100
                        : Math.min(promo.valor, producto.precio);
                    subtotal = (producto.precio - descuento) * cantidad;
                }
            } else {
                subtotal = producto.precio * cantidad;
            }

            const clave     = this.claveItem(producto.id, promo ? promo.id : null, tamanioId);
            const existente = this.pedido.find(p =>
                this.claveItem(p.producto_id, p.promocion_id, p.tamanio_id) === clave
            );
            const nombre = tamanoNombre ? `${producto.nombre} (${tamanoNombre})` : producto.nombre;

            if (existente) {
                existente.cantidad += cantidad;
                // Recalcular subtotal según tipo de promo
                if (promo) {
                    const n = promo.nombre.toLowerCase();
                    if (n.includes('2x1')) {
                        existente.subtotal = existente.precio * (existente.cantidad / 2);
                    } else if (n.includes('3x2')) {
                        existente.subtotal = existente.precio * Math.floor(existente.cantidad / 3) * 2
                                           + existente.precio * (existente.cantidad % 3);
                    } else {
                        existente.subtotal = (existente.precio - existente.descuento) * existente.cantidad;
                    }
                } else {
                    existente.subtotal = existente.precio * existente.cantidad;
                }
            } else {
                this.pedido.push({
                    producto_id:  producto.id,
                    nombre,
                    precio:       producto.precio,
                    descuento,
                    cantidad,
                    subtotal,
                    es_promo:     !!promo,
                    promocion_id: promo ? promo.id : null,
                    tamanio_id:   tamanioId ?? null,
                });
            }

            await fetch(`/pedidos/${this.pedidoId}/agregar`, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({
                    producto_id:  producto.id,
                    cantidad,
                    promocion_id: promo ? promo.id : null,
                    precio:       producto.precio,
                    tamanio_id:   tamanioId ?? null,
                }),
            });
        },

        async aumentar(item) {
            item.cantidad++;
            item.subtotal = (item.precio - item.descuento) * item.cantidad;
            await fetch(`/pedidos/${this.pedidoId}/cantidad`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ producto_id: item.producto_id, cantidad: item.cantidad, tamanio_id: item.tamanio_id ?? null }),
            });
        },

        async disminuir(item) {
            if (item.cantidad > 1) {
                item.cantidad--;
                item.subtotal = (item.precio - item.descuento) * item.cantidad;
                await fetch(`/pedidos/${this.pedidoId}/cantidad`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ producto_id: item.producto_id, cantidad: item.cantidad, tamanio_id: item.tamanio_id ?? null }),
                });
            } else {
                this.eliminar(item);
            }
        },

        async eliminar(item) {
            this.pedido = this.pedido.filter(p =>
                this.claveItem(p.producto_id, p.promocion_id, p.tamanio_id) !==
                this.claveItem(item.producto_id, item.promocion_id, item.tamanio_id)
            );
            await fetch(`/pedidos/${this.pedidoId}/eliminar`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ producto_id: item.producto_id, tamanio_id: item.tamanio_id ?? null }),
            });
        },
    }
}
</script>

@endsection