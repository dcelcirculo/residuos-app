<?php

namespace App\Http\Controllers;

use App\Models\Recoleccion;
use App\Models\Solicitud;
use Illuminate\Http\Request;

class RecoleccionController extends Controller
{
    public function index()
    {
        $solicitudesPendientes = Solicitud::where('user_id', auth()->id())
            ->where('estado', 'pendiente')
            ->orderByDesc('fecha_programada')
            ->get(['id','tipo_residuo','fecha_programada','frecuencia','estado']);

        $recolecciones = Recoleccion::whereHas('solicitud', function($q){
                $q->where('user_id', auth()->id());
            })
            ->latest()
            ->paginate(10);

        return view('recolecciones.index', compact('solicitudesPendientes','recolecciones'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'solicitud_id' => 'required|exists:solicitudes,id',
            'kilos'        => 'required|numeric|min:0.1',
        ]);

        $solicitud = Solicitud::where('id', $data['solicitud_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $puntos = (int) floor($data['kilos'] * 10);

        $recoleccion = Recoleccion::create([
            'solicitud_id' => $solicitud->id,
            'user_id'      => auth()->id(),
            'fecha_real'   => now(),
            'kilos'        => $data['kilos'],
            'puntos'       => $puntos,
        ]);

        $solicitud->update(['estado' => 'recogida']);

        return redirect()->route('recolecciones.index')
            ->with('ok', 'Recolección registrada: '.$recoleccion->kilos.' kg, '.$recoleccion->puntos.' puntos.');
    }

    public function update(Request $request, Recoleccion $recoleccion)
    {
        if ($recoleccion->solicitud->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'kilos' => 'required|numeric|min:0.1',
        ]);

        $puntos = (int) floor($data['kilos'] * 10);

        $recoleccion->update([
            'kilos'  => $data['kilos'],
            'puntos' => $puntos,
        ]);

        return back()->with('ok', 'Recolección actualizada.');
    }
}