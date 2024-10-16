<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class permiso extends Model
{
    use HasFactory;

    protected $table = 'permisos';

    protected $fillable = [
        'contratista',
        'fecha_inicio',
        'fecha_fin',
        'tipo_trabajo',
        'capacitados',
        'trabajadores',
        'trabajos_a_realizar',
        'equipos_a_intervenir',
        'elementos',
        'fecha_a_c',
        'cierre',
        'fecha_fin_pte',
    ];

    protected $casts = [
        'tipo_trabajo' => 'array',
        'trabajadores' => 'array',
        'elementos' => 'array',
        'cierre' => 'array',
    ];
}
