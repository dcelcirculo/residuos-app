<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modelo: Recoleccion
 * --------------------------------------------------------------------------
 * Representa un evento de recolección ejecutado para una Solicitud.
 *
 * Atributos principales:
 * - solicitud_id : referencia a la solicitud atendida
 * - user_id      : empresa recolectora (usuario) que ejecutó la recolección
 * - fecha_real   : timestamp/fecha en que ocurrió la recolección
 * - kilos        : cantidad recolectada (kg)
 * - puntos       : puntaje calculado (ej. kilos * 10)
 *
 * Relaciones:
 * - solicitud()  : belongsTo Solicitud
 * - recolector() : belongsTo User (rol empresa recolectora)
 *
 * Notas:
 * - $fillable habilita asignación masiva en create()/update().
 * - $table fija el nombre correcto en plural "recolecciones".
 */
class Recoleccion extends Model
{
    use HasFactory;

    /**
     * Campos permitidos para asignación masiva.
     * Mantener este arreglo alineado con las reglas de validación del controlador.
     * @var array<int, string>
     */
    protected $fillable = ['solicitud_id','user_id','fecha_real','kilos','puntos','cumple_separacion'];

    /**
     * Conversión de atributos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_real' => 'datetime',
        'kilos' => 'decimal:2',
        'cumple_separacion' => 'boolean',
    ];

    /**
     * Relación: esta recolección pertenece a una Solicitud.
     * Permite: $recoleccion->solicitud
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function solicitud(){
        return $this->belongsTo(\App\Models\Solicitud::class);
    }

    /**
     * Relación: esta recolección pertenece a una empresa recolectora (usuario).
     * Campo foráneo específico: user_id.
     * Permite: $recoleccion->recolector (empresa)
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
