<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\PurchaseOrder;

class StockMovement extends Model
{

    protected $fillable = ['observaciones', 'fecha_movimiento', 'stock_id', 'cantidad_movimiento', 'personal_id', 'marca', 'certificacion', 'tipo', 'firma'];

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

            // Verificar si el stock quedó en nivel crítico y si necesita reposición
            if ($stock->cantidad <= 10) {
                // Verificar si ya existe una orden de compra pendiente o pedida
                $pendingOrders = PurchaseOrder::where('stock_id', $stock->id)
                    ->whereIn('status', ['pendiente', 'pedido'])
                    ->count();

                if ($pendingOrders === 0) {
                    PurchaseOrder::create([
                        'stock_id' => $stock->id,
                        'quantity' => 20, // Cantidad por defecto para reposición
                        'status' => 'pendiente',
                        'requested_date' => Carbon::now(),
                        'notes' => 'Orden generada automáticamente por nivel bajo de stock',
                    ]);
                }
            }
        });
    }
}
