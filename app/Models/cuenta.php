<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class cuenta extends Model
{
    protected $table = 'cuentas';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'saldo',
        'tipo',
        'activo',
        // Otros campos de la cuenta financiera
    ];

    // Relaciones
    public function transacciones()
    {
        return $this->hasMany(transacciones::class);
    }
}
