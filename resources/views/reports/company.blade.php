<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reporte por empresa recolectora') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Empresa</label>
                        <select name="company_id" class="mt-1 w-full border-gray-300 rounded" required>
                            <option value="">Seleccione…</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" @selected($companyId == $company->id)>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo de residuo</label>
                        <select name="tipo_residuo" class="mt-1 w-full border-gray-300 rounded">
                            <option value="">Todos</option>
                            @foreach($especialidades as $tipo)
                                <option value="{{ $tipo }}" @selected($tipoResiduo === $tipo)>{{ ucfirst($tipo) }}</option>
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
                        <a href="{{ route('reports.company') }}" class="px-4 py-2 border rounded">Limpiar</a>
                    </div>
                </form>
            </div>

            @if($companyId && $records->isNotEmpty())
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <div class="mb-4 flex flex-wrap gap-6 text-sm text-gray-700">
                        <div><strong>Total de recolecciones:</strong> {{ $records->count() }}</div>
                        <div><strong>Kilos recolectados:</strong> {{ number_format($totals['kilos'], 2) }}</div>
                        <div><strong>Puntos otorgados:</strong> {{ number_format($totals['puntos']) }}</div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-3 py-2 border">Fecha</th>
                                    <th class="px-3 py-2 border">Hora</th>
                                    <th class="px-3 py-2 border">Vecino</th>
                                    <th class="px-3 py-2 border">Tipo</th>
                                    <th class="px-3 py-2 border">Kilos</th>
                                    <th class="px-3 py-2 border">Puntos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($records as $row)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 border">{{ optional($row->fecha_real)->format('Y-m-d') }}</td>
                                        <td class="px-3 py-2 border">{{ optional($row->fecha_real)->format('H:i') }}</td>
                                        <td class="px-3 py-2 border">{{ optional(optional($row->solicitud)->user)->name ?? '—' }}</td>
                                        <td class="px-3 py-2 border">{{ ucfirst($row->solicitud->tipo_residuo ?? '—') }}</td>
                                        <td class="px-3 py-2 border">{{ number_format($row->kilos, 2) }}</td>
                                        <td class="px-3 py-2 border">{{ number_format($row->puntos) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @elseif($companyId)
                <div class="bg-white shadow sm:rounded-lg p-6 text-sm text-gray-600">
                    No se encontraron recolecciones para los filtros seleccionados.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
