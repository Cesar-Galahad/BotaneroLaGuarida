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
    <aside class="w-64 bg-gray-900 text-gray-200 flex flex-col">

        <!-- Logo del negocio -->
        <div class="p-6 border-b border-gray-700 flex items-center gap-3">
            <img src="imagenes/LogoGuarida.png"
                 class="w-20 h-20 object-contain">
            <div>
                <h1 class="text-xl font-bold text-red-600">
                    La Guarida
                </h1>
                <p class="text-xs text-gray-400">
                    Panel Administrativo
                </p>
            </div>
        </div>

        <!-- Menu con dropdowns -->
        <nav class="flex-1 p-4 space-y-2"
             x-data="{ active: null }">

            <!-- Dashboard / principal -->
            <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded hover:bg-gray-800">
                Dashboard
            </a>

            <!-- Mesas -->
            <div>
                <button @click="active = active === 'mesas' ? null : 'mesas'"
                        class="w-full text-left px-4 py-2 rounded hover:bg-gray-800 flex justify-between items-center">
                    Mesas
                    <span x-text="active === 'mesas' ? '▲' : '▼'"></span>
                </button>

                <div x-show="active === 'mesas'"
                     x-cloak
                     x-transition
                     class="ml-4 mt-2 space-y-1">
                    <a href="{{ route('mesas.index') }}" class="block px-4 py-1 text-sm hover:text-red-500">Listar</a>
                    <a href="{{ route('pedidos.index') }}" class="block px-4 py-1 text-sm hover:text-red-500">Abiertos</a>
                </div>
            </div>


            <!-- Productos -->
            <div>
                <button @click="active = active === 'productos' ? null : 'productos'"
                        class="w-full text-left px-4 py-2 rounded hover:bg-gray-800 flex justify-between items-center">
                    Productos
                    <span x-text="active === 'productos' ? '▲' : '▼'"></span>
                </button>

                <div x-show="active === 'productos'"
                     x-cloak
                     x-transition
                     class="ml-4 mt-2 space-y-1">
                    <a href="{{ route('productos.index') }}"  class="block px-4 py-1 text-sm hover:text-red-500">Listar</a>
                    <a href="{{ route('productos.create') }}" class="block px-4 py-1 text-sm hover:text-red-500">Crear</a>
                </div>
            </div>

            <!-- Categorias -->
            <div>
                <button @click="active = active === 'categorias' ? null : 'categorias'"
                        class="w-full text-left px-4 py-2 rounded hover:bg-gray-800 flex justify-between items-center">
                    Categorías
                    <span x-text="active === 'categorias' ? '▲' : '▼'"></span>
                </button>

                <div x-show="active === 'categorias'"
                     x-cloak
                     x-transition
                     class="ml-4 mt-2 space-y-1">
                    <a href="{{ route('categorias.index') }}"  class="block px-4 py-1 text-sm hover:text-red-500">Listar</a>
                    <a href="{{ route('categorias.create') }}" class="block px-4 py-1 text-sm hover:text-red-500">Crear</a>
                </div>
            </div>

            <!-- Promociones -->
            <div>
                <button @click="active = active === 'promociones' ? null : 'promociones'"
                        class="w-full text-left px-4 py-2 rounded hover:bg-gray-800 flex justify-between items-center">
                    Promociones
                    <span x-text="active === 'promociones' ? '▲' : '▼'"></span>
                </button>

                <div x-show="active === 'promociones'"
                     x-cloak
                     x-transition
                     class="ml-4 mt-2 space-y-1">
                    <a href="{{ route('promociones.index') }}"  class="block px-4 py-1 text-sm hover:text-red-500">Listar</a>
                    <a href="{{ route('promociones.create') }}" class="block px-4 py-1 text-sm hover:text-red-500">Crear</a>
                </div>
            </div>

            <!-- Clientes -->
            <div>
                <button @click="active = active === 'clientes' ? null : 'clientes'"
                        class="w-full text-left px-4 py-2 rounded hover:bg-gray-800 flex justify-between items-center">
                    Clientes
                    <span x-text="active === 'clientes' ? '▲' : '▼'"></span>
                </button>

                <div x-show="active === 'clientes'"
                     x-cloak
                     x-transition
                     class="ml-4 mt-2 space-y-1">
                    <a href="{{ route('clientes.index') }}"  class="block px-4 py-1 text-sm hover:text-red-500">Listar</a>
                    <a href="{{ route('clientes.create') }}" class="block px-4 py-1 text-sm hover:text-red-500">Crear</a>
                </div>
            </div>

            <!-- Empleados -->
            <div>
                <button @click="active = active === 'empleados' ? null : 'empleados'"
                        class="w-full text-left px-4 py-2 rounded hover:bg-gray-800 flex justify-between items-center">
                    Empleados
                    <span x-text="active === 'empleados' ? '▲' : '▼'"></span>
                </button>

                <div x-show="active === 'empleados'"
                     x-cloak
                     x-transition
                     class="ml-4 mt-2 space-y-1">
                    <a href="{{ route('empleados.index') }}"  class="block px-4 py-1 text-sm hover:text-red-500">Listar</a>
                    <a href="{{ route('empleados.create') }}" class="block px-4 py-1 text-sm hover:text-red-500">Crear</a>
                </div>
            </div>

            <!-- Reportes -->
            <div>
                <button @click="active = active === 'reportes' ? null : 'reportes'"
                        class="w-full text-left px-4 py-2 rounded hover:bg-gray-800 flex justify-between items-center">
                    Reportes pendientes
                    <span x-text="active === 'reportes' ? '▲' : '▼'"></span>
                </button>

                <div x-show="active === 'reportes'"
                     x-cloak
                     x-transition
                     class="ml-4 mt-2 space-y-1">
                    <a href="#" class="block px-4 py-1 text-sm hover:text-red-500">Ventas</a>
                    <a href="#" class="block px-4 py-1 text-sm hover:text-red-500">Inventario</a>
                    <a href="#" class="block px-4 py-1 text-sm hover:text-red-500">Rendimiento</a>
                </div>
            </div>

        </nav>

    </aside>

    <!-- Contenido -->
    <main class="flex-1">

        <header class="bg-white shadow p-4 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-700">
                @yield('titulo')
            </h2>

            <div class="flex items-center gap-4">
                <span class="text-gray-600">
                    {{ Auth::guard('empleado')->user()->nombre }} {{ Auth::guard('empleado')->user()->apellidop }}
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-600 hover:underline text-sm">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </header>

        <div class="p-6">
            @yield('content')
        </div>

    </main>

</div>

<script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>