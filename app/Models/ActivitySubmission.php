<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivitySubmission extends Model
{
    // Nombre exacto de tu tabla en la base de datos
    protected $table = 'activity_submissions';

    // Campos habilitados para inserción masiva
    protected $fillable = [
        'user_id',
        'time_block',
        'project_name',
        'activity_title',
        'description',
        'evidence_photo',
        'status', 
        'submitted_at',
    ];

    /**
     * Relación: Un registro de actividad pertenece a un Trabajador (User)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}