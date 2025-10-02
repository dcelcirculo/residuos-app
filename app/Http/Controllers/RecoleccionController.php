<?php

namespace App\Http\Controllers;

use App\Models\Recoleccion;
use App\Models\Solicitud;
use App\Models\User;
use App\Services\PointsCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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
    public function __construct(private readonly PointsCalculator $pointsCalculator)
    {
    }

    /**
     * Mostrar el formulario de registro de recolección + historial de recolecciones
     * del usuario autenticado.
     *
     * Vista: resources/views/recolecciones/index.blade.php
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->isEmpresaRecolectora()) {
            $especialidades = $user->empresaRecolectora?->especialidades ?? [];

            $solicitudesPendientes = empty($especialidades)
                ? collect()
                : Solicitud::with('user')
                    ->whereIn('tipo_residuo', $especialidades)
                    ->whereIn('estado', ['pendiente', 'confirmada'])
                    ->orderBy('fecha_programada')
                    ->orderBy('turno_ruta')
                    ->get(['id','user_id','tipo_residuo','fecha_programada','frecuencia','estado','turno_ruta']);
        } else {
            // Ciudadanos: sólo sus propias solicitudes pendientes.
            $solicitudesPendientes = Solicitud::where('user_id', $user->id)
                ->where('estado', 'pendiente')
                ->orderByDesc('fecha_programada')
                ->get(['id','tipo_residuo','fecha_programada','frecuencia','estado']);
        }

        $recoleccionesQuery = $this->historyQuery($user);

        $recolecciones = (clone $recoleccionesQuery)
            ->orderByDesc('fecha_real')
            ->paginate(10);

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
            'cumple_separacion' => 'required|boolean',
        ]);

        $user = auth()->user();

        if ($user->isEmpresaRecolectora()) {
            $solicitud = Solicitud::with('user')->findOrFail($data['solicitud_id']);

            $especialidades = $user->empresaRecolectora?->especialidades ?? [];

            abort_unless(in_array($solicitud->tipo_residuo, $especialidades, true), 403, 'La empresa no atiende este tipo de residuo.');
            abort_if($solicitud->estado === 'recogida', 422, 'Esta solicitud ya fue marcada como recogida.');
        } else {
            // Ciudadano registrando su propia recolección.
            $solicitud = Solicitud::where('id', $data['solicitud_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();

            abort_if($solicitud->estado === 'recogida', 422, 'Esta solicitud ya fue marcada como recogida.');
        }

        // 3) Calcular puntos según cumplimiento y fórmula dinámica.
        $cumple = (bool) $data['cumple_separacion'];
        $puntos = $cumple ? $this->pointsCalculator->calculate((float) $data['kilos']) : 0;

        // 4) Crear la recolección con los datos calculados.
        $recoleccion = Recoleccion::create([
            'solicitud_id' => $solicitud->id,
            'user_id'      => $user->id,
            'fecha_real'   => now(),
            'kilos'        => $data['kilos'],
            'cumple_separacion' => $cumple,
            'puntos'       => $puntos,
        ]);

        // 5) Actualizar estado de la solicitud a "recogida" y dejar registro de la empresa recolectora.
        $solicitud->update(['estado' => 'recogida']);

        // 6) Redirigir a la vista de índice con mensaje de confirmación.
        return redirect()->route('recolecciones.index')
            ->with('ok', 'Recolección registrada: '.$recoleccion->kilos.' kg, '.$recoleccion->puntos.' puntos.');
    }

    /**
     * Exporta el historial de recolecciones del usuario autenticado.
     */
    public function export()
    {
        $user = auth()->user();

        abort_if($user->isAdmin(), 403);

        $query = $this->historyQuery($user)->orderByDesc('fecha_real');

        $fileName = sprintf('recolecciones_%s_%s.csv', $user->id, now()->format('Ymd_His'));

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function () use ($query, $user) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            if ($user->isEmpresaRecolectora()) {
                fputcsv($handle, ['ID','Fecha','Hora','Vecino','Kilos'], ';');
            } else {
                fputcsv($handle, ['ID','Fecha','Hora','Empresa','Kilos'], ';');
            }

            foreach ($query->cursor() as $recoleccion) {
                $fecha = $recoleccion->fecha_real instanceof Carbon
                    ? $recoleccion->fecha_real
                    : ($recoleccion->fecha_real ? Carbon::parse($recoleccion->fecha_real) : null);

                $row = [
                    $recoleccion->id,
                    optional($fecha)->format('Y-m-d'),
                    optional($fecha)->format('H:i'),
                ];

                if ($user->isEmpresaRecolectora()) {
                    $row[] = optional(optional($recoleccion->solicitud)->user)->name;
                } else {
                    $row[] = optional($recoleccion->recolector)->name;
                }

                $row[] = number_format((float) $recoleccion->kilos, 2, '.', '');

                fputcsv($handle, $row, ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function historyQuery(User $user)
    {
        if ($user->isEmpresaRecolectora()) {
            return Recoleccion::with(['solicitud.user'])
                ->where('user_id', $user->id);
        }

        return Recoleccion::with(['recolector'])
            ->whereHas('solicitud', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
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
            'cumple_separacion' => 'required|boolean',
        ]);

        // 3) Recalcular puntos con la nueva cantidad de kilos.
        $cumple = (bool) $data['cumple_separacion'];
        $puntos = $cumple ? $this->pointsCalculator->calculate((float) $data['kilos']) : 0;

        // 4) Persistir cambios en la recolección.
        $recoleccion->update([
            'kilos'  => $data['kilos'],
            'cumple_separacion' => $cumple,
            'puntos' => $puntos,
        ]);

        // 5) Volver a la pantalla anterior con mensaje de éxito.
        return back()->with('ok', 'Recolección actualizada.');
    }
}
