<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Recolecciones') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('ok'))
                <div class="p-4 bg-green-100 text-green-800 rounded">
                    {{ session('ok') }}
                </div>
            @endif

            <!-- Formulario -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-3">Registrar recolección</h3>

                    @if($solicitudesPendientes->count() === 0)
                        <p class="text-gray-600">No tienes solicitudes pendientes para registrar.</p>
                    @else
                        <form method="POST" action="{{ route('recolecciones.store') }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Solicitud</label>
                                <select name="solicitud_id" class="mt-1 block w-full border-gray-300 rounded" required>
                                    @foreach($solicitudesPendientes as $s)
                                        <option value="{{ $s->id }}">
                                            #{{ $s->id }} – {{ ucfirst($s->tipo_residuo) }} – {{ $s->fecha_programada }} ({{ $s->frecuencia }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kilos recolectados</label>
                                <input type="number" step="0.01" name="kilos" class="mt-1 block w-full border-gray-300 rounded" required>
                            </div>

                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Guardar</button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Historial -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-3">Historial de recolecciones</h3>

                    <table class="min-w-full border-collapse border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2">ID</th>
                                <th class="border px-3 py-2">Solicitud</th>
                                <th class="border px-3 py-2">Fecha real</th>
                                <th class="border px-3 py-2">Kilos</th>
                                <th class="border px-3 py-2">Puntos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recolecciones as $r)
                                <tr>
                                    <td class="border px-3 py-2">{{ $r->id }}</td>
                                    <td class="border px-3 py-2">#{{ $r->solicitud_id }}</td>
                                    <td class="border px-3 py-2">{{ $r->fecha_real }}</td>
                                    <td class="border px-3 py-2">{{ $r->kilos }}</td>
                                    <td class="border px-3 py-2">{{ $r->puntos }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-4">Sin recolecciones registradas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $recolecciones->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>