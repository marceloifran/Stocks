<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\personal;

class personalTableSeeder extends Seeder
{
    public function run()
    {
        personal::create([
            'nombre' => 'Juan Pérez',
            'dni' => '12345678',
            'nro_identificacion' => 'ID001',
            'obra_id' => 1,
        ]);

        personal::create([
            'nombre' => 'María López',
            'dni' => '87654321',
            'nro_identificacion' => 'ID002',
            'obra_id' => 1,

        ]);

        personal::create([
            'nombre' => 'Carlos García',
            'dni' => '11223344',
            'nro_identificacion' => 'ID003',
            'obra_id' => 2,

        ]);

        personal::create([
            'nombre' => 'Ana Torres',
            'dni' => '44332211',
            'nro_identificacion' => 'ID004',
            'obra_id' => 2,

        ]);

    }
}
