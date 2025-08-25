<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si el usuario superadmin ya existe
        $existingAdmin = User::where('email', 'admin@example.com')->first();

        if (!$existingAdmin) {
            // Crear usuario superadmin si no existe
            $superadmin = User::create([
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'), // Cambiar en producción
                'email_verified_at' => now(),
            ]);

            // Asignar rol superadmin
            $superadmin->assignRole('superadmin');
        } else {
            // Si el usuario ya existe, asegurarse de que tenga el rol correcto
            if (!$existingAdmin->hasRole('superadmin')) {
                $existingAdmin->assignRole('superadmin');
            }
        }
    }
}
