<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HuellaCarbonoParametro extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'huella_carbono_parametros';

    protected $fillable = [
        'categoria',
        'tipo',
        'descripcion',
        'factor_conversion',
        'unidad_medida',
        'unidad_resultado',
        'activo',
        'tenant_id',
    ];

    protected $casts = [
        'factor_conversion' => 'float',
        'activo' => 'boolean',
    ];

    /**
     * Get the tenant that owns this record.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
