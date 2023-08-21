<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = ['stock_id', 'cantidad_movimiento','personal_id'];

    public function stock()
    {
        return $this->belongsTo(\App\Models\stock::class);

    }

    public function personal()
    {
        return $this->belongsTo(\App\Models\personal::class, 'personal_id');

    }

    protected static function booted()
    {
        static::created(function ($stockMovement) {
            $stock = $stockMovement->stock;

            // Resta la cantidad de movimiento del stock correspondiente
            $stock->cantidad -= $stockMovement->cantidad_movimiento;
            $stock->save();
        });
    }
}
