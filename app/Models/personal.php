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
        'obra_id',
    ];

    public function stockMovement()
    {
        return $this->hasMany(StockMovement::class, 'personal_id');
    }

    public function obra()
    {
        return $this->belongsTo(obra::class);
    }

    public function getCantidadMovimientosAttribute()
    {
        return $this->stockMovement()->count();
    }
}
