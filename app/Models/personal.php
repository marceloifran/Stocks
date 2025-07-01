<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class personal extends Model
{
    protected $fillable = [
        'nombre',
        'dni',
        'firma',
        'nro_identificacion',
        'departamento'
    ];

    public function presente()
    {
        $asistencia = Asistencia::where('codigo', $this->nro_identificacion)->exists();
        $presente = $asistencia ? true : false;

        return $presente;
    }


    public function asistencia()
    {
        return $this->hasMany(asistencia::class, 'codigo', 'nro_identificacion');
    }

    public function stockMovement()
    {
        return $this->hasMany(StockMovement::class, 'personal_id');
    }

    public function comidas()
    {
        return $this->hasMany(Comida::class, 'codigo', 'nro_identificacion');
    }
}
