<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Desactivar temporalmente las restricciones de llaves foráneas para poder limpiar la tabla
        Schema::disableForeignKeyConstraints();
        
        // Vaciar por completo la tabla para asegurar que no existan remanentes
        DB::table('roles')->truncate();
        
        // Reactivar las restricciones de llaves foráneas
        Schema::enableForeignKeyConstraints();

        // Registrar los roles limpios sin riesgo de duplicados
        $roles = ['Administrador', 'Responsable', 'Trabajador'];

        foreach ($roles as $roleName) {
            Role::create(['name' => $roleName]);
        }
    }
}