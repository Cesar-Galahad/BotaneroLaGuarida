<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>La Guarida</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#f5f5f5]">

<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 flex flex-col sticky top-0 h-screen overflow-y-auto"
           style="background-color: #1d1d1b;">

        <!-- Logo -->
        <div class="p-6 flex items-center gap-3" style="border-bottom: 1px solid #2e2e2c;">
            <img src="/imagenes/LogoGuarida.png" class="w-12 h-12 object-contain">
            <div>
                <h1 class="text-xl font-bold" style="color: #ea0000;">La Guarida</h1>
                <p class="text-xs" style="color: #9ca3af;">Panel Administrativo</p>
            </div>
        </div>

        <!-- Menú -->
        <nav class="flex-1 p-4 space-y-1"
             x-data="{ active: '{{ request()->routeIs('bitacora.*') ? 'reportes' : (request()->routeIs('mesas.*') || request()->routeIs('pedidos.*') ? 'mesas' : 'null') }}' }">

            @php $rol = Auth::guard('empleado')->user()->rol->nombre ?? ''; @endphp

            {{-- Dashboard Admin --}}
            @if($rol === 'Administrador')
            <a href="{{ route('dashboard') }}"
               class="block px-4 py-2 rounded-lg text-sm font-medium transition"
               style="{{ request()->routeIs('dashboard') ? 'background-color:#f4a400; color:#1d1d1b;' : 'color:#d1d5db;' }}"
               @if(!request()->routeIs('dashboard'))
               onmouseover="this.style.backgroundColor='#2e2e2c'; this.style.color='#ffffff'"
               onmouseout="this.style.backgroundColor=''; this.style.color='#d1d5db'"
               @endif>
                Dashboard
            </a>

            {{-- Dashboard Mesero --}}
            @elseif($rol === 'Mesero')
            <a href="{{ route('mesero.dashboard') }}"
               class="block px-4 py-2 rounded-lg text-sm font-medium transition"
               style="{{ request()->routeIs('mesero.dashboard') ? 'background-color:#f4a400; color:#1d1d1b;' : 'color:#d1d5db;' }}"
               @if(!request()->routeIs('mesero.dashboard'))
               onmouseover="this.style.backgroundColor='#2e2e2c'; this.style.color='#ffffff'"
               onmouseout="this.style.backgroundColor=''; this.style.color='#d1d5db'"
               @endif>
                Dashboard
            </a>
            @endif

            {{-- Mesas (dropdown) --}}
            @if(in_array($rol, ['Mesero']))
            @php $mesasActivo = request()->routeIs('mesas.*') || request()->routeIs('pedidos.*'); @endphp
            <div>
                <button @click="active = active === 'mesas' ? null : 'mesas'"
                        class="w-full text-left px-4 py-2 rounded-lg text-sm font-medium transition flex justify-between items-center"
                        style="{{ $mesasActivo ? 'background-color:#f4a400; color:#1d1d1b;' : 'color:#d1d5db;' }}"
                        @if(!$mesasActivo)
                        onmouseover="this.style.backgroundColor='#2e2e2c'; this.style.color='#ffffff'"
                        onmouseout="this.style.backgroundColor=''; this.style.color='#d1d5db'"
                        @endif>
                    Mesas
                    <span x-text="active === 'mesas' ? '▲' : '▼'" class="text-xs"
                          style="color: {{ $mesasActivo ? '#1d1d1b' : '#f4a400' }};"></span>
                </button>
                <div x-show="active === 'mesas'" x-cloak x-transition class="ml-4 mt-1 space-y-1">
                    <a href="{{ route('mesas.index') }}"
                       class="block px-4 py-1.5 text-sm rounded transition"
                       style="color:#9ca3af;"
                       onmouseover="this.style.color='#f4a400'"
                       onmouseout="this.style.color='#9ca3af'">Listar</a>
                    <a href="{{ route('pedidos.index') }}"
                       class="block px-4 py-1.5 text-sm rounded transition"
                       style="color:#9ca3af;"
                       onmouseover="this.style.color='#f4a400'"
                       onmouseout="this.style.color='#9ca3af'">Pedidos abiertos</a>
                </div>
            </div>
            @endif

            {{-- Cocina --}}
            @if($rol === 'Cocinero')
            <a href="{{ route('cocina.pedidos') }}"
               class="block px-4 py-2 rounded-lg text-sm font-medium transition"
               style="{{ request()->routeIs('cocina.*') ? 'background-color:#f4a400; color:#1d1d1b;' : 'color:#d1d5db;' }}"
               @if(!request()->routeIs('cocina.*'))
               onmouseover="this.style.backgroundColor='#2e2e2c'; this.style.color='#ffffff'"
               onmouseout="this.style.backgroundColor=''; this.style.color='#d1d5db'"
               @endif>
                Órdenes
            </a>
            @endif

            {{-- Productos --}}
            @if(in_array($rol, ['Administrador', 'Mesero', 'Cocinero']))
            <a href="{{ route('productos.index') }}"
               class="block px-4 py-2 rounded-lg text-sm font-medium transition"
               style="{{ request()->routeIs('productos.*') ? 'background-color:#f4a400; color:#1d1d1b;' : 'color:#d1d5db;' }}"
               @if(!request()->routeIs('productos.*'))
               onmouseover="this.style.backgroundColor='#2e2e2c'; this.style.color='#ffffff'"
               onmouseout="this.style.backgroundColor=''; this.style.color='#d1d5db'"
               @endif>
                Productos
            </a>
            @endif
            {{-- Tamaños --}}
            @if($rol === 'Administrador')
            <a href="{{ route('tamanios.index') }}"
            class="block px-4 py-2 rounded-lg text-sm font-medium transition"
            style="{{ request()->routeIs('tamanios.*') ? 'background-color:#f4a400; color:#1d1d1b;' : 'color:#d1d5db;' }}"
            @if(!request()->routeIs('tamanios.*'))
            onmouseover="this.style.backgroundColor='#2e2e2c'; this.style.color='#ffffff'"
            onmouseout="this.style.backgroundColor=''; this.style.color='#d1d5db'"
            @endif>
                Tamaños
            </a>
            @endif
            {{-- Categorías --}}
            @if(in_array($rol, ['Administrador', 'Mesero', 'Cocinero']))
            <a href="{{ route('categorias.index') }}"
               class="block px-4 py-2 rounded-lg text-sm font-medium transition"
               style="{{ request()->routeIs('categorias.*') ? 'background-color:#f4a400; color:#1d1d1b;' : 'color:#d1d5db;' }}"
               @if(!request()->routeIs('categorias.*'))
               onmouseover="this.style.backgroundColor='#2e2e2c'; this.style.color='#ffffff'"
               onmouseout="this.style.backgroundColor=''; this.style.color='#d1d5db'"
               @endif>
                Categorías
            </a>
            @endif

            {{-- Promociones --}}
            @if(in_array($rol, ['Administrador', 'Mesero', 'Cocinero']))
            <a href="{{ route('promociones.index') }}"
               class="block px-4 py-2 rounded-lg text-sm font-medium transition"
               style="{{ request()->routeIs('promociones.*') ? 'background-color:#f4a400; color:#1d1d1b;' : 'color:#d1d5db;' }}"
               @if(!request()->routeIs('promociones.*'))
               onmouseover="this.style.backgroundColor='#2e2e2c'; this.style.color='#ffffff'"
               onmouseout="this.style.backgroundColor=''; this.style.color='#d1d5db'"
               @endif>
                Promociones
            </a>
            @endif

            {{-- Canjes --}}
            @if($rol === 'Administrador')
            <a href="{{ route('canjes.index') }}"
            class="block px-4 py-2 rounded-lg text-sm font-medium transition"
            style="{{ request()->routeIs('canjes.*') ? 'background-color:#f4a400; color:#1d1d1b;' : 'color:#d1d5db;' }}"
            @if(!request()->routeIs('canjes.*'))
            onmouseover="this.style.backgroundColor='#2e2e2c'; this.style.color='#ffffff'"
            onmouseout="this.style.backgroundColor=''; this.style.color='#d1d5db'"
            @endif>
                Productos Canje
            </a>
            @endif
            {{-- Clientes --}}
            @if(in_array($rol, ['Administrador', 'Mesero']))
            <a href="{{ route('clientes.index') }}"
               class="block px-4 py-2 rounded-lg text-sm font-medium transition"
               style="{{ request()->routeIs('clientes.*') ? 'background-color:#f4a400; color:#1d1d1b;' : 'color:#d1d5db;' }}"
               @if(!request()->routeIs('clientes.*'))
               onmouseover="this.style.backgroundColor='#2e2e2c'; this.style.color='#ffffff'"
               onmouseout="this.style.backgroundColor=''; this.style.color='#d1d5db'"
               @endif>
                Clientes
            </a>
            @endif

            {{-- Empleados --}}
            @if($rol === 'Administrador')
            <a href="{{ route('empleados.index') }}"
               class="block px-4 py-2 rounded-lg text-sm font-medium transition"
               style="{{ request()->routeIs('empleados.*') ? 'background-color:#f4a400; color:#1d1d1b;' : 'color:#d1d5db;' }}"
               @if(!request()->routeIs('empleados.*'))
               onmouseover="this.style.backgroundColor='#2e2e2c'; this.style.color='#ffffff'"
               onmouseout="this.style.backgroundColor=''; this.style.color='#d1d5db'"
               @endif>
                Empleados
            </a>
            @endif

            {{-- Reportes (dropdown) --}}
            @if($rol === 'Administrador')
            @php $reportesActivo = request()->routeIs('bitacora.*'); @endphp
            <div>
                <button @click="active = active === 'reportes' ? null : 'reportes'"
                        class="w-full text-left px-4 py-2 rounded-lg text-sm font-medium transition flex justify-between items-center"
                        style="{{ $reportesActivo ? 'background-color:#f4a400; color:#1d1d1b;' : 'color:#d1d5db;' }}"
                        @if(!$reportesActivo)
                        onmouseover="this.style.backgroundColor='#2e2e2c'; this.style.color='#ffffff'"
                        onmouseout="this.style.backgroundColor=''; this.style.color='#d1d5db'"
                        @endif>
                    Reportes
                    <span x-text="active === 'reportes' ? '▲' : '▼'" class="text-xs"
                          style="color: {{ $reportesActivo ? '#1d1d1b' : '#f4a400' }};"></span>
                </button>
                <div x-show="active === 'reportes'" x-cloak x-transition class="ml-4 mt-1 space-y-1">
                    <a href="{{ route('bitacora.index', ['tab' => 'productos']) }}"
                       class="block px-4 py-1.5 text-sm rounded transition"
                       style="color: {{ request()->routeIs('bitacora.index') && request('tab', 'productos') === 'productos' ? '#f4a400' : '#9ca3af' }};"
                       onmouseover="this.style.color='#f4a400'"
                       onmouseout="this.style.color='#9ca3af'">
                        Bitácora Productos
                    </a>
                    <a href="{{ route('bitacora.index', ['tab' => 'pedidos']) }}"
                       class="block px-4 py-1.5 text-sm rounded transition"
                       style="color: {{ request()->routeIs('bitacora.index') && request('tab') === 'pedidos' ? '#f4a400' : '#9ca3af' }};"
                       onmouseover="this.style.color='#f4a400'"
                       onmouseout="this.style.color='#9ca3af'">
                        Bitácora Pedidos
                    </a>
                </div>
            </div>
            @endif

        </nav>

        <!-- Aviso de privacidad -->
        <div class="px-4 py-3" style="border-top: 1px solid #2e2e2c;">
            <a href="{{ route('privacidad') }}"
            class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs transition"
            style="color: #f4a400;"
            onmouseover="this.style.backgroundColor='#2e2e2c'; this.style.color='#fbbb26'"
            onmouseout="this.style.backgroundColor=''; this.style.color='#f4a400'">
                
                Aviso de privacidad
            </a>
        </div>

        <!-- Usuario logueado -->
        <div class="p-4" style="border-top: 1px solid #2e2e2c;">
            <div class="flex items-center gap-3 mb-3">
                <a href="{{ route('perfil') }}" class="flex items-center gap-3 mb-3 rounded-lg p-1 transition"
                    onmouseover="this.style.backgroundColor='#2e2e2c'"
                    onmouseout="this.style.backgroundColor=''">
                    <div class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center shrink-0"
                        style="background-color: #ea0000;">
                        @if(Auth::guard('empleado')->user()->imagen)
                            <img src="{{ asset('storage/' . Auth::guard('empleado')->user()->imagen) }}"
                                class="w-full h-full object-cover">
                        @else
                            <span class="text-white text-sm font-bold">
                                {{ strtoupper(substr(Auth::guard('empleado')->user()->nombre, 0, 1)) }}{{ strtoupper(substr(Auth::guard('empleado')->user()->apellidop, 0, 1)) }}
                            </span>
                        @endif
                    </div>
                
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-white truncate">
                            {{ Auth::guard('empleado')->user()->nombre }} {{ Auth::guard('empleado')->user()->apellidop }}
                        </p>
                        <p class="text-xs truncate" style="color: #9ca3af;">
                            {{ Auth::guard('empleado')->user()->rol->nombre ?? '' }}
                        </p>
                    </div>
                </a>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full text-left text-sm transition"
                        style="color: #ea0000;"
                        onmouseover="this.style.color='#fbbb26'"
                        onmouseout="this.style.color='#ea0000'">
                    Cerrar sesión
                </button>
            </form>
        </div>

    </aside>

    <!-- Contenido sin header -->
    <main class="flex-1 p-6">
        @yield('content')
    </main>

</div>
{{-- Modal de confirmación global --}}
<div x-data="{
        modalConfirm: false,
        mensaje: '',
        formId: '',
        abrir(mensaje, formId) {
            this.mensaje = mensaje;
            this.formId = formId;
            this.modalConfirm = true;
        },
        confirmar() {
            document.getElementById(this.formId).submit();
            this.modalConfirm = false;
        }
     }"
     x-show="modalConfirm"
     x-cloak
     @abrir-confirm.window="abrir($event.detail.mensaje, $event.detail.formId)"
     @keydown.escape.window="modalConfirm = false"
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
                <h4 class="font-bold text-gray-800">¿Estás seguro?</h4>
                <p class="text-sm text-gray-500 mt-0.5" x-text="mensaje"></p>
            </div>
        </div>

        <div class="flex gap-3 justify-end">
            <button @click="modalConfirm = false"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg transition text-sm">
                Cancelar
            </button>
            <button @click="confirmar()"
                    class="text-white font-semibold px-4 py-2 rounded-lg transition text-sm"
                    style="background-color: #ea0000;"
                    onmouseover="this.style.backgroundColor='#5d0c03'"
                    onmouseout="this.style.backgroundColor='#ea0000'">
                Confirmar
            </button>
        </div>
    </div>
</div>

@stack('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>