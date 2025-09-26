<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nueva Solicitud de Recolección') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('solicitudes.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Tipo de residuo</label>
                            <select name="tipo_residuo" class="mt-1 block w-full border-gray-300 rounded" required>
                                <option value="organico">Orgánico</option>
                                <option value="inorganico">Inorgánico</option>
                                <option value="peligroso">Peligroso</option>
                            </select>
                            @error('tipo_residuo')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Fecha programada</label>
                            <input type="date" name="fecha_programada" class="mt-1 block w-full border-gray-300 rounded" required>
                            @error('fecha_programada')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700">Frecuencia</label>
                            <select name="frecuencia" class="mt-1 block w-full border-gray-300 rounded" required>
                                <option value="programada">Programada</option>
                                <option value="demanda">Por demanda</option>
                            </select>
                            @error('frecuencia')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('solicitudes.index') }}" class="px-4 py-2 border rounded">Cancelar</a>
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Guardar solicitud</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>