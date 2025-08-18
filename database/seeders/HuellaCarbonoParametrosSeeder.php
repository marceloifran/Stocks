<?php

namespace Database\Seeders;

use App\Models\HuellaCarbonoParametro;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class HuellaCarbonoParametrosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener los factores de conversión de la configuración
        $factores = Config::get('huella_carbono.factores_conversion');
        $tiposFuente = Config::get('huella_carbono.tipos_fuente');

        // Combustibles
        foreach ($tiposFuente['combustible'] as $tipo => $descripcion) {
            HuellaCarbonoParametro::create([
                'categoria' => 'combustible',
                'tipo' => $tipo,
                'descripcion' => $descripcion,
                'factor_conversion' => $factores[$tipo] ?? 0,
                'unidad_medida' => 'litros',
                'unidad_resultado' => 'kgCO2e',
                'activo' => true,
            ]);
        }

        // Electricidad
        foreach ($tiposFuente['electricidad'] as $tipo => $descripcion) {
            HuellaCarbonoParametro::create([
                'categoria' => 'electricidad',
                'tipo' => $tipo,
                'descripcion' => $descripcion,
                'factor_conversion' => $factores[$tipo] ?? 0,
                'unidad_medida' => 'kWh',
                'unidad_resultado' => 'kgCO2e',
                'activo' => true,
            ]);
        }

        // Residuos
        foreach ($tiposFuente['residuos'] as $tipo => $descripcion) {
            HuellaCarbonoParametro::create([
                'categoria' => 'residuos',
                'tipo' => $tipo,
                'descripcion' => $descripcion,
                'factor_conversion' => $factores[$tipo] ?? 0,
                'unidad_medida' => 'kg',
                'unidad_resultado' => 'kgCO2e',
                'activo' => true,
            ]);
        }
    }
}
