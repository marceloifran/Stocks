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
        'cargo',
        'edad',
    ];

    public function stockMovement()
    {
        return $this->hasMany(StockMovement::class, 'personal_id');
    }
   
}
