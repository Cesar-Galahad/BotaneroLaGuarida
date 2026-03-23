<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket — Mesa {{ $pedido->mesa->numero }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; font-size: 12px; width: 300px; margin: 0 auto; padding: 10px; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        .row { display: flex; justify-content: space-between; margin: 3px 0; }
        .total-row { display: flex; justify-content: space-between; font-size: 14px; font-weight: bold; margin: 4px 0; }
        .logo { font-size: 20px; font-weight: bold; }
        .small { font-size: 10px; color: #555; }
        .tag { font-size: 10px; color: #888; font-style: italic; }
        @media print {
            .no-print { display: none; }
            body { width: 100%; }
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="center" style="margin-bottom: 10px;">
        <p class="logo">LA GUARIDA</p>
        <p class="small">Centro Botanero</p>
        <p class="small">{{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="divider"></div>

    {{-- Info pedido --}}
    <div style="margin-bottom: 6px;">
        <div class="row">
            <span>Mesa:</span>
            <span class="bold">{{ $pedido->mesa->numero }}</span>
        </div>
        <div class="row">
            <span>Folio:</span>
            <span>#{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="row">
            <span>Atendido por:</span>
            <span>{{ $pedido->empleado->nombre ?? '—' }}</span>
        </div>
        @if($pedido->cliente)
        <div class="row">
            <span>Cliente:</span>
            <span>{{ $pedido->cliente->nombre }} {{ $pedido->cliente->apellidop }}</span>
        </div>
        @endif
    </div>

    <div class="divider"></div>

    {{-- Productos --}}
    @foreach($pedido->detalles as $detalle)
    @php
        // Etiqueta del tamaño
        $tamanio = $detalle->tamanio;
        $etiquetaTamanio = $tamanio
            ? (in_array($tamanio->unidad, ['vaso', 'litro'])
                ? ucfirst($tamanio->unidad)
                : $tamanio->cantidad . ' ' . $tamanio->unidad)
            : null;

        // Detectar si es canje (precio_unitario = 0)
        $esCanje = (float) $detalle->precio_unitario === 0.0;
    @endphp
    <div style="margin-bottom: 4px;">
        <div class="row">
            <span class="bold">
                {{ $detalle->producto->nombre }}
                @if($etiquetaTamanio) ({{ $etiquetaTamanio }}) @endif
                @if($esCanje) <span class="tag">[Canje]</span> @endif
            </span>
        </div>
        @if($esCanje)
        <div class="row">
            <span>{{ $detalle->cantidad }} x Gratis</span>
            <span>$0.00</span>
        </div>
        @else
        <div class="row">
            <span>{{ $detalle->cantidad }} x ${{ number_format($detalle->precio_unitario, 2) }}</span>
            <span>${{ number_format($detalle->precio_unitario * $detalle->cantidad, 2) }}</span>
        </div>
        @if($detalle->descuento_aplicado > 0)
        <div class="row small">
            <span>Descuento:</span>
            <span>-${{ number_format($detalle->descuento_aplicado, 2) }}</span>
        </div>
        @endif
        @endif
    </div>
    @endforeach

    <div class="divider"></div>

    {{-- Total --}}
    @php
        $total = $pedido->detalles->sum(fn($d) => $d->subtotal);
        $pago  = $pedido->pagos->first();
    @endphp

    <div class="total-row">
        <span>TOTAL:</span>
        <span>${{ number_format($total, 2) }}</span>
    </div>

    @if($pago)
    <div class="row">
        <span>Método de pago:</span>
        <span>{{ ucfirst($pago->metodo_pago) }}</span>
    </div>
    @endif

    <div class="divider"></div>

    {{-- Puntos --}}
    @if($pedido->cliente)
    <div class="center small" style="margin: 6px 0;">
        <p>Puntos acumulados en esta visita:</p>
        <p class="bold">+{{ (int) round($total * 0.05) }} puntos</p>
        <p>Total de puntos: {{ $pedido->cliente->puntos }}</p>
    </div>
    <div class="divider"></div>
    @endif

    {{-- Footer --}}
    <div class="center small" style="margin-top: 8px;">
        <p>¡Gracias por tu visita!</p>
        <p>La Guarida — Centro Botanero</p>
    </div>

    {{-- Botones --}}
    <div class="no-print" style="margin-top: 16px; text-align: center;">
        <button onclick="window.print()"
                style="background:#ea0000; color:white; border:none; padding:8px 24px; border-radius:8px; cursor:pointer; font-size:13px;">
            Imprimir ticket
        </button>
        <button onclick="window.close()"
                style="background:#e5e7eb; color:#374151; border:none; padding:8px 24px; border-radius:8px; cursor:pointer; font-size:13px; margin-left:8px;">
            Cerrar
        </button>
    </div>

</body>
</html>