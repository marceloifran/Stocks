<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\stock;
use Carbon\Carbon;

class stocksTableSeeder extends Seeder
{
    public function run()
    {
        stock::create([
            'nombre' => 'Excavadora',
            'fecha' => Carbon::now()->format('Y-m-d'),
            'cantidad' => 5,
            'descripcion' => 'Excavadora de gran capacidad para movimiento de tierra.',
            'unidad_medida' => 'unidad',
            'tipo_stock' => 'maquinaria',
            'precio' => 150000.00,
        ]);

        stock::create([
            'nombre' => 'Camión de carga',
            'fecha' => Carbon::now()->format('Y-m-d'),
            'cantidad' => 10,
            'descripcion' => 'Camiones para transporte de materiales.',
            'unidad_medida' => 'unidad',
            'tipo_stock' => 'vehículo',
            'precio' => 80000.00,
        ]);

        stock::create([
            'nombre' => 'Perforadora',
            'fecha' => Carbon::now()->format('Y-m-d'),
            'cantidad' => 3,
            'descripcion' => 'Perforadora para extracción de minerales.',
            'unidad_medida' => 'unidad',
            'tipo_stock' => 'maquinaria',
            'precio' => 120000.00,
        ]);

        stock::create([
            'nombre' => 'Pala mecánica',
            'fecha' => Carbon::now()->format('Y-m-d'),
            'cantidad' => 4,
            'descripcion' => 'Pala mecánica para movimiento de tierra.',
            'unidad_medida' => 'unidad',
            'tipo_stock' => 'maquinaria',
            'precio' => 95000.00,
        ]);

        stock::create([
            'nombre' => 'Cemento',
            'fecha' => Carbon::now()->format('Y-m-d'),
            'cantidad' => 200,
            'descripcion' => 'Sacos de cemento para construcción.',
            'unidad_medida' => 'saco',
            'tipo_stock' => 'material',
            'precio' => 5.00,
        ]);

        // Agrega más elementos según sea necesario
    }
}
