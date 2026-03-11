<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Guarida — Cambiar contraseña</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#1b1b1b]">

<section class="min-h-screen flex items-center justify-center">
    <div class="relative w-full max-w-md bg-[#2a2a2a] rounded-2xl shadow-2xl pt-16 pb-8 px-8">

        <!-- Logo flotante -->
        <div class="absolute -top-12 left-1/2 transform -translate-x-1/2">
            <div class="w-24 h-24 bg-[#fff] rounded-full flex items-center justify-center shadow-lg border-4 border-[#1b1b1b]">
                <img src="imagenes/LogoGuarida.png" class="w-20 h-20 object-contain">
            </div>
        </div>

        <!-- Título -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-[#ea0000]">Bienvenido</h1>
            <p class="text-gray-400 text-sm mt-1">
                Es tu primer acceso. Por seguridad,<br>debes cambiar tu contraseña.
            </p>
        </div>

        <form method="POST" action="{{ route('primer.login.post') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm mb-2 text-gray-300">Nueva contraseña</label>
                <input type="password" name="contrasena_nueva" required
                       class="w-full p-3 rounded-lg bg-[#1b1b1b] border border-gray-600 text-white
                              focus:border-[#f4a400] focus:ring-[#f4a400]
                              @error('contrasena_nueva') border-red-500 @enderror">
                @error('contrasena_nueva')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm mb-2 text-gray-300">Confirmar contraseña</label>
                <input type="password" name="contrasena_confirmar" required
                       class="w-full p-3 rounded-lg bg-[#1b1b1b] border border-gray-600 text-white
                              focus:border-[#f4a400] focus:ring-[#f4a400]
                              @error('contrasena_confirmar') border-red-500 @enderror">
                @error('contrasena_confirmar')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-[#f4a400] hover:bg-[#fbbb26] text-black font-semibold py-3 rounded-lg transition">
                Guardar y continuar
            </button>

        </form>
    </div>
</section>

</body>
</html>