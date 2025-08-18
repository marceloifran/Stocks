<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HuellaCarbonoDetalle extends Model
{
    use HasFactory;

    protected $table = 'huella_carbono_detalles';

    protected $fillable = [
        'huella_carbono_id',
        'tipo_fuente', // combustible, electricidad, residuos
        'cantidad',
        'unidad', // litros, kWh, kg
        'emisiones_co2',
        'factor_conversion',
        'detalles', // JSON con información adicional
    ];

    protected $casts = [
        'cantidad' => 'float',
        'emisiones_co2' => 'float',
        'factor_conversion' => 'float',
        'detalles' => 'array',
    ];

    public function huellaCarbono(): BelongsTo
    {
        return $this->belongsTo(HuellaCarbono::class);
    }

    public function calcularEmisiones()
    {
        // Obtener factores de conversión desde la configuración
        $factores = config('huella_carbono.factores_conversion');

        if (isset($factores[$this->tipo_fuente])) {
            $this->factor_conversion = $factores[$this->tipo_fuente];
            $this->emisiones_co2 = $this->cantidad * $this->factor_conversion;
            $this->save();

            // Actualizar el total en la huella de carbono
            $this->huellaCarbono->calcularTotal();
        }
    }
}
