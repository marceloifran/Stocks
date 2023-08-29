<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class personal extends Model
{
    protected $fillable = [
        'nombre',
        'rol'
        // Agrega aquí otros campos
    ];

    // Define la relación con los movimientos de stock


    public function sueldo()
    {
        return $this->hasMany(sueldo::class, 'personal_id');
    }



    public function stockMovement()
    {
        return $this->hasMany(StockMovement::class, 'personal_id');
    }

    public function equipos()
    {
        return $this->hasMany(equipos::class, 'personal_id');
    }


}

