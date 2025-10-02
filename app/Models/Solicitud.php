<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo: Solicitud
 * --------------------------------------------------------------------------
 * Representa una petición de recolección hecha por un usuario en EcoGestión.
 *
 * Atributos principales:
 * - user_id          : identificador del usuario que crea la solicitud
 * - tipo_residuo     : orgánico | inorgánico | peligroso
 * - fecha_programada : fecha objetivo de la recolección
 * - frecuencia       : programada | demanda
 * - estado           : pendiente | recogida | cancelada (según el flujo)
 *
 * Relaciones:
 * - user()           : belongsTo User (autor de la solicitud)
 * - recolecciones()  : hasMany Recoleccion (eventos de recolección realizados)
 *
 * Notas:
 * - $fillable permite asignación masiva segura en create()/update().
 * - $table fija el nombre de tabla explícitamente (buena práctica documental).
 */
class Solicitud extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla explícito (opcional si el plural fuera regular).
     * En este proyecto lo dejamos para documentar la intención.
     * @var string
     */
    protected $table = 'solicitudes';

    /**
     * Atributos permitidos para asignación masiva.
     * IMPORTANTE: mantén sincronizado este arreglo con las validaciones del controlador.
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',           // Usuario que realiza la solicitud
        'tipo_residuo',      // Orgánico | Inorgánico | Peligroso
        'fecha_programada',  // Fecha programada para la recolección (YYYY-MM-DD)
        'frecuencia',        // programada | demanda
        'recolecciones_por_semana', // Número de recolecciones semanales (1 o 2)
        'turno_ruta',        // Posición dentro de la ruta de recolección
        'estado',            // Estado del ciclo de la solicitud
    ];

    /**
     * Conversión de atributos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_programada' => 'date',
        'recordatorio_prev_enviado_at' => 'datetime',
        'recordatorio_dia_enviado_at' => 'datetime',
        'turno_ruta' => 'integer',
        'recolecciones_por_semana' => 'integer',
    ];

    /**
     * Relación: la solicitud pertenece a un usuario.
     * Permite acceder al autor: $solicitud->user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: la solicitud puede tener varias recolecciones asociadas.
     * Ej.: $solicitud->recolecciones()->latest()->get()
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recolecciones()
    {
        return $this->hasMany(Recoleccion::class);
    }
}
