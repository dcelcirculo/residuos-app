<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = [
        'user_id',
        'tipo_residuo',
        'fecha_programada',
        'frecuencia',
        'estado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recolecciones()
    {
        return $this->hasMany(Recoleccion::class);
    }
}
