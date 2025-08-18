<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HuellaCarbono extends Model
{
    use HasFactory;

    protected $table = 'huella_carbono';

    protected $fillable = [
        'fecha',
        'total_emisiones',
        'notas',
    ];

    protected $casts = [
        'fecha' => 'date',
        'total_emisiones' => 'float',
    ];

    public function detalles(): HasMany
    {
        return $this->hasMany(HuellaCarbonoDetalle::class);
    }

    public function calcularTotal()
    {
        $this->total_emisiones = $this->detalles->sum('emisiones_co2');
        $this->save();
    }
}
