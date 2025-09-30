<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Modelo: User
 * --------------------------------------------------------------------------
 * Representa a los usuarios del sistema EcoGestión.
 *
 * Atributos principales (tabla users):
 * - id       : identificador único
 * - name     : nombre del usuario
 * - email    : correo electrónico
 * - password : contraseña (encriptada)
 * - role     : rol dentro del sistema (user | admin | recolector)
 *
 * Relaciones:
 * - solicitudes()   : hasMany Solicitud (usuarios que crean solicitudes)
 * - recolecciones() : hasMany Recoleccion (usuarios con rol recolector)
 *
 * Métodos útiles:
 * - isAdmin()     : true si el usuario es administrador
 * - isUser()      : true si es usuario normal
 * - isRecolector(): true si es recolector
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Atributos que se pueden asignar masivamente.
     * IMPORTANTE: incluir "role" porque lo agregamos en la migración.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Atributos que deben ocultarse en arrays/JSON.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atributos que deben ser casteados a tipos específicos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Relación: un usuario puede tener varias solicitudes.
     */
    public function solicitudes()
    {
        return $this->hasMany(\App\Models\Solicitud::class);
    }

    /**
     * Relación: un usuario (recolector) puede tener varias recolecciones.
     */
    public function recolecciones()
    {
        return $this->hasMany(\App\Models\Recoleccion::class);
    }

    /**
     * Verifica si el usuario es administrador.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Verifica si el usuario es un usuario normal.
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Verifica si el usuario es recolector.
     */
    public function isRecolector(): bool
    {
        return $this->role === 'recolector';
    }
}