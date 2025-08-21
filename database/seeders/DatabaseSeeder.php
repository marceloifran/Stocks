<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Roles y permisos primero
            RolesAndPermissionsSeeder::class,
            // Luego superadmin
            SuperAdminSeeder::class,
            // Y el resto de seeders
            UserSeeder::class,
            PersonalTableSeeder::class,
            StocksTableSeeder::class,
            HuellaCarbonoParametrosSeeder::class,
        ]);
    }
}
