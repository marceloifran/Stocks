<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = ['stock_id', 'cantidad_movimiento','persona'];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
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
