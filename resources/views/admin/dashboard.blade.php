{{-- 
    Vista: Panel de Administración (admin.dashboard)
    ----------------------------------------------------------------------------
    Propósito:
      - Mostrar accesos rápidos a las funciones clave del sistema para un ADMIN.
      - Centraliza flujos frecuentes: solicitudes, recolecciones, gestión de usuarios y perfil.

    Notas:
      - Esta vista se renderiza desde AdminController@dashboard y está protegida
        por el Gate 'admin-only' (ver AppServiceProvider) y el middleware en routes/web.php.
      - Usa el layout de la app (<x-app-layout>) para heredar la barra superior y estilos.
--}}

<x-app-layout>
    {{-- Slot de encabezado: se inyecta en el layout para mostrar el título superior --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- Título visible en la parte superior de la página --}}
            {{ __('Menú Principal (Administrador)') }}
        </h2>
    </x-slot>

    {{-- Contenedor vertical principal con padding vertical --}}
    <div class="py-6">
        {{-- Contenedor centrado y responsivo con separación vertical entre tarjetas --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Tarjeta principal: lista de accesos del administrador --}}
            <div class="p-6 bg-white shadow sm:rounded-lg">
                {{-- Subtítulo de la tarjeta --}}
                <h3 class="text-lg font-medium">Opciones del sistema</h3>

                {{-- Lista vertical de opciones. Cada <li> contiene un enlace tipo botón. --}}
                <ul class="mt-3 space-y-3">
                    {{-- Acceso: Crear nueva solicitud --}}
                    <li>
                        <a href="{{ route('solicitudes.create') }}"
                           class="block px-4 py-2 bg-blue-100 rounded hover:bg-blue-200">
                            {{-- Icono textual + etiqueta --}}
                            ➕ Registrar solicitud
                        </a>
                    </li>

                    {{-- Acceso: Listado/consulta de solicitudes (desde allí también se edita/borra por fila) --}}
                    <li>
                        <a href="{{ route('solicitudes.index') }}"
                           class="block px-4 py-2 bg-blue-100 rounded hover:bg-blue-200">
                            📋 Consultar solicitudes
                        </a>
                    </li>

                    {{-- Acceso: Modificar solicitud (redirige al listado para seleccionar cuál editar) --}}
                    <li>
                        <a href="{{ route('solicitudes.index') }}"
                           class="block px-4 py-2 bg-blue-100 rounded hover:bg-blue-200">
                            ✏️ Modificar solicitud
                        </a>
                    </li>

                    {{-- Acceso: Eliminar solicitud (también se hace desde el listado) --}}
                    <li>
                        <a href="{{ route('solicitudes.index') }}"
                           class="block px-4 py-2 bg-blue-100 rounded hover:bg-blue-200">
                            🗑️ Eliminar solicitud
                        </a>
                    </li>

                    {{-- Acceso: Reportes / historial de recolecciones (vista del módulo de recolecciones) --}}
                    <li>
                        <a href="{{ route('recolecciones.index') }}"
                           class="block px-4 py-2 bg-blue-100 rounded hover:bg-blue-200">
                            📊 Reportes de recolecciones
                        </a>
                    </li>

                    {{-- Acceso: Gestión puntual de usuarios (buscar/editar/eliminar) --}}
                    <li>
                        <a href="{{ route('admin.users.manage') }}"
                           class="block px-4 py-2 bg-blue-100 rounded hover:bg-blue-200">
                            👥 Gestión de usuarios
                        </a>
                    </li>

                    {{-- Acceso: Perfil del usuario autenticado (sección Breeze/Jetstream) --}}
                    <li>
                        <a href="{{ route('profile.edit') }}"
                           class="block px-4 py-2 bg-blue-100 rounded hover:bg-blue-200">
                            ⚙️ Mi perfil
                        </a>
                    </li>
                </ul>
            </div>{{-- /Tarjeta principal --}}

        </div>{{-- /Contenedor centrado --}}
    </div>{{-- /Contenedor vertical principal --}}
</x-app-layout>