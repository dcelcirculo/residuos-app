<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SolicitudController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $solicitudes = auth()->user()
    //     ->solicitudes()
    //     ->latest()
    //     ->paginate(10);

    //     return view('solicitudes.index', compact('solicitudes'));
    // }
    public function index(Request $request)
    {
        [$query, $sort, $dir] = $this->buildQuery($request);

        $solicitudes = $query
            ->paginate(10)
            ->appends($request->query());

        return view('solicitudes.index', compact('solicitudes', 'sort', 'dir'));
    }

    public function export(\Illuminate\Http\Request $request)
    {
        [$query] = $this->buildQuery($request);

        // Construimos el CSV en memoria (UTF-8 con BOM para Excel)
        $tmp = fopen('php://temp', 'w+');
        fwrite($tmp, "\xEF\xBB\xBF"); // BOM

        // Cabecera
        fputcsv($tmp, ['ID','Tipo residuo','Frecuencia','Estado','Fecha programada','Creado'], ';');

        // Datos
        foreach ($query->orderBy('id')->cursor() as $s) {
            fputcsv($tmp, [
                $s->id,
                (string) $s->tipo_residuo,
                (string) $s->frecuencia,
                (string) ($s->estado ?? ''),
                (string) $s->fecha_programada,
                optional($s->created_at)->toDateTimeString(),
            ], ';');
        }

        rewind($tmp);
        $csv = stream_get_contents($tmp);
        fclose($tmp);

        $fileName = 'solicitudes_'.now()->format('Ymd_His').'.csv';

        // Enviamos respuesta binaria con cabeceras de descarga
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
     * Aplica filtros/orden al listado de solicitudes del usuario actual.
     * Devuelve [Builder $query, string $sort, string $dir]
     */
    private function buildQuery(Request $request): array
    {
        $user = $request->user();
        $query = $user->solicitudes(); // parte desde relación del usuario

        // Búsqueda libre (ajusta campos si necesitas)
        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($qq) use ($q) {
                $qq->where('id', $q)
                   ->orWhere('tipo_residuo', 'like', "%{$q}%")
                   ->orWhere('frecuencia', 'like', "%{$q}%")
                   ->orWhere('estado', 'like', "%{$q}%");
            });
        }

        // Filtros
        if ($request->filled('estado') && $request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->date('to'));
        }

        // Orden
        $sort = in_array($request->get('sort'), ['id','created_at','fecha_programada','estado']) ? $request->get('sort') : 'created_at';
        $dir  = in_array($request->get('dir'),  ['asc','desc']) ? $request->get('dir')  : 'desc';

        $query->orderBy($sort, $dir);

        return [$query, $sort, $dir];
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('solicitudes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\Illuminate\Http\Request $request){
        $data = $request->validate([
        'tipo_residuo'     => 'required|in:organico,inorganico,peligroso',
        'fecha_programada' => 'required|date',
        'frecuencia'       => 'required|in:programada,demanda',
    ]);

    $data['user_id'] = auth()->id();

    \App\Models\Solicitud::create($data);

    return redirect()
        ->route('solicitudes.index')
        ->with('ok', 'Solicitud creada');
    }

    /**
     * Display the specified resource.
     */
    public function show(Solicitud $solicitud)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Solicitud $solicitud)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Solicitud $solicitud)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Solicitud $solicitud)
    {
        //
    }
}
