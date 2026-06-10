<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    // Permitir la asignación masiva de los campos operativos
    protected $fillable = [
        'title', 
        'description', 
        'time_block', 
        'role_id'
    ];

    /**
     * Relación: Una actividad pertenece a un Rol específico (o es global si es null).
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relación: Una actividad puede tener muchas entregas/respuestas de evidencia.
     */
    public function submissions()
    {
        return $this->hasMany(ActivitySubmission::class);
    }
}