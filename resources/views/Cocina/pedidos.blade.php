@extends('layouts.app')

@section('titulo', 'Órdenes en cocina')

@section('content')

<div id="ordenes-container">
    @include('Cocina.partials.ordenes', ['pedidos' => $pedidos])
</div>

@endsection

@push('scripts')
<script>
    // Polling cada 15 segundos
    setInterval(() => {
        fetch('/cocina/pedidos/actualizar')
            .then(res => res.text())
            .then(html => {
                document.getElementById('ordenes-container').innerHTML = html;
            });
    }, 15000);

    // Marcar orden como lista
    function marcarLista(pedidoId) {
        if (!confirm('¿Marcar esta orden como lista?')) return;
        fetch(`/cocina/pedidos/${pedidoId}/lista`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(() => {
            document.getElementById(`orden-${pedidoId}`).remove();
        });
    }
</script>
@endpush