<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Menú Principal') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Menú principal -->
            <div class="p-6 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-medium">Opciones del sistema</h3>
                <ul class="mt-3 space-y-3">
                    <li>
                        <a href="{{ route('solicitudes.create') }}"
                            class="block px-4 py-2 bg-blue-100 rounded hover:bg-blue-200">
                            ➕ Registrar solicitud
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('solicitudes.index') }}"
                            class="block px-4 py-2 bg-blue-100 rounded hover:bg-blue-200">
                            📋 Consultar solicitudes
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="block px-4 py-2 bg-blue-100 rounded hover:bg-blue-200">
                            ✏️ Modificar solicitud
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="block px-4 py-2 bg-blue-100 rounded hover:bg-blue-200">
                            🗑️ Eliminar solicitud
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('recolecciones.index') }}"
                            class="block px-4 py-2 bg-blue-100 rounded hover:bg-blue-200">
                            📊 Reportes de recolecciones
                        </a>
                    </li>
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