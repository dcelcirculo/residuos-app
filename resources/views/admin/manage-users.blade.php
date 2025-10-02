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

            @php
                $tiposResiduos = $tiposResiduos ?? ['organico','inorganico','peligroso'];
                $tipoLabels = [
                    'organico' => 'Orgánico',
                    'inorganico' => 'Inorgánico',
                    'peligroso' => 'Peligroso',
                ];
            @endphp

            {{-- Panel: Alta rápida de usuarios (todos los roles) --}}
            <div class="bg-white p-6 shadow sm:rounded-lg mb-6">
                <h3 class="text-lg font-medium mb-3">Crear nuevo usuario</h3>
                <p class="text-sm text-gray-600 mb-4">Registra vecinos, personal administrativo o empresas recolectoras asignándoles el rol correspondiente.</p>

                <form method="POST" action="{{ route('admin.users.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf

                    <div>
                        <label class="block text-sm">Nombre completo</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="border rounded w-full px-2 py-1" required>
                        @error('name', 'createUser')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm">Correo electrónico</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="border rounded w-full px-2 py-1" required>
                        @error('email', 'createUser')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm">Rol</label>
                        @php $newRole = old('role', 'user'); @endphp
                        <select name="role" id="create-role" class="border rounded w-full px-2 py-1" required data-toggle-especialidades="create">
                            <option value="user" {{ $newRole === 'user' ? 'selected' : '' }}>Usuario</option>
                            <option value="admin" {{ $newRole === 'admin' ? 'selected' : '' }}>Administrador</option>
                            <option value="empresa" {{ $newRole === 'empresa' ? 'selected' : '' }}>Empresa recolectora</option>
                        </select>
                        @error('role', 'createUser')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2" data-especialidades="create" @class(['hidden' => $newRole !== 'empresa'])>
                        <label class="block text-sm">Especialidades de residuos</label>
                        <div class="flex flex-wrap gap-3 mt-1">
                            @foreach($tiposResiduos as $tipo)
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="especialidades[]" value="{{ $tipo }}" class="rounded border-gray-300" @checked(in_array($tipo, old('especialidades', [])))>
                                    {{ $tipoLabels[$tipo] ?? ucfirst($tipo) }}
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Selecciona al menos una opción cuando el rol sea "Empresa recolectora".</p>
                        @error('especialidades', 'createUser')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm">Teléfono (WhatsApp)</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" class="border rounded w-full px-2 py-1" placeholder="+57XXXXXXXXXX">
                        <p class="text-xs text-gray-500 mt-1">Formato E.164, opcional pero recomendado para notificaciones.</p>
                        @error('phone', 'createUser')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm">Contraseña temporal</label>
                        <input type="password" name="password" class="border rounded w-full px-2 py-1" required>
                        <p class="text-xs text-gray-500 mt-1">Comparte esta contraseña y solicita cambiarla en el primer ingreso.</p>
                        @error('password', 'createUser')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" class="border rounded w-full px-2 py-1" required>
                        @error('password_confirmation', 'createUser')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2 flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Crear usuario</button>
                    </div>
                </form>
            </div>

            {{-- Panel: Fórmula de puntos --}}
            <div class="bg-white p-6 shadow sm:rounded-lg mb-6">
                <h3 class="text-lg font-medium mb-3">Fórmula de puntos</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Define cómo se calculan los puntos usando la variable <code>kilos</code>. Puedes usar funciones PHP como <code>floor</code>, <code>ceil</code>, <code>round</code>, <code>min</code> y <code>max</code>.
                </p>

                <form method="POST" action="{{ route('admin.settings.points') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm">Fórmula actual</label>
                        <textarea name="formula" rows="2" class="mt-1 w-full border rounded px-3 py-2" required>{{ old('formula', $formula ?? '') }}</textarea>
                        @error('formula')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Ejemplo: <code>floor(kilos * 12)</code> o <code>min(floor(kilos * 15), 500)</code>.</p>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Actualizar fórmula</button>
                </form>
            </div>

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
                            @error('name')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm">Email</label>
                            <input type="email" name="email" value="{{ $user->email }}" class="border rounded w-full px-2 py-1">
                            @error('email')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm">Teléfono (WhatsApp)</label>
                            <input type="tel" name="phone" value="{{ $user->phone }}" class="border rounded w-full px-2 py-1" placeholder="+57XXXXXXXXXX">
                            <p class="text-xs text-gray-500 mt-1">Formato E.164, incluye el prefijo del país.</p>
                            @error('phone')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm">Rol</label>
                            <select name="role" id="edit-role" class="border rounded w-full px-2 py-1" data-toggle-especialidades="edit">
                                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Usuario</option>
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrador</option>
                                <option value="empresa" {{ $user->role === 'empresa' ? 'selected' : '' }}>Empresa recolectora</option>
                            </select>
                            @error('role')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div data-especialidades="edit" @class(['hidden' => $user->role !== 'empresa'])>
                            <label class="block text-sm">Especialidades de residuos</label>
                            @php $seleccionadas = old('especialidades', $user->empresaRecolectora?->especialidades ?? []); @endphp
                            <div class="flex flex-wrap gap-3 mt-1">
                                @foreach($tiposResiduos as $tipo)
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="checkbox" name="especialidades[]" value="{{ $tipo }}" class="rounded border-gray-300" @checked(in_array($tipo, $seleccionadas ?? []))>
                                        {{ $tipoLabels[$tipo] ?? ucfirst($tipo) }}
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Requerido cuando el rol es "Empresa recolectora".</p>
                            @error('especialidades')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm">Contraseña nueva (opcional)</label>
                            <input type="password" name="password" class="border rounded w-full px-2 py-1" placeholder="Ingresa una nueva contraseña si deseas cambiarla">
                            <p class="text-xs text-gray-500 mt-1">Deja en blanco para mantener la contraseña actual. Mínimo 8 caracteres.</p>
                            @error('password')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm">Confirmar contraseña</label>
                            <input type="password" name="password_confirmation" class="border rounded w-full px-2 py-1" placeholder="Repite la nueva contraseña">
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggles = document.querySelectorAll('[data-toggle-especialidades]');

            toggles.forEach(select => {
                select.addEventListener('change', event => {
                    const target = event.target.getAttribute('data-toggle-especialidades');
                    const wrapper = document.querySelector(`[data-especialidades="${target}"]`);
                    if (!wrapper) return;

                    if (event.target.value === 'empresa') {
                        wrapper.classList.remove('hidden');
                        wrapper.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.required = true);
                    } else {
                        wrapper.classList.add('hidden');
                        wrapper.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.required = false);
                    }
                });
            });
        });
    </script>
</x-app-layout>
