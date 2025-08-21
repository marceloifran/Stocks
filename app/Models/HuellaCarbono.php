<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HuellaCarbono extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'huella_carbono';

    protected $fillable = [
        'fecha',
        'total_emisiones',
        'notas',
        'tenant_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'total_emisiones' => 'float',
    ];

    public function detalles(): HasMany
    {
        return $this->hasMany(HuellaCarbonoDetalle::class);
    }

    /**
     * Get the tenant that owns this record.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function calcularTotal()
    {
        $this->total_emisiones = $this->detalles->sum('emisiones_co2');
        $this->save();
    }
}
