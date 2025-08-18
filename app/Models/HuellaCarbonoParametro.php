<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HuellaCarbonoParametro extends Model
{
    use HasFactory;

    protected $table = 'huella_carbono_parametros';

    protected $fillable = [
        'categoria',
        'tipo',
        'descripcion',
        'factor_conversion',
        'unidad_medida',
        'unidad_resultado',
        'activo',
    ];

    protected $casts = [
        'factor_conversion' => 'float',
        'activo' => 'boolean',
    ];
}
