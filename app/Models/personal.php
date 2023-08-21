<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class personal extends Model
{
    protected $fillable = [
        'nombre',
        // Agrega aquí otros campos
    ];

    // Define la relación con los movimientos de stock

    public function stockMovement()
    {
        return $this->hasMany(StockMovement::class, 'personal_id');
    }


}

