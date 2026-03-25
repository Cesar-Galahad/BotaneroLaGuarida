@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto">

    <!-- Título -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Historial de Tickets</h1>
        <p class="text-sm text-gray-500">Tickets generados</p>
    </div>

    <!-- Tabla -->
    <div class="bg-white shadow-md rounded-xl overflow-hidden">

        <table class="w-full text-sm text-left">
            <thead style="background-color:#1d1d1b; color:white;">
                <tr>
                    <th class="px-4 py-3">Folio</th>
                    <th class="px-4 py-3">Mesa</th>
                    <th class="px-4 py-3">Empleado</th>
                    <th class="px-4 py-3">Cliente</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Fecha</th>
                    <th class="px-4 py-3 text-center">Acción</th>
                </tr>
            </thead>

            <tbody>
                @forelse($pedidos as $pedido)

                @php
                    $total = $pedido->detalles->sum(fn($d) => $d->subtotal);
                @endphp

                <tr class="border-b hover:bg-gray-50">

                    <td class="px-4 py-3 font-semibold">
                        #{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}
                    </td>

                    <td class="px-4 py-3">
                        Mesa {{ $pedido->mesa->numero ?? '—' }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $pedido->empleado->nombre ?? '—' }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $pedido->cliente
                            ? $pedido->cliente->nombre . ' ' . $pedido->cliente->apellidop
                            : 'Sin cliente' }}
                    </td>

                    <td class="px-4 py-3 font-bold text-green-600">
                        ${{ number_format($total, 2) }}
                    </td>

                    <td class="px-4 py-3 text-gray-500">
                        {{ \Carbon\Carbon::parse($pedido->created_at)->format('d/m/Y H:i') }}
                    </td>

                    <td class="px-4 py-3 text-center">

                        <a href="{{ route('pedidos.ticket', $pedido->id) }}"
                           target="_blank"
                           class="px-3 py-1 rounded-lg text-white text-xs font-semibold"
                           style="background-color:#ea0000;"
                           onmouseover="this.style.backgroundColor='#5d0c03'"
                           onmouseout="this.style.backgroundColor='#ea0000'">
                            Ver Ticket
                        </a>

                    </td>

                </tr>

                @empty
                <tr>
                    <td colspan="7" class="text-center py-6 text-gray-500">
                        No hay tickets registrados
                    </td>
                </tr>
                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection