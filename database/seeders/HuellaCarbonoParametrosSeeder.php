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

        // Obtener todos los tenants para crear parámetros para cada uno
        $tenants = \App\Models\Tenant::all();

        // Si no hay tenants, crear parámetros sin tenant_id
        if ($tenants->isEmpty()) {
            $this->createParametersForTenant(null, $tiposFuente, $factores);
            return;
        }

        // Crear parámetros para cada tenant
        foreach ($tenants as $tenant) {
            $this->createParametersForTenant($tenant->id, $tiposFuente, $factores);
        }
    }

    /**
     * Crea parámetros de huella de carbono para un tenant específico
     *
     * @param int|null $tenantId ID del tenant o null si no se requiere
     * @param array $tiposFuente Tipos de fuente
     * @param array $factores Factores de conversión
     */
    private function createParametersForTenant(?int $tenantId, array $tiposFuente, array $factores): void
    {
        // Combustibles
        foreach ($tiposFuente['combustible'] as $tipo => $descripcion) {
            HuellaCarbonoParametro::firstOrCreate(
                [
                    'categoria' => 'combustible',
                    'tipo' => $tipo,
                    'tenant_id' => $tenantId,
                ],
                [
                    'descripcion' => $descripcion,
                    'factor_conversion' => $factores[$tipo] ?? 0,
                    'unidad_medida' => 'litros',
                    'unidad_resultado' => 'kgCO2e',
                    'activo' => true,
                ]
            );
        }

        // Electricidad
        foreach ($tiposFuente['electricidad'] as $tipo => $descripcion) {
            HuellaCarbonoParametro::firstOrCreate(
                [
                    'categoria' => 'electricidad',
                    'tipo' => $tipo,
                    'tenant_id' => $tenantId,
                ],
                [
                    'descripcion' => $descripcion,
                    'factor_conversion' => $factores[$tipo] ?? 0,
                    'unidad_medida' => 'kWh',
                    'unidad_resultado' => 'kgCO2e',
                    'activo' => true,
                ]
            );
        }

        // Residuos
        foreach ($tiposFuente['residuos'] as $tipo => $descripcion) {
            HuellaCarbonoParametro::firstOrCreate(
                [
                    'categoria' => 'residuos',
                    'tipo' => $tipo,
                    'tenant_id' => $tenantId,
                ],
                [
                    'descripcion' => $descripcion,
                    'factor_conversion' => $factores[$tipo] ?? 0,
                    'unidad_medida' => 'kg',
                    'unidad_resultado' => 'kgCO2e',
                    'activo' => true,
                ]
            );
        }
    }
}
