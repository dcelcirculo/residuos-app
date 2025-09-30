{{-- 
    Vista: Crear Solicitud
    -------------------------------------------------------------
    Muestra el formulario para que un usuario registre una nueva
    solicitud de recolección (tipo de residuo, fecha y frecuencia).
--}}

<x-app-layout>
    {{-- Encabezado (slot "header" del layout) --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nueva Solicitud de Recolección') }}
        </h2>
    </x-slot>

    <div class="py-6">
        {{-- Contenedor centrado y con ancho máximo cómodo --}}
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            {{-- Tarjeta visual (panel blanco con sombra y bordes redondeados) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- Formulario de creación de solicitud
                         - method POST: crea un nuevo recurso
                         - action: ruta nombrada a store() en SolicitudController --}}
                    <form method="POST" action="{{ route('solicitudes.store') }}">
                        @csrf {{-- Token anti-CSRF obligatorio en formularios POST --}}

                        {{-- Campo: Tipo de residuo --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">
                                Tipo de residuo
                            </label>
                            {{-- Selector con las opciones que el backend valida:
                                 organico | inorganico | peligroso --}}
                            <select name="tipo_residuo" class="mt-1 block w-full border-gray-300 rounded" required>
                                <option value="organico">Orgánico</option>
                                <option value="inorganico">Inorgánico</option>
                                <option value="peligroso">Peligroso</option>
                            </select>
                            {{-- Mensaje mostrado si la validación del backend falla para este campo --}}
                            @error('tipo_residuo')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Campo: Fecha programada de la recolección --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">
                                Fecha programada
                            </label>
                            {{-- Input tipo date; requerido por validación --}}
                            <input 
                                type="date" 
                                name="fecha_programada" 
                                class="mt-1 block w-full border-gray-300 rounded" 
                                required
                            >
                            {{-- Mensaje de error de validación para la fecha --}}
                            @error('fecha_programada')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Campo: Frecuencia de la recolección --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700">
                                Frecuencia
                            </label>
                            {{-- Selector con opciones validadas en el backend:
                                 programada | demanda --}}
                            <select name="frecuencia" class="mt-1 block w-full border-gray-300 rounded" required>
                                <option value="programada">Programada</option>
                                <option value="demanda">Por demanda</option>
                            </select>
                            {{-- Mensaje de error de validación para la frecuencia --}}
                            @error('frecuencia')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Acciones del formulario --}}
                        <div class="flex items-center gap-3">
                            {{-- Cancelar: vuelve al índice/listado de solicitudes sin crear nada --}}
                            <a href="{{ route('solicitudes.index') }}" class="px-4 py-2 border rounded">
                                Cancelar
                            </a>
                            {{-- Guardar: envía el formulario y crea la solicitud --}}
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">
                                Guardar solicitud
                            </button>
                        </div>
                    </form>
                    {{-- Fin del formulario --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>