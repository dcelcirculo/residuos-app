{{-- 
    Vista: Welcome (Pantalla de selección de rol antes del login)
    ----------------------------------------------------------------------------
    Presenta el logotipo y opciones de acceso para Usuario, Administrador
    y Recolector. Redirige a la ruta 'login' con un parámetro ?role=...
--}}

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoGestión - Acceso</title>
    {{-- Carga de assets con Vite (Tailwind + JS) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col justify-center items-center bg-gray-100">

    {{-- Logotipo y nombre de la app --}}
    <div class="flex flex-col items-center mb-8">
        {{-- Icono simple como logo (SVG) --}}
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-800" fill="none" viewBox="0 0 24 24"
             stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M5 13c0 4 3 7 7 7 4.418 0 8-3.582 8-8 0-4-3-7-7-7-3 0-6 2-7 5m0 0c3 0 5-2 5-5" />
        </svg>
        <h1 class="mt-2 text-2xl font-bold text-gray-800">EcoGestión</h1>
        <p class="text-gray-600">Sistema Integral de Recolección de Basuras</p>
    </div>

    {{-- Subtítulo / instrucción --}}
    <h2 class="text-lg font-semibold mb-6">Seleccione su tipo de acceso:</h2>

    {{-- Botones de acceso por rol --}}
    <div class="space-y-4 w-64">
        <a href="{{ route('login') }}?role=user"
           class="block w-full text-center bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
            👤 Usuario
        </a>
        <a href="{{ route('login') }}?role=admin"
           class="block w-full text-center bg-gray-800 text-white py-2 rounded hover:bg-gray-900">
            🛠️ Administrador
        </a>
        <a href="{{ route('login') }}?role=empresa"
           class="block w-full text-center bg-green-600 text-white py-2 rounded hover:bg-green-700">
            🚛 Empresa recolectora
        </a>
    </div>

    {{-- Registro para usuarios nuevos --}}
    <div class="mt-8 text-center text-sm text-gray-600">
        ¿Eres nuevo en EcoGestión?
        <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">Crear cuenta</a>
    </div>
</body>
</html>
