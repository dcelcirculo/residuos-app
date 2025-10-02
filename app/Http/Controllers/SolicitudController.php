<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SolicitudController extends Controller
{
    /**
     * Mostrar listado de solicitudes del usuario autenticado.
     * Incluye filtros, búsqueda y ordenamiento.
     */
    public function index(Request $request)
    {
        // Construir query dinámica usando filtros y orden
        [$query, $sort, $dir] = $this->buildQuery($request);

        // Paginar resultados y mantener filtros en la URL
        $solicitudes = $query
            ->paginate(10)
            ->appends($request->query());

        return view('solicitudes.index', compact('solicitudes', 'sort', 'dir'));
    }

    /**
     * Exportar listado de solicitudes a un archivo CSV.
     */
    public function export(Request $request)
    {
        [$query] = $this->buildQuery($request);

        // Crear CSV en memoria (UTF-8 con BOM para compatibilidad con Excel)
        $tmp = fopen('php://temp', 'w+');
        fwrite($tmp, "\xEF\xBB\xBF"); // BOM

        // Encabezados del archivo
        fputcsv($tmp, ['ID','Tipo residuo','Frecuencia','Veces semana','Turno ruta','Estado','Fecha programada','Creado'], ';');

        // Filas con los datos de cada solicitud
        foreach ($query->orderBy('id')->cursor() as $s) {
            fputcsv($tmp, [
                $s->id,
                (string) $s->tipo_residuo,
                (string) $s->frecuencia,
                (int) $s->recolecciones_por_semana,
                (string) ($s->turno_ruta ?? ''),
                (string) ($s->estado ?? ''),
                (string) $s->fecha_programada,
                optional($s->created_at)->toDateTimeString(),
            ], ';');
        }

        // Obtener el contenido del CSV
        rewind($tmp);
        $csv = stream_get_contents($tmp);
        fclose($tmp);

        $fileName = 'solicitudes_'.now()->format('Ymd_His').'.csv';

        // Retornar respuesta para descarga
        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            'Content-Length'      => (string) strlen($csv),
            'Cache-Control'       => 'no-store, no-cache, must-revalidate',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ]);
    }

    /**
     * Método privado para construir el query de solicitudes con filtros y ordenamiento.
     * Devuelve [Builder $query, string $sort, string $dir].
     */
    private function buildQuery(Request $request): array
    {
        $user = $request->user();

        // Admins necesitan visibilidad global de solicitudes
        if ($user->isAdmin()) {
            $query = Solicitud::query()->with('user');

            // Filtrar por usuario específico (ID directo)
            if ($request->filled('user_id')) {
                $userId = (int) $request->input('user_id');
                if ($userId > 0) {
                    $query->where('user_id', $userId);
                }
            }

            // Búsqueda por nombre o email del solicitante
            if ($request->filled('user')) {
                $needle = $request->string('user')->trim();
                $query->whereHas('user', function ($sub) use ($needle) {
                    $sub->where('name', 'like', "%{$needle}%")
                        ->orWhere('email', 'like', "%{$needle}%");
                });
            }
        } else {
            // Usuarios normales sólo ven sus propias solicitudes
            $query = $user->solicitudes();
        }

        // 🔍 Búsqueda libre en id, tipo_residuo, frecuencia o estado
        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($qq) use ($q) {
                $qq->where('id', $q)
                   ->orWhere('tipo_residuo', 'like', "%{$q}%")
                   ->orWhere('frecuencia', 'like', "%{$q}%")
                   ->orWhere('estado', 'like', "%{$q}%");
            });
        }

        // Filtro por estado (si no es "todos")
        if ($request->filled('estado') && $request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }

        // Filtros por fechas
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->date('to'));
        }

        // Ordenar resultados (default: created_at desc)
        $sort = in_array($request->get('sort'), ['id','created_at','fecha_programada','estado','recolecciones_por_semana','turno_ruta'])
            ? $request->get('sort')
            : 'created_at';
        $dir  = in_array($request->get('dir'),  ['asc','desc'])
            ? $request->get('dir')
            : 'desc';

        $query->orderBy($sort, $dir);

        return [$query, $sort, $dir];
    }

    /**
     * Mostrar formulario para crear una nueva solicitud.
     */
    public function create()
    {
        return view('solicitudes.create');
    }

    /**
     * Guardar una nueva solicitud en la base de datos.
     */
    public function store(Request $request)
    {
        // Validación de datos del formulario
        $data = $request->validate([
            'tipo_residuo'     => 'required|in:organico,inorganico,peligroso',
            'fecha_programada' => 'required|date',
            'frecuencia'       => 'required|in:programada,demanda',
            'recolecciones_por_semana' => 'required|integer|in:1,2',
            'turno_ruta'       => 'required|integer|min:1|max:500',
        ]);

        // Asociar solicitud al usuario autenticado  y estado inicial
        $data['user_id'] = auth()->id();
        $data['estado']  = 'pendiente'; // ← estado por defecto

        // Crear registro en DB
        Solicitud::create($data);

        return redirect()
            ->route('solicitudes.index')
            ->with('ok', 'Solicitud creada');
    }

    /**
     * Mostrar una solicitud específica.
     * (Por implementar si es necesario).
     */
    public function show(Solicitud $solicitud)
    {
        //
    }

    /**
     * Mostrar formulario de edición de una solicitud.
     */
    public function edit(Solicitud $solicitud)
    {
        // Seguridad: sólo el dueño puede editar su solicitud
    abort_unless($solicitud->user_id === auth()->id(), 403);
    return view('solicitudes.edit', compact('solicitud'));
    }

    /**
     * Actualizar una solicitud existente.
     */
    public function update(Request $request, Solicitud $solicitud)
{
    // Seguridad: sólo el dueño puede actualizar su solicitud
    abort_unless($solicitud->user_id === auth()->id(), 403);

    $data = $request->validate([
        'tipo_residuo'     => 'required|in:organico,inorganico,peligroso',
        'fecha_programada' => 'required|date|after_or_equal:today',
        'frecuencia'       => 'required|in:programada,demanda',
        'recolecciones_por_semana' => 'required|integer|in:1,2',
        'turno_ruta'       => 'required|integer|min:1|max:500',
        'estado'           => 'nullable|in:pendiente,recogida,cancelada',
    ]);

    $solicitud->update($data);
    return redirect()->route('solicitudes.index')->with('ok','Solicitud actualizada');
}

    /**
     * Eliminar una solicitud.
     */
    public function destroy(Solicitud $solicitud)
{
    // Seguridad: sólo el dueño puede eliminar su solicitud
    abort_unless($solicitud->user_id === auth()->id(), 403);

    $solicitud->delete();
    return back()->with('ok','Solicitud eliminada');
}
}
