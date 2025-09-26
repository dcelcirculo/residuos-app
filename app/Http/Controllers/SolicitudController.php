<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $solicitudes = auth()->user()
        ->solicitudes()
        ->latest()
        ->paginate(10);

        return view('solicitudes.index', compact('solicitudes'));
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
