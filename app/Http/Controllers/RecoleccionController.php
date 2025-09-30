<?php

namespace App\Http\Controllers;

use App\Models\Recoleccion;
use App\Models\Solicitud;
use Illuminate\Http\Request;

/**
 * Controlador: RecoleccionController
 * --------------------------------------------------------------------------
 * Gestiona el flujo de recolecciones (registro y actualización) asociado
 * a las solicitudes de un usuario en EcoGestión.
 *
 * Métodos:
 * - index()  : muestra formulario para registrar una recolección y el historial.
 * - store()  : guarda una nueva recolección y marca la solicitud como "recogida".
 * - update() : permite ajustar kilos/puntos de una recolección existente.
 */
class RecoleccionController extends Controller
{
    /**
     * Mostrar el formulario de registro de recolección + historial de recolecciones
     * del usuario autenticado.
     *
     * Vista: resources/views/recolecciones/index.blade.php
     */
    public function index()
    {
        // 1) Traer solicitudes "pendientes" del usuario autenticado para poder asociarles una recolección.
        //    Se seleccionan sólo los campos necesarios para optimizar.
        $solicitudesPendientes = Solicitud::where('user_id', auth()->id())
            ->where('estado', 'pendiente')
            ->orderByDesc('fecha_programada')
            ->get(['id','tipo_residuo','fecha_programada','frecuencia','estado']);

        // 2) Traer recolecciones del usuario (vía relación con solicitud).
        //    whereHas: asegura que la recolección pertenezca a una solicitud creada por el usuario autenticado.
        //    latest(): orden descendente por columna de tiempo (created_at por defecto).
        //    paginate(10): paginación de 10 por página.
        $recolecciones = Recoleccion::whereHas('solicitud', function($q){
                $q->where('user_id', auth()->id());
            })
            ->latest()
            ->paginate(10);

        // 3) Renderizar la vista con ambos datasets.
        return view('recolecciones.index', compact('solicitudesPendientes','recolecciones'));
    }

    /**
     * Registrar una nueva recolección asociada a una solicitud del usuario.
     * Cambia el estado de la solicitud a "recogida".
     */
    public function store(Request $request)
    {
        // 1) Validar datos de entrada:
        //    - solicitud_id debe existir en la tabla 'solicitudes'
        //    - kilos debe ser numérico y > 0
        $data = $request->validate([
            'solicitud_id' => 'required|exists:solicitudes,id',
            'kilos'        => 'required|numeric|min:0.1',
        ]);

        // 2) Verificar que la solicitud pertenece al usuario autenticado.
        //    firstOrFail() lanzará 404 si no existe o no coincide el user_id.
        $solicitud = Solicitud::where('id', $data['solicitud_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // 3) Calcular los puntos (regla simple: 10 puntos por kilo, redondeando hacia abajo).
        $puntos = (int) floor($data['kilos'] * 10);

        // 4) Crear la recolección con los datos calculados.
        $recoleccion = Recoleccion::create([
            'solicitud_id' => $solicitud->id,    // FK a la solicitud
            'user_id'      => auth()->id(),      // recolector = usuario autenticado
            'fecha_real'   => now(),             // marca de tiempo actual
            'kilos'        => $data['kilos'],
            'puntos'       => $puntos,
        ]);

        // 5) Actualizar estado de la solicitud a "recogida".
        $solicitud->update(['estado' => 'recogida']);

        // 6) Redirigir a la vista de índice con mensaje de confirmación.
        return redirect()->route('recolecciones.index')
            ->with('ok', 'Recolección registrada: '.$recoleccion->kilos.' kg, '.$recoleccion->puntos.' puntos.');
    }

    /**
     * Actualizar una recolección existente (kilos/puntos).
     * Sólo se permite si la recolección corresponde a una solicitud del usuario autenticado.
     */
    public function update(Request $request, Recoleccion $recoleccion)
    {
        // 1) Regla de autorización: la solicitud asociada debe ser del usuario autenticado.
        if ($recoleccion->solicitud->user_id !== auth()->id()) {
            abort(403); // Prohibido si intenta modificar algo que no es suyo.
        }

        // 2) Validar el dato de kilos (mismo criterio del store).
        $data = $request->validate([
            'kilos' => 'required|numeric|min:0.1',
        ]);

        // 3) Recalcular puntos con la nueva cantidad de kilos.
        $puntos = (int) floor($data['kilos'] * 10);

        // 4) Persistir cambios en la recolección.
        $recoleccion->update([
            'kilos'  => $data['kilos'],
            'puntos' => $puntos,
        ]);

        // 5) Volver a la pantalla anterior con mensaje de éxito.
        return back()->with('ok', 'Recolección actualizada.');
    }
}