<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Activity;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        $this->call([
            RoleSeeder::class,
        ]);

        
        $admRole  = Role::firstOrCreate(['name' => 'Administrador']);
        $userRole = Role::firstOrCreate(['name' => 'Trabajador']);
        $respRole = Role::firstOrCreate(['name' => 'Responsable']);

    
        User::updateOrCreate(
            ['email' => 'jhoel.fernandez@consigueventas.com'], 
            [
                'name' => 'Jhoel',
                'apellidos' => 'Fernandez Alvarado',
                'dni' => '10000001',
                'telefono' => '999888777',
                'password' => Hash::make('admin123'),
                'role_id' => $admRole->id,
            ]
        );

     
        User::updateOrCreate(
            ['email' => 'justin.dev@consigueventas.com'], 
            [
                'name' => 'Justin Marcelo',
                'apellidos' => 'Capcha Ochoa',
                'dni' => '76526948',
                'telefono' => '987654321',
                'password' => Hash::make('practicante123'),
                'role_id' => $userRole->id,
            ]
        );

        
        Activity::updateOrCreate(
            ['title' => 'Revisión y Limpieza de Bandeja de Entrada Corporativa'],
            [
                'description' => 'Verificar correos de clientes y derivar incidencias al equipo de TI.', 
                'time_block' => '08:00 - 9:00',
                'role_id' => null 
            ]
        );

        Activity::updateOrCreate(
            ['title' => 'Cierre y Reporte de Ventas Diario'],
            [
                'description' => 'Subir la captura del arqueo de caja de la plataforma e ingresar balance total.', 
                'time_block' => '16:00 - 17:00',
                'role_id' => null
            ]
        );
    }
}