{{-- 
    Vista: Gestión de Usuarios (solo administradores)
    ----------------------------------------------------------------------------
    Esta vista permite al administrador:
    - Buscar un usuario por ID o por nombre (username parcial).
    - Si se encuentra, mostrar su información básica.
    - Modificar nombre, email o rol del usuario.
    - Eliminar el usuario seleccionado.
--}}

<x-app-layout>
    {{-- Encabezado de la vista --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Usuarios (Admin)') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensajes de confirmación o error --}}
            @if(session('ok'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
                    {{ session('ok') }}
                </div>
            @endif

            {{-- Formulario de búsqueda --}}
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <h3 class="text-lg font-medium mb-3">Buscar usuario</h3>
                <form method="POST" action="{{ route('admin.users.search') }}" class="flex gap-4">
                    @csrf
                    <div>
                        <input type="text" name="id" placeholder="ID de usuario" class="border rounded px-2 py-1">
                    </div>
                    <div>
                        <input type="text" name="username" placeholder="Nombre de usuario" class="border rounded px-2 py-1">
                    </div>
                    <div>
                        <input type="text" name="email" placeholder="Correo electrónico" class="border rounded px-2 py-1">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Buscar</button>
                </form>
            </div>

            {{-- Si se encontró un usuario, mostrar detalle y opciones --}}
            @isset($user)
                <div class="mt-6 bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="text-lg font-medium mb-3">Usuario encontrado</h3>
                    <p><strong>ID:</strong> {{ $user->id }}</p>
                    <p><strong>Nombre:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Rol:</strong> {{ $user->role }}</p>

                    {{-- Formulario de actualización --}}
                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="mt-4 space-y-3">
                        @csrf
                        <div>
                            <label class="block text-sm">Nombre</label>
                            <input type="text" name="name" value="{{ $user->name }}" class="border rounded w-full px-2 py-1">
                        </div>
                        <div>
                            <label class="block text-sm">Email</label>
                            <input type="email" name="email" value="{{ $user->email }}" class="border rounded w-full px-2 py-1">
                        </div>
                        <div>
                            <label class="block text-sm">Rol</label>
                            <select name="role" class="border rounded w-full px-2 py-1">
                                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Usuario</option>
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrador</option>
                                <option value="recolector" {{ $user->role === 'recolector' ? 'selected' : '' }}>Recolector</option>
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Guardar cambios</button>
                    </form>

                    {{-- Formulario de eliminación --}}
                    <form method="POST" action="{{ route('admin.users.delete', $user->id) }}" class="mt-3">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded"
                                onclick="return confirm('¿Seguro que deseas eliminar este usuario?')">
                            Eliminar usuario
                        </button>
                    </form>
                </div>
            @endisset

        </div>
    </div>
</x-app-layout>