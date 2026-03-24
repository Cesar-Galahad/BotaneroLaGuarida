@extends('layouts.app')

@section('titulo', 'Bitácora del Sistema')

@section('content')

{{-- Alpine store global para el modal --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('modal', {
            open: false,
            reg: {},
            abrir(data) {
                this.reg = data;
                this.open = true;
            },
            cerrar() {
                this.open = false;
            }
        });
    });
</script>

{{-- ── MODAL ── --}}
<div x-data
     x-show="$store.modal.open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center">

    {{-- Fondo oscuro --}}
    <div class="absolute inset-0 bg-black/50" @click="$store.modal.cerrar()"></div>

    {{-- Contenido --}}
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 p-6 z-10"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">

        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-bold text-gray-800">Detalle del registro</h3>
            <button @click="$store.modal.cerrar()" class="text-gray-400 hover:text-gray-600 transition text-2xl leading-none">&times;</button>
        </div>

        <div class="space-y-3 text-sm">
            <div class="flex items-center gap-3">
                <span class="text-xs font-semibold text-gray-400 uppercase w-24 shrink-0">Operación</span>
                <span x-html="$store.modal.reg.badge"></span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-semibold text-gray-400 uppercase w-24 shrink-0">ID Registro</span>
                <span class="text-gray-700" x-text="$store.modal.reg.id_registro"></span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-semibold text-gray-400 uppercase w-24 shrink-0">Empleado</span>
                <span class="text-gray-700" x-text="$store.modal.reg.empleado"></span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-semibold text-gray-400 uppercase w-24 shrink-0">Fecha</span>
                <span class="text-gray-700" x-text="$store.modal.reg.fecha"></span>
            </div>
            <div class="flex flex-col gap-1 pt-1">
                <span class="text-xs font-semibold text-gray-400 uppercase">Descripción completa</span>
                <div class="bg-gray-50 rounded-lg p-3 text-gray-700 leading-relaxed border border-gray-100 whitespace-pre-wrap"
                     x-text="$store.modal.reg.descripcion"></div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button @click="$store.modal.cerrar()"
                    class="bg-gray-100 text-gray-700 text-sm px-5 py-2 rounded-lg hover:bg-gray-200 transition">
                Cerrar
            </button>
        </div>
    </div>
</div>

<div class="mb-6">
    <h3 class="text-xl font-bold text-gray-800">Bitácora del Sistema</h3>
    <p class="text-sm text-gray-500 mt-1">Historial de operaciones registradas automáticamente</p>
</div>

{{-- Pestañas --}}
<div class="flex gap-2 mb-6 border-b border-gray-200">
    <a href="{{ route('bitacora.index', array_merge(request()->except(['tab','page_prod','page_ped']), ['tab' => 'productos'])) }}"
       class="px-5 py-2.5 text-sm font-medium rounded-t-lg transition
              {{ $tab === 'productos'
                  ? 'bg-white border border-b-white border-gray-200 text-red-600 -mb-px'
                  : 'text-gray-500 hover:text-gray-700' }}">
        📦 Productos
    </a>
    <a href="{{ route('bitacora.index', array_merge(request()->except(['tab','page_prod','page_ped']), ['tab' => 'pedidos'])) }}"
       class="px-5 py-2.5 text-sm font-medium rounded-t-lg transition
              {{ $tab === 'pedidos'
                  ? 'bg-white border border-b-white border-gray-200 text-red-600 -mb-px'
                  : 'text-gray-500 hover:text-gray-700' }}">
        🧾 Pedidos
    </a>
</div>

{{-- Filtros --}}
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('bitacora.index') }}" class="flex flex-wrap gap-4 items-end">
        <input type="hidden" name="tab" value="{{ $tab }}">

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Operación</label>
            <select name="operacion"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400">
                <option value="">Todas</option>
                <option value="INSERT" {{ request('operacion') === 'INSERT' ? 'selected' : '' }}>INSERT</option>
                <option value="UPDATE" {{ request('operacion') === 'UPDATE' ? 'selected' : '' }}>UPDATE</option>
                <option value="DELETE" {{ request('operacion') === 'DELETE' ? 'selected' : '' }}>DELETE</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Fecha inicio</label>
            <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}"
                   class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Fecha fin</label>
            <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}"
                   class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400">
        </div>

        <div class="flex gap-2">
            <button type="submit"
                    class="bg-red-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-red-700 transition">
                Filtrar
            </button>
            <a href="{{ route('bitacora.index', ['tab' => $tab]) }}"
               class="bg-gray-100 text-gray-700 text-sm px-4 py-2 rounded-lg hover:bg-gray-200 transition">
                Limpiar
            </a>
        </div>
    </form>
</div>

{{-- ── PESTAÑA PRODUCTOS ── --}}
@if($tab === 'productos')
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <span class="text-sm font-semibold text-gray-700">Movimientos en Productos</span>
        <span class="text-xs text-gray-400">{{ $registrosProductos->total() }} registros</span>
    </div>

    @if($registrosProductos->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <p class="text-4xl mb-3">📭</p>
            <p class="text-sm">Sin registros en esta sección</p>
        </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500 tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">#</th>
                    <th class="px-6 py-3 text-left">Operación</th>
                    <th class="px-6 py-3 text-left">ID Registro</th>
                    <th class="px-6 py-3 text-left">Empleado</th>
                    <th class="px-6 py-3 text-left">Descripción</th>
                    <th class="px-6 py-3 text-left">Fecha</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50" x-data>
                @foreach($registrosProductos as $reg)
                @php
                    $badge = match($reg->operacion) {
                        'INSERT' => '<span class=\"inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full\">＋ INSERT</span>',
                        'UPDATE' => '<span class=\"inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 text-xs font-semibold px-2.5 py-1 rounded-full\">✎ UPDATE</span>',
                        default  => '<span class=\"inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs font-semibold px-2.5 py-1 rounded-full\">✕ DELETE</span>',
                    };
                    $badgeReal = match($reg->operacion) {
                        'INSERT' => '<span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full">＋ INSERT</span>',
                        'UPDATE' => '<span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 text-xs font-semibold px-2.5 py-1 rounded-full">✎ UPDATE</span>',
                        default  => '<span class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs font-semibold px-2.5 py-1 rounded-full">✕ DELETE</span>',
                    };
                    $empleado    = trim($reg->empleado_nombre) ?: '—';
                    $descripcion = $reg->descripcion;
                    $fecha       = \Carbon\Carbon::parse($reg->fecha)->format('d/m/Y H:i');
                @endphp
                <tr class="hover:bg-gray-50 transition cursor-pointer"
                    x-data
                    @click="$store.modal.abrir({
                        badge:       {{ json_encode($badge) }},
                        id_registro: {{ json_encode((string)$reg->id_registro) }},
                        empleado:    {{ json_encode($empleado) }},
                        fecha:       {{ json_encode($fecha) }},
                        descripcion: {{ json_encode($descripcion) }}
                    })">
                    <td class="px-6 py-4 text-gray-400">{{ $reg->id }}</td>
                    <td class="px-6 py-4">{!! $badgeReal !!}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $reg->id_registro }}</td>
                    <td class="px-6 py-4 text-gray-700">{{ $empleado }}</td>
                    <td class="px-6 py-4 text-gray-600 max-w-xs truncate">{{ $descripcion }}</td>
                    <td class="px-6 py-4 text-gray-500 whitespace-nowrap">{{ $fecha }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($registrosProductos->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $registrosProductos->links() }}
    </div>
    @endif
    @endif
</div>
@endif

{{-- ── PESTAÑA PEDIDOS ── --}}
@if($tab === 'pedidos')
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <span class="text-sm font-semibold text-gray-700">Movimientos en Pedidos</span>
        <span class="text-xs text-gray-400">{{ $registrosPedidos->total() }} registros</span>
    </div>

    @if($registrosPedidos->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <p class="text-4xl mb-3">📭</p>
            <p class="text-sm">Sin registros en esta sección</p>
        </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500 tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">#</th>
                    <th class="px-6 py-3 text-left">Operación</th>
                    <th class="px-6 py-3 text-left">ID Pedido</th>
                    <th class="px-6 py-3 text-left">Empleado</th>
                    <th class="px-6 py-3 text-left">Descripción</th>
                    <th class="px-6 py-3 text-left">Fecha</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50" x-data>
                @foreach($registrosPedidos as $reg)
                @php
                    $badge = match($reg->operacion) {
                        'INSERT' => '<span class=\"inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full\">＋ INSERT</span>',
                        'UPDATE' => '<span class=\"inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 text-xs font-semibold px-2.5 py-1 rounded-full\">✎ UPDATE</span>',
                        default  => '<span class=\"inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs font-semibold px-2.5 py-1 rounded-full\">✕ DELETE</span>',
                    };
                    $badgeReal = match($reg->operacion) {
                        'INSERT' => '<span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full">＋ INSERT</span>',
                        'UPDATE' => '<span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 text-xs font-semibold px-2.5 py-1 rounded-full">✎ UPDATE</span>',
                        default  => '<span class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs font-semibold px-2.5 py-1 rounded-full">✕ DELETE</span>',
                    };
                    $empleado    = trim($reg->empleado_nombre) ?: '—';
                    $descripcion = $reg->descripcion;
                    $fecha       = \Carbon\Carbon::parse($reg->fecha)->format('d/m/Y H:i');
                @endphp
                <tr class="hover:bg-gray-50 transition cursor-pointer"
                    x-data
                    @click="$store.modal.abrir({
                        badge:       {{ json_encode($badge) }},
                        id_registro: {{ json_encode((string)$reg->id_registro) }},
                        empleado:    {{ json_encode($empleado) }},
                        fecha:       {{ json_encode($fecha) }},
                        descripcion: {{ json_encode($descripcion) }}
                    })">
                    <td class="px-6 py-4 text-gray-400">{{ $reg->id }}</td>
                    <td class="px-6 py-4">{!! $badgeReal !!}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $reg->id_registro }}</td>
                    <td class="px-6 py-4 text-gray-700">{{ $empleado }}</td>
                    <td class="px-6 py-4 text-gray-600 max-w-xs truncate">{{ $descripcion }}</td>
                    <td class="px-6 py-4 text-gray-500 whitespace-nowrap">{{ $fecha }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($registrosPedidos->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $registrosPedidos->links() }}
    </div>
    @endif
    @endif
</div>
@endif

@endsection
