<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Obra;

class ObraTableSeeder extends Seeder
{
    public function run()
    {
        Obra::create([

            'nombre' => 'Obra Uno',
            'estado' => 'En Proceso',
            'fecha_arranque' => '2025-01-01',
            'fecha_final' => '2025-12-31',
            // ...otros campos si corresponde...
        ]);

        Obra::create([
            'nombre' => 'Obra Dos',
            'estado' => 'En Proceso',
            'fecha_arranque' => '2025-01-01',
            'fecha_final' => '2026-12-31',
            // ...otros campos si corresponde...
        ]);
    }
}
