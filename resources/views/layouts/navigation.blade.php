<?php
/*
|--------------------------------------------------------------------------
| Archivo de navegación principal de la aplicación EcoGestión
|--------------------------------------------------------------------------
| Este archivo Blade define la barra de navegación superior utilizada en toda la aplicación.
| Incluye el logo, nombre de la app, enlaces principales, menú de usuario y menú responsive
| para dispositivos móviles. Utiliza Alpine.js para la lógica de mostrar/ocultar el menú
| en pantallas pequeñas.
*/
?>

{{-- Barra de navegación principal. Incluye menú, logo, enlaces y usuario. --}}
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    {{-- Contenedor general del menú de navegación --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                {{-- Logo y nombre de la aplicación --}}
                <div class="shrink-0 flex flex-col items-center">
                    <a href="{{ route('dashboard') }}" class="flex flex-col items-center leading-tight">
                        {{-- Icono SVG representando el logo de EcoGestión --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-700" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5 13c0 4 3 7 7 7 4.418 0 8-3.582 8-8 0-4-3-7-7-7-3 0-6 2-7 5m0 0c3 0 5-2 5-5" />
                        </svg>
                        {{-- Nombre de la aplicación debajo del logo --}}
                        <span class="mt-1 font-extrabold text-lg text-green-700">
                            {{ config('app.name', 'EcoGestión') }}
                        </span>
                    </a>
                </div>

                {{-- Enlaces principales de la navegación (solo visibles en pantallas medianas/grandes) --}}
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    {{-- Enlace al Dashboard (activo si es la ruta actual) --}}
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    {{-- Solo visible para administradores --}}
@if(Auth::user()?->isAdmin())
    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
        {{ __('Administración') }}
    </x-nav-link>

    {{-- (Opcional) Acceso directo al listado de usuarios del admin --}}
    <x-nav-link :href="route('admin.users')" :active="request()->routeIs('admin.users')">
        {{ __('Usuarios') }}
    </x-nav-link>

    {{-- Accesos directos a los reportes administrativos --}}
    <x-nav-link :href="route('reports.user')" :active="request()->routeIs('reports.user')">
        {{ __('Reporte vecinos') }}
    </x-nav-link>
    <x-nav-link :href="route('reports.users')" :active="request()->routeIs('reports.users')">
        {{ __('Reporte global') }}
    </x-nav-link>
    <x-nav-link :href="route('reports.company')" :active="request()->routeIs('reports.company')">
        {{ __('Reporte empresas') }}
    </x-nav-link>
@endif
                </div>
            </div>

            {{-- Menú desplegable de usuario (visible sólo en pantallas grandes) --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        {{-- Botón que muestra el nombre del usuario y un icono de flecha --}}
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                {{-- Icono de flecha hacia abajo indicando menú desplegable --}}
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @if(Auth::user()?->isAdmin())
    <x-dropdown-link :href="route('admin.dashboard')">
        {{ __('Administración') }}
    </x-dropdown-link>
@endif
                        {{-- Enlace para editar el perfil del usuario --}}
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        {{-- Formulario para cerrar sesión (logout) --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Botón hamburguesa para abrir el menú en dispositivos móviles (responsive) --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        {{-- Icono hamburguesa: tres líneas horizontales (menú cerrado) --}}
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        {{-- Icono de cierre: una X (menú abierto) --}}
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Menú responsive: se muestra sólo en dispositivos móviles cuando open=true --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            {{-- Enlace al Dashboard dentro del menú responsive --}}
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            {{-- Enlaces solo para administradores (responsive) --}}
@if(Auth::user()?->isAdmin())
    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
        {{ __('Administración') }}
    </x-responsive-nav-link>
    <x-responsive-nav-link :href="route('admin.users')" :active="request()->routeIs('admin.users')">
        {{ __('Usuarios') }}
    </x-responsive-nav-link>
    <x-responsive-nav-link :href="route('reports.user')" :active="request()->routeIs('reports.user')">
        {{ __('Reporte vecinos') }}
    </x-responsive-nav-link>
    <x-responsive-nav-link :href="route('reports.users')" :active="request()->routeIs('reports.users')">
        {{ __('Reporte global') }}
    </x-responsive-nav-link>
    <x-responsive-nav-link :href="route('reports.company')" :active="request()->routeIs('reports.company')">
        {{ __('Reporte empresas') }}
    </x-responsive-nav-link>
@endif
        </div>

        {{-- Opciones de usuario en menú responsive --}}
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                {{-- Muestra el nombre y correo electrónico del usuario autenticado --}}
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                {{-- Enlace al perfil de usuario (responsive) --}}
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                {{-- Formulario para cerrar sesión (responsive) --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
