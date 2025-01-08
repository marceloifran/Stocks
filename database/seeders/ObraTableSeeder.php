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
            'presupuesto' => 100000,
            // ...otros campos si corresponde...
        ]);

        Obra::create([
            'nombre' => 'Obra Dos',
            'estado' => 'En Proceso',
            'presupuesto' => 200000,
            // ...otros campos si corresponde...
        ]);
    }
}
