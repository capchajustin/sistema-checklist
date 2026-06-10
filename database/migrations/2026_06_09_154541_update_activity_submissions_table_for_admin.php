<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_submissions', function (Blueprint $table) {
            // Vincula la actividad con el ID del trabajador (si no existe ya)
            if (!Schema::hasColumn('activity_submissions', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade')->after('id');
            }
            
            // Columna de estado para la aprobación del Administrador
            if (!Schema::hasColumn('activity_submissions', 'status')) {
                $table->string('status')->default('pending')->after('evidence_photo'); 
                // Valores: 'pending', 'approved' (apto), 'rejected' (no apto)
            }
        });
    }

    public function down(): void
    {
        Schema::table('activity_submissions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'status']);
        });
    }
};