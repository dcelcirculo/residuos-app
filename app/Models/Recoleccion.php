<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo: Recoleccion
 * --------------------------------------------------------------------------
 * Representa un evento de recolección ejecutado para una Solicitud.
 *
 * Atributos principales:
 * - solicitud_id : referencia a la solicitud atendida
 * - user_id      : recolector (usuario) que ejecutó la recolección
 * - fecha_real   : timestamp/fecha en que ocurrió la recolección
 * - kilos        : cantidad recolectada (kg)
 * - puntos       : puntaje calculado (ej. kilos * 10)
 *
 * Relaciones:
 * - solicitud()  : belongsTo Solicitud
 * - recolector() : belongsTo User (rol: recolector)
 *
 * Notas:
 * - $fillable habilita asignación masiva en create()/update().
 * - $table fija el nombre correcto en plural "recolecciones".
 */
class Recoleccion extends Model
{   
    /**
     * Campos permitidos para asignación masiva.
     * Mantener este arreglo alineado con las reglas de validación del controlador.
     * @var array<int, string>
     */
    protected $fillable = ['solicitud_id','user_id','fecha_real','kilos','puntos'];

    /**
     * Relación: esta recolección pertenece a una Solicitud.
     * Permite: $recoleccion->solicitud
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function solicitud(){
        return $this->belongsTo(\App\Models\Solicitud::class);
    }

    /**
     * Relación: esta recolección pertenece a un Usuario (recolector).
     * Campo foráneo específico: user_id.
     * Permite: $recoleccion->recolector
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recolector(){
        return $this->belongsTo(\App\Models\User::class,'user_id');
    }

    /**
     * Nombre explícito de la tabla (evita que Eloquent suponga "recoleccions").
     * @var string
     */
    protected $table = 'recolecciones';
}