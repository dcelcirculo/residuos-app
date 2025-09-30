<?php

namespace App\Models;

// Importa los traits y clases necesarios de Eloquent
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Define el modelo Solicitud que extiende de Model
class Solicitud extends Model
{
    // Usa el trait HasFactory para permitir la creación de factories
    use HasFactory;

    // Especifica el nombre de la tabla asociada al modelo
    protected $table = 'solicitudes';

    // Define los atributos que se pueden asignar de manera masiva
    protected $fillable = [
        'user_id',           // ID del usuario que realiza la solicitud
        'tipo_residuo',      // Tipo de residuo solicitado
        'fecha_programada',  // Fecha en la que se programa la recolección
        'frecuencia',        // Frecuencia de la recolección
        'estado',            // Estado actual de la solicitud
    ];

    // Relación: una solicitud pertenece a un usuario
    public function user()
    {
        // Define la relación de pertenencia con el modelo User
        return $this->belongsTo(User::class);
    }

    // Relación: una solicitud puede tener muchas recolecciones asociadas
    public function recolecciones()
    {
        // Define la relación de uno a muchos con el modelo Recoleccion
        return $this->hasMany(Recoleccion::class);
    }
}
