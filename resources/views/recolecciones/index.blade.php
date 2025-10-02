{{-- 
    Vista: Recolecciones (registro + historial)
    ----------------------------------------------------------------------------
    - Muestra un formulario para registrar una recolección asociada a una solicitud
      del usuario autenticado.
    - Lista paginada del historial de recolecciones del mismo usuario.
--}}

<x-app-layout>
    {{-- Encabezado de la página --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Recolecciones') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @php
                $user = auth()->user();
                $isEmpresa = $user?->isEmpresaRecolectora();
            @endphp

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <p class="text-sm text-gray-600">
                        Consulta tus recolecciones registradas y descarga el reporte en CSV con fecha, hora y peso.
                    </p>
                </div>
                <div>
                    <a href="{{ route('recolecciones.export') }}"
                       class="inline-flex items-center gap-2 rounded-md border border-gray-300 px-4 py-2 hover:bg-gray-50">
                        ⬇️ Exportar CSV
                    </a>
                </div>
            </div>

            {{-- Mensaje de confirmación (flash) al registrar/actualizar --}}
            @if(session('ok'))
                <div class="p-4 bg-green-100 text-green-800 rounded">
                    {{ session('ok') }}
                </div>
            @endif

            {{-- Panel: Formulario de registro de recolección --}}
            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-3">Registrar recolección</h3>

                    @if($isEmpresa)
                        <p class="text-sm text-gray-600 mb-4">
                            Selecciona una solicitud compatible con tus especialidades, pesa el material en sitio y registra los kilos exactos.
                        </p>
                    @endif

                    {{-- Si no hay solicitudes pendientes, informamos al usuario --}}
                    @if($solicitudesPendientes->count() === 0)
                        <p class="text-gray-600">
                            {{ $isEmpresa ? 'No hay solicitudes pendientes según tus especialidades.' : 'No tienes solicitudes pendientes para registrar.' }}
                        </p>
                    @else
                        {{-- Formulario para crear una nueva recolección --}}
                        <form method="POST" action="{{ route('recolecciones.store') }}" class="space-y-4">
                            @csrf

                            {{-- Selector de solicitud pendiente a la cual asociar la recolección --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Solicitud</label>
                                <select name="solicitud_id" class="mt-1 block w-full border-gray-300 rounded" required>
                                    @foreach($solicitudesPendientes as $s)
                                        <option value="{{ $s->id }}">
                                            #{{ $s->id }} – {{ ucfirst($s->tipo_residuo) }} – {{ $s->fecha_programada }} ({{ $s->frecuencia }})
                                            @if($isEmpresa)
                                                @php
                                                    $vecino = $s->relationLoaded('user') ? optional($s->user)->name : null;
                                                @endphp
                                                {{ $vecino ? ' – Vecino: '.$vecino : '' }}
                                                {{ $s->turno_ruta ? ' – Turno '.$s->turno_ruta : '' }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('solicitud_id')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Kilos recolectados (numérico con decimales) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kilos recolectados</label>
                                <input type="number" step="0.01" name="kilos" class="mt-1 block w-full border-gray-300 rounded" required>
                                <p class="text-xs text-gray-500 mt-1">Registra el peso exacto en kilogramos (por ejemplo: 12.35).</p>
                                @error('kilos')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Cumplimiento de separación --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Separación adecuada</label>
                                <input type="hidden" name="cumple_separacion" value="0">
                                <label class="inline-flex items-center gap-2 mt-1 text-sm text-gray-700">
                                    <input type="checkbox" name="cumple_separacion" value="1" class="rounded border-gray-300" required @checked(old('cumple_separacion', '0') == '1')>
                                    Confirmo que el residuo fue entregado cumpliendo los requisitos de separación.
                                </label>
                                @error('cumple_separacion')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Botón para guardar la recolección --}}
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                                Guardar
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Panel: Historial de recolecciones del usuario --}}
            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-3">Historial de recolecciones</h3>

                    {{-- Tabla básica con los campos principales --}}
                    <table class="min-w-full border-collapse border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2">ID</th>
                                <th class="border px-3 py-2">Solicitud</th>
                                @if($isEmpresa)
                                    <th class="border px-3 py-2">Vecino</th>
                                @else
                                    <th class="border px-3 py-2">Empresa</th>
                                @endif
                                <th class="border px-3 py-2">Fecha real</th>
                                <th class="border px-3 py-2">Separación</th>
                                <th class="border px-3 py-2">Kilos</th>
                                <th class="border px-3 py-2">Puntos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recolecciones as $r)
                                <tr>
                                    <td class="border px-3 py-2">{{ $r->id }}</td>
                                    <td class="border px-3 py-2">#{{ $r->solicitud_id }}</td>
                                    <td class="border px-3 py-2">
                                        @if($isEmpresa)
                                            {{ optional(optional($r->solicitud)->user)->name ?? '—' }}
                                        @else
                                            {{ optional($r->recolector)->name ?? '—' }}
                                        @endif
                                    </td>
                                    <td class="border px-3 py-2">{{ $r->fecha_real }}</td>
                                    <td class="border px-3 py-2">
                                        @if($r->cumple_separacion)
                                            <span class="inline-flex px-2 py-0.5 rounded-full bg-green-100 text-green-800 text-xs">Sí</span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded-full bg-red-100 text-red-800 text-xs">No</span>
                                        @endif
                                    </td>
                                    <td class="border px-3 py-2">{{ $r->kilos }}</td>
                                    <td class="border px-3 py-2">{{ $r->puntos }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        Sin recolecciones registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Paginación (usa el paginador de Laravel) --}}
                    <div class="mt-4">
                        {{ $recolecciones->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
