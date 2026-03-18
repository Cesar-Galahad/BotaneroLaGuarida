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
<body class="bg-gray-100">
 
<div class="flex min-h-screen">
 
    <!-- Sidebar para la navegacion, aqui colque los dropdown que no se les olvide -->
    <aside class="w-64 bg-gray-900 text-gray-200 flex flex-col sticky top-0 h-screen overflow-y-auto">
 
        <!-- Logo -->
        <div class="p-6 border-b border-gray-700 flex items-center gap-3">
            <img src="/imagenes/LogoGuarida.png" class="w-15 h-15 object-contain">
            <div>
                <h1 class="text-xl font-bold text-red-600">La Guarida</h1>
                <p class="text-xs text-gray-400">Panel Administrativo</p>
            </div>
        </div>
 
        <!-- Menú -->
        <nav class="flex-1 p-4 space-y-2" x-data="{ active: '{{ request()->routeIs('bitacora.*') ? 'reportes' : 'null' }}' }">
 
        @php $rol = Auth::guard('empleado')->user()->rol->nombre ?? ''; @endphp
 
        {{-- Dashboard --}}
        @if($rol === 'Administrador')
        <a href="{{ route('dashboard') }}"
        class="block px-4 py-2 rounded hover:bg-gray-800">
            Dashboard
        </a>
        @elseif($rol === 'Mesero')
        <a href="{{ route('mesero.dashboard') }}"
        class="block px-4 py-2 rounded hover:bg-gray-800">
            Dashboard
        </a>
        @endif
 
        {{-- Mesas --}}
        @if(in_array($rol, ['Mesero']))
        <div>
            <button @click="active = active === 'mesas' ? null : 'mesas'"
                    class="w-full text-left px-4 py-2 rounded hover:bg-gray-800 flex justify-between items-center">
                Mesas
                <span x-text="active === 'mesas' ? '▲' : '▼'"></span>
            </button>
            <div x-show="active === 'mesas'" x-cloak x-transition class="ml-4 mt-2 space-y-1">
                <a href="{{ route('mesas.index') }}"   class="block px-4 py-1 text-sm hover:text-red-500">Listar</a>
                <a href="{{ route('pedidos.index') }}" class="block px-4 py-1 text-sm hover:text-red-500">Pedidos abiertos</a>
            </div>
        </div>
        @endif
 
        {{-- Cocina --}}
        @if($rol === 'Cocinero')
        <a href="{{ route('cocina.pedidos') }}"
        class="block px-4 py-2 rounded hover:bg-gray-800">
            Órdenes
        </a>
        @endif
 
        {{-- Productos --}}
        @if(in_array($rol, ['Administrador', 'Mesero', 'Cocinero']))
        <a href="{{ route('productos.index') }}"
        class="block px-4 py-2 rounded hover:bg-gray-800">
            Productos
        </a>
        @endif
 
        {{-- Categorías --}}
        @if(in_array($rol, ['Administrador', 'Mesero', 'Cocina']))
        <a href="{{ route('categorias.index') }}"
        class="block px-4 py-2 rounded hover:bg-gray-800">
            Categorías
        </a>
        @endif
 
        {{-- Promociones --}}
        @if(in_array($rol, ['Administrador', 'Mesero', 'Cocina']))
        <a href="{{ route('promociones.index') }}"
        class="block px-4 py-2 rounded hover:bg-gray-800">
            Promociones
        </a>
        @endif
 
        {{-- Clientes --}}
        @if(in_array($rol, ['Administrador', 'Mesero']))
        <a href="{{ route('clientes.index') }}"
        class="block px-4 py-2 rounded hover:bg-gray-800">
            Clientes
        </a>
        @endif
 
        {{-- Empleados (solo admin) --}}
        @if($rol === 'Administrador')
        <a href="{{ route('empleados.index') }}"
        class="block px-4 py-2 rounded hover:bg-gray-800">
            Empleados
        </a>
        @endif
 
        {{-- Reportes (solo admin) --}}
        @if($rol === 'Administrador')
        <div>
            <button @click="active = active === 'reportes' ? null : 'reportes'"
                    class="w-full text-left px-4 py-2 rounded hover:bg-gray-800 flex justify-between items-center">
                Reportes
                <span x-text="active === 'reportes' ? '▲' : '▼'" class="text-xs text-gray-400"></span>
            </button>
            <div x-show="active === 'reportes'" x-cloak x-transition class="ml-4 mt-1 space-y-1">
                <a href="{{ route('bitacora.index', ['tab' => 'productos']) }}"
                   class="block px-4 py-1 text-sm hover:text-red-500
                          {{ request()->routeIs('bitacora.index') && request('tab', 'productos') === 'productos' ? 'text-red-500' : '' }}">
                    Bitácora Productos
                </a>
                <a href="{{ route('bitacora.index', ['tab' => 'pedidos']) }}"
                   class="block px-4 py-1 text-sm hover:text-red-500
                          {{ request()->routeIs('bitacora.index') && request('tab') === 'pedidos' ? 'text-red-500' : '' }}">
                    Bitácora Pedidos
                </a>
                {{-- Aquí tu compañero puede agregar más opciones de reportes --}}
            </div>
        </div>
        @endif
 
    </nav>
 
        <!-- Usuario logueado -->
        <div class="p-4 border-t border-gray-700">
            <div class="flex items-center gap-3 mb-3">
 
                {{-- Foto o iniciales --}}
                <div class="w-10 h-10 rounded-full overflow-hidden bg-red-600 flex items-center justify-center shrink-0">
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
                    <p class="text-sm font-semibold text-gray-200 truncate">
                        {{ Auth::guard('empleado')->user()->nombre }} {{ Auth::guard('empleado')->user()->apellidop }}
                    </p>
                    <p class="text-xs text-gray-400 truncate">
                        {{ Auth::guard('empleado')->user()->rol->nombre ?? '' }}
                    </p>
                </div>
            </div>
 
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full text-left text-sm text-red-400 hover:text-red-300 transition">
                    Cerrar sesión
                </button>
            </form>
        </div>
 
    </aside>
 
    <!-- Contenido -->
    <main class="flex-1">
 
        <header class="bg-white shadow p-4 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-700">
                @yield('titulo')
            </h2>
        </header>
 
        <div class="p-6">
            @yield('content')
        </div>
 
    </main>
 
</div>
 
<script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>