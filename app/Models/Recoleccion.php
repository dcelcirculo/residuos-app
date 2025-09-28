<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recoleccion extends Model
{   
    
    protected $fillable = ['solicitud_id','user_id','fecha_real','kilos','puntos'];

    public function solicitud(){
        return $this->belongsTo(\App\Models\Solicitud::class);
    }

    public function recolector(){
        return $this->belongsTo(\App\Models\User::class,'user_id');
    }

    // Fuerza el nombre correcto de la tabla
    protected $table = 'recolecciones';
}
