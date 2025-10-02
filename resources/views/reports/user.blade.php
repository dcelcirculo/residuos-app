<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reporte por usuario') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Usuario</label>
                        <select name="user_id" class="mt-1 w-full border-gray-300 rounded" required>
                            <option value="">Seleccione…</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected($selectedUserId == $user->id)>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Desde</label>
                        <input type="date" name="from" value="{{ optional($from)->format('Y-m-d') }}" class="mt-1 w-full border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hasta</label>
                        <input type="date" name="to" value="{{ optional($to)->format('Y-m-d') }}" class="mt-1 w-full border-gray-300 rounded">
                    </div>
                    <div class="flex items-end gap-2">
                        <button class="px-4 py-2 bg-indigo-600 text-white rounded">Generar</button>
                        <a href="{{ route('reports.user') }}" class="px-4 py-2 border rounded">Limpiar</a>
                    </div>
                </form>
            </div>

            @if($selectedUserId && $records->isNotEmpty())
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <div class="mb-4 flex flex-wrap gap-6 text-sm text-gray-700">
                        <div><strong>Total de registros:</strong> {{ $records->count() }}</div>
                        <div><strong>Kilos recolectados:</strong> {{ number_format($totals['kilos'], 2) }}</div>
                        <div><strong>Puntos acumulados:</strong> {{ number_format($totals['puntos']) }}</div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-3 py-2 border">Fecha</th>
                                    <th class="px-3 py-2 border">Hora</th>
                                    <th class="px-3 py-2 border">Tipo de residuo</th>
                                    <th class="px-3 py-2 border">Kilos</th>
                                    <th class="px-3 py-2 border">Cumple separación</th>
                                    <th class="px-3 py-2 border">Puntos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($records as $row)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 border">{{ optional($row->fecha_real)->format('Y-m-d') }}</td>
                                        <td class="px-3 py-2 border">{{ optional($row->fecha_real)->format('H:i') }}</td>
                                        <td class="px-3 py-2 border">{{ ucfirst($row->solicitud->tipo_residuo ?? '—') }}</td>
                                        <td class="px-3 py-2 border">{{ number_format($row->kilos, 2) }}</td>
                                        <td class="px-3 py-2 border">
                                            @if($row->cumple_separacion)
                                                <span class="inline-flex px-2 py-0.5 rounded-full bg-green-100 text-green-800 text-xs">Sí</span>
                                            @else
                                                <span class="inline-flex px-2 py-0.5 rounded-full bg-red-100 text-red-800 text-xs">No</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 border">{{ number_format($row->puntos) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @elseif($selectedUserId)
                <div class="bg-white shadow sm:rounded-lg p-6 text-sm text-gray-600">
                    No se encontraron recolecciones para los filtros seleccionados.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
