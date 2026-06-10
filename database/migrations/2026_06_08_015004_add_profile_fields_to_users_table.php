<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Laravel Breeze por defecto trae 'name', lo mantendremos para los nombres
            // Añadimos los apellidos, dni y teléfono cuidando que no rompan registros existentes
            $table->string('apellidos')->nullable()->after('name');
            $table->string('dni', 8)->nullable()->unique()->after('apellidos');
            $table->string('telefono')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['apellidos', 'dni', 'telefono', 'avatar']);
        });
    }
};