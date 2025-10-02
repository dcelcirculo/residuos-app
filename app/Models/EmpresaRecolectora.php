<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpresaRecolectora extends Model
{
    protected $fillable = ['user_id', 'nombre', 'especialidades'];

    protected $casts = [
        'especialidades' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
