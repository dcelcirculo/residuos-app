{{-- 
    Vista: Dashboard (Menú principal de la aplicación)
    ----------------------------------------------------------------------------
    Presenta accesos rápidos a las funcionalidades clave del sistema:
    - Registrar, consultar, modificar y eliminar solicitudes
    - Reportes de recolecciones
    - Acceso al perfil del usuario
--}}

<x-app-layout>
    {{-- Encabezado del dashboard --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Menú Principal') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Tarjeta con el menú principal de acciones --}}
            <div class="p-6 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-medium">Opciones del sistema</h3>
                <ul class="mt-3 space-y-3">
                    {{-- Crear nueva solicitud --}}
                    <li>
                        <a href="{{ route('solicitudes.create') }}"
                           class="block px-4 py-2 bg-blue-100 rounded hover:bg-blue-200">
                            ➕ Registrar solicitud
                        </a>
                    </li>

                    {{-- Listado/consulta de solicitudes --}}
                    <li>
                        <a href="{{ route('solicitudes.index') }}"
                           class="block px-4 py-2 bg-blue-100 rounded hover:bg-blue-200">
                            📋 Consultar solicitudes
                        </a>
                    </li>

                    {{-- Modificar solicitud: de momento redirige al listado para elegir cuál editar --}}
                    <li>
                        <a href="{{ route('solicitudes.index') }}"
                           class="block px-4 py-2 bg-blue-100 rounded hover:bg-blue-200">
                            ✏️ Modificar solicitud
                        </a>
                    </li>

                    {{-- Eliminar solicitud: también desde el listado (con botón eliminar por fila) --}}
                    <li>
                        <a href="{{ route('solicitudes.index') }}"
                           class="block px-4 py-2 bg-blue-100 rounded hover:bg-blue-200">
                            🗑️ Eliminar solicitud
                        </a>
                    </li>

                    {{-- Reportes / historial de recolecciones --}}
                    <li>
                        <a href="{{ route('recolecciones.index') }}"
                           class="block px-4 py-2 bg-blue-100 rounded hover:bg-blue-200">
                            📊 Reportes de recolecciones
                        </a>
                    </li>

                    {{-- Enlace al perfil del usuario --}}
                    <li>
                        <a href="{{ route('profile.edit') }}"
                           class="block px-4 py-2 bg-blue-100 rounded hover:bg-blue-200">
                            ⚙️ Mi perfil
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</x-app-layout>