<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Ejemplo: "Verificar servidores de la web"
            $table->text('description')->nullable();
            $table->string('time_block'); // Ejemplo: "08:00 - 10:00", "14:00 - 16:00"
            
            // Relación opcional para segmentar por roles (Administrador, Responsable, Trabajador)
            $table->foreignId('role_id')->nullable()->constrained()->onDelete('cascade');

            $table->timestamps();
        });

        // Tabla pivot o registro de cumplimiento diario por usuario
       Schema::create('activity_submissions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    
    // Campos dinámicos que rellenará el trabajador por bloque
    $table->string('time_block');      // Ej: "08:00 - 09:00"
    $table->string('project_name');    // Nombre del proyecto asignado
    $table->string('activity_title');  // Nombre de la actividad realizada
    $table->text('description');       // Descripción del trabajo diario
    $table->string('evidence_photo');  // Foto de evidencia
    
    $table->date('submitted_at');      // Día del registro (Y-m-d)
    $table->timestamps();
    
    // Regla de seguridad: Un trabajador solo puede subir UN registro por bloque horario al día
    $table->unique(['user_id', 'time_block', 'submitted_at'], 'user_block_date_unique');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_submissions');
        Schema::dropIfExists('activities');
    }
};