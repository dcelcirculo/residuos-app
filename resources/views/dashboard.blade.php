{{-- 
    Vista: Dashboard (Usuario)
    ----------------------------------------------------------------------------
    Menú principal para usuarios NO administradores en EcoGestión.
    Ofrece accesos rápidos a:
    - Registrar nueva solicitud
    - Consultar / administrar solicitudes (editar/eliminar desde el listado)
    - Reportes (historial de recolecciones del usuario)
    - Perfil del usuario
--}}

<x-app-layout>
    {{-- Encabezado del dashboard del usuario --}}
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
                    {{-- Registrar nueva solicitud --}}
                    <li>
                        <a href="{{ route('solicitudes.create') }}"
                           class="block px-4 py-2 bg-blue-100 rounded hover:bg-blue-200">
                            ➕ Registrar solicitud
                        </a>
                    </li>

                    {{-- Listado/consulta de solicitudes (desde aquí también podrás editar/eliminar por fila) --}}
                    <li>
                        <a href="{{ route('solicitudes.index') }}"
                           class="block px-4 py-2 bg-blue-100 rounded hover:bg-blue-200">
                            📋 Consultar solicitudes
                        </a>
                    </li>

                    {{-- Reportes / historial de recolecciones del usuario --}}
                    <li>
                        <a href="{{ route('recolecciones.index') }}"
                           class="block px-4 py-2 bg-blue-100 rounded hover:bg-blue-200">
                            📊 Mis recolecciones
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

                {{-- Si el usuario también es admin, mostramos un atajo opcional al panel de admin --}}
                @if(auth()->user()?->isAdmin())
                    <div class="mt-6 border-t pt-4">
                        <p class="text-sm text-gray-600 mb-2">Accesos de administración:</p>
                        <a href="{{ route('admin.dashboard') }}"
                           class="inline-block px-4 py-2 bg-yellow-100 rounded hover:bg-yellow-200">
                            🛠️ Panel de administración
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>