<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'domain',
        'description',
        'settings',
        'is_active',
    ];

    /**
     * El método "boot" del modelo.
     * Se ejecuta automáticamente cuando la aplicación arranca.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Cuando se crea un nuevo tenant, generar sus parámetros de huella de carbono
        static::created(function ($tenant) {
            static::createHuellaCarbonoParametros($tenant->id);
        });
    }

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the users for the tenant.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Crea parámetros de huella de carbono para un tenant específico
     * Este método es llamado automáticamente al crear un nuevo tenant
     *
     * @param int $tenantId ID del tenant
     * @return void
     */
    protected static function createHuellaCarbonoParametros(int $tenantId): void
    {
        // Obtener los factores de conversión y tipos de fuente de la configuración
        $factores = config('huella_carbono.factores_conversion');
        $tiposFuente = config('huella_carbono.tipos_fuente');

        // Combustibles
        foreach ($tiposFuente['combustible'] as $tipo => $descripcion) {
            \App\Models\HuellaCarbonoParametro::firstOrCreate(
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
            \App\Models\HuellaCarbonoParametro::firstOrCreate(
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
            \App\Models\HuellaCarbonoParametro::firstOrCreate(
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
