<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if(session('ok'))
            <div class="mb-4 rounded-md bg-green-50 p-4 text-green-800">
                {{ session('ok') }}
            </div>
        @endif

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Mis solicitudes</h1>
                <p class="text-sm text-gray-500">Consulta, filtra y ordena tus solicitudes.</p>
            </div>

            <div class="flex items-center gap-2">
                {{-- Exporta respetando los filtros actuales --}}
                <a href="{{ route('solicitudes.export', request()->query()) }}"
                    class="inline-flex items-center gap-2 rounded-md border border-gray-300 px-4 py-2 hover:bg-gray-50">
                    ⬇️ Exportar CSV
                </a>

                <a href="{{ route('solicitudes.create') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                    ➕ Nueva solicitud
                </a>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" action="{{ route('solicitudes.index') }}"
              class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Buscar</label>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="ID, tipo, estado…"
                       class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Estado</label>
                @php $estado = request('estado','todos'); @endphp
                <select name="estado"
                        class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="todos" {{ $estado==='todos'?'selected':'' }}>Todos</option>
                    <option value="pendiente" {{ $estado==='pendiente'?'selected':'' }}>Pendiente</option>
                    <option value="en_proceso" {{ $estado==='en_proceso'?'selected':'' }}>En proceso</option>
                    <option value="completada" {{ $estado==='completada'?'selected':'' }}>Completada</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Desde</label>
                <input type="date" name="from" value="{{ request('from') }}"
                       class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Hasta</label>
                <input type="date" name="to" value="{{ request('to') }}"
                       class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="md:col-span-5 flex items-end gap-2">
                <button class="inline-flex items-center px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">
                    Filtrar
                </button>
                <a href="{{ route('solicitudes.index') }}"
                   class="inline-flex items-center px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-50">
                    Limpiar
                </a>
            </div>
        </form>

        @php
            $toggle = function($col) {
                $dir = (request('sort')===$col && request('dir')==='asc') ? 'desc' : 'asc';
                return array_merge(request()->except(['page']), ['sort'=>$col,'dir'=>$dir]);
            };
        @endphp

        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        <a href="{{ route('solicitudes.index', $toggle('id')) }}">ID</a>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Frecuencia</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        <a href="{{ route('solicitudes.index', $toggle('fecha_programada')) }}">Fecha programada</a>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        <a href="{{ route('solicitudes.index', $toggle('estado')) }}">Estado</a>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        <a href="{{ route('solicitudes.index', $toggle('created_at')) }}">Creado</a>
                    </th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($solicitudes as $s)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-sm">{{ $s->id }}</td>
                        <td class="px-4 py-2 text-sm">{{ ucfirst($s->tipo_residuo ?? '—') }}</td>
                        <td class="px-4 py-2 text-sm">{{ ucfirst($s->frecuencia ?? '—') }}</td>
                        <td class="px-4 py-2 text-sm">{{ (string) $s->fecha_programada }}</td>
                        <td class="px-4 py-2 text-sm">
                            @php
                                $colors = [
                                    'pendiente' => 'bg-yellow-100 text-yellow-800',
                                    'en_proceso'=> 'bg-blue-100 text-blue-800',
                                    'completada'=> 'bg-green-100 text-green-800',
                                ];
                                $estado = $s->estado ?? 'pendiente';
                                $estadoClass = $colors[$estado] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $estadoClass }}">
                                {{ ucfirst(str_replace('_',' ', $estado)) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-sm">{{ optional($s->created_at)->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-2 text-sm text-right">
                            {{-- Acciones por fila: Ver / Editar / Eliminar --}}
                            <div class="inline-flex items-center gap-3">
                                <a href="{{ route('solicitudes.show', $s) }}" class="text-indigo-600 hover:text-indigo-900">Ver</a>
                                <a href="{{ route('solicitudes.edit', $s) }}" class="text-gray-600 hover:text-gray-900">Editar</a>

                                {{-- Formulario inline para eliminar esta solicitud (la de esta fila) --}}
                                <form method="POST" action="{{ route('solicitudes.destroy', $s) }}"
                                      onsubmit="return confirm('¿Eliminar esta solicitud #{{ $s->id }}?')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">Sin solicitudes aún.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $solicitudes->links() }}
        </div>
    </div>
</x-app-layout>
