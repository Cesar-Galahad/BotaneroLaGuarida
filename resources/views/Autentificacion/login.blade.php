<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <section class="min-h-screen flex items-center justify-center bg-[#1b1b1b]">

        <div class="relative w-full max-w-md bg-[#2a2a2a] rounded-2xl shadow-2xl pt-16 pb-8 px-8">

            <!-- Logo flotante -->
            <div class="absolute -top-12 left-1/2 transform -translate-x-1/2">
                <div class="w-24 h-24 bg-[#FFF] rounded-full flex items-center justify-center shadow-lg border-4 border-[#1b1b1b]">
                    <!-- Aquí irá tu logo -->
                    <img src="imagenes/LogoGuarida.png" class="w-20 h-20 object-contain">
                </div>
            </div>

            <!-- Título -->
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-[#ea0000]">
                    La Guarida
                </h1>
                <p class="text-gray-400 text-sm">
                    Punto de venta
                </p>
            </div>

            <!-- Formulario -->
            <form method="POST" action="{{ url('/login') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm mb-2 text-gray-300">
                        Correo electrónico
                    </label>
                    <input type="email" name="correo" required
                        class="w-full p-3 rounded-lg bg-[#1b1b1b] border border-gray-600 text-white focus:border-[#f4a400] focus:ring-[#f4a400]">
                </div>

                <div class="mb-6">
                    <label class="block text-sm mb-2 text-gray-300">
                        Contraseña
                    </label>
                    <input type="password" name="contrasena" required
                        class="w-full p-3 rounded-lg bg-[#1b1b1b] border border-gray-600 text-white focus:border-[#f4a400] focus:ring-[#f4a400]">
                </div>

                <!-- Botón login -->
                <button type="submit"
                    class="w-full bg-[#f4a400] hover:bg-[#fbbb26] text-black font-semibold py-3 rounded-lg transition">
                    Iniciar sesión
                </button>

                {{-- Error de correo --}}
                @error('correo')
                    <div class="mt-4 bg-red-600 text-white p-3 rounded-lg text-sm flex items-center gap-2">
                        <span></span> {{ $message }}
                    </div>
                @enderror

                {{-- Error de contraseña --}}
                @error('contrasena')
                    <div class="mt-4 bg-red-600 text-white p-3 rounded-lg text-sm flex items-center gap-2">
                        <span></span> {{ $message }}
                    </div>
                @enderror

                <!-- Separador -->
                <div class="flex items-center my-6">
                    <div class="flex-grow h-px bg-gray-600"></div>
                    <span class="px-3 text-gray-400 text-sm">o</span>
                    <div class="flex-grow h-px bg-gray-600"></div>
                </div>

                <!-- Botón Google -->
                <a href="{{ route('auth.google') }}"
                    class="w-full flex items-center justify-center gap-3 bg-white text-black py-3 rounded-lg hover:bg-gray-200 transition">
                        
                        <svg class="w-5 h-5" viewBox="0 0 48 48">
                            <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 32.6 29.3 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.7 1.1 7.8 3l5.7-5.7C33.7 6.1 29.1 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.3-.1-2.3-.4-3.5z"/>
                            <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.5 16 18.9 12 24 12c3 0 5.7 1.1 7.8 3l5.7-5.7C33.7 6.1 29.1 4 24 4c-7.7 0-14.3 4.4-17.7 10.7z"/>
                            <path fill="#4CAF50" d="M24 44c5.2 0 9.9-2 13.4-5.2l-6.2-5.1C29.2 35.1 26.7 36 24 36c-5.3 0-9.8-3.4-11.3-8l-6.5 5C9.5 39.4 16.2 44 24 44z"/>
                            <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-1.1 3-3.2 5.4-6.1 6.9l6.2 5.1C34.9 38.6 44 32 44 24c0-1.3-.1-2.3-.4-3.5z"/>
                        </svg>

                    Iniciar con Google
                </a>

            </form>

        </div>

    </section>
</body>
</html>