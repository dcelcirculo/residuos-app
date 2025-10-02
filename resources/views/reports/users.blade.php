<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reporte global por usuarios') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Localidad</label>
                        <select name="localidad" class="mt-1 w-full border-gray-300 rounded">
                            <option value="">Todas</option>
                            @foreach($localidades as $item)
                                <option value="{{ $item }}" @selected($localidad == $item)>{{ $item }}</option>
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
                        <a href="{{ route('reports.users') }}" class="px-4 py-2 border rounded">Limpiar</a>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                @if($summary->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-3 py-2 border">Tipo de residuo</th>
                                    <th class="px-3 py-2 border">Total recolecciones</th>
                                    <th class="px-3 py-2 border">Kilos</th>
                                    <th class="px-3 py-2 border">Puntos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($summary as $row)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 border">{{ ucfirst($row->tipo_residuo) }}</td>
                                        <td class="px-3 py-2 border">{{ $row->total_recolecciones }}</td>
                                        <td class="px-3 py-2 border">{{ number_format($row->kilos, 2) }}</td>
                                        <td class="px-3 py-2 border">{{ number_format($row->puntos) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-600">No se encontraron datos para los filtros seleccionados.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
