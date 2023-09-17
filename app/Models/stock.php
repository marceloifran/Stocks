<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'fecha',
        'cantidad',
        'descripcion',
        'unidad_medida',
        'tipo_stock',
    ];

    // Define la relación con el elemento de stockhistory
    public function stockhistory()
    {
        return $this->hasMany(StockHistory::class, 'stock_id');
    }

    public function stockMovement()
    {
        return $this->hasMany(StockMovement::class, 'stock_id');
    }

    public function getIsLowStockAttribute()
    {
        if ($this->cantidad <= 10) {
            return 'Stock Bajo';
        } else {
            return 'Stock Alto';
        }
    }


    // Stock model
protected static function boot()
{
    parent::boot();

    static::updating(function ($stock) {
        $oldCantidad = $stock->getOriginal('cantidad');
        $newCantidad = $stock->cantidad;

        if ($oldCantidad !== $newCantidad) {
            StockHistory::create([
                'stock_id' => $stock->id,
                'user_id' => auth()->id(),
                'nombre_campo' => 'cantidad',
                'valor_anterior' => $oldCantidad,
                'valor_nuevo' => $newCantidad,
                'fecha_nueva' => Carbon::now()->format('Y-m-d'),
            ]);
        }
    });
}



}
