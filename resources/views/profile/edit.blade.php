{{-- 
    Vista: Perfil de usuario
    ----------------------------------------------------------------------------
    Agrupa las secciones de actualización de perfil, cambio de contraseña
    y eliminación de cuenta (parciales de Breeze/Jetstream).
--}}

<x-app-layout>
    {{-- Encabezado del perfil --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Sección: Actualizar información básica (nombre, email) --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    {{-- Parcial que contiene el formulario de actualización --}}
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Sección: Cambiar contraseña --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    {{-- Parcial que contiene el formulario de cambio de contraseña --}}
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Sección: Eliminar cuenta --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    {{-- Parcial que contiene el formulario de eliminación de usuario --}}
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>