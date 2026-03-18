<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @forelse($pedidos as $pedido)
    <div id="orden-{{ $pedido->id }}" class="bg-white rounded-2xl shadow p-5 border-l-4 border-red-500">

        {{-- Header mesa --}}
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Mesa {{ $pedido->mesa->numero }}</h3>
                <p class="text-xs text-gray-400">{{ $pedido->fecha->format('H:i') }} hrs</p>
            </div>
            <span class="bg-red-100 text-red-600 text-xs font-bold px-3 py-1 rounded-full">
                {{ $pedido->detalles->sum('cantidad') }} items
            </span>
        </div>

        {{-- Productos --}}
        <div class="space-y-2 mb-4">
            @foreach($pedido->detalles as $detalle)
            <div class="flex justify-between items-center bg-gray-50 rounded-lg px-3 py-2">
                <span class="text-sm font-medium text-gray-700">
                    {{ $detalle->producto->nombre }}
                    @if($detalle->tamano)
                        <span class="text-xs text-gray-400">({{ $detalle->tamano }})</span>
                    @endif
                </span>
                <span class="bg-red-600 text-white text-xs font-bold w-7 h-7 flex items-center justify-center rounded-full shrink-0">
                    {{ $detalle->cantidad }}
                </span>
            </div>
            @endforeach
        </div>

        {{-- Botón lista --}}
        <button onclick="marcarLista({{ $pedido->id }})"
                class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-lg transition text-sm">
            Orden Lista
        </button>

    </div>
    @empty
    <div class="col-span-3 text-center py-16 text-gray-400">
        Sin órdenes pendientes por ahora.
    </div>
    @endforelse
</div>