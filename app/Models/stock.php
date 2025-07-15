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
        'precio',
    ];

    public function getValorTotalAttribute()
    {
        return $this->cantidad * $this->precio;
    }

    // Define la relación con el elemento de stockhistory
    public function stockhistory()
    {
        return $this->hasMany(StockHistory::class, 'stock_id');
    }

    public function stockMovement()
    {
        return $this->hasMany(StockMovement::class, 'stock_id');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'stock_id');
    }

    public function getIsLowStockAttribute()
    {
        if ($this->cantidad <= 10) {
            return 'Stock Bajo';
        }
        else if ($this->cantidad <= 20) {
            return 'Stock Medio';
        }

        else {
            return 'Stock Alto';
        }
    }

    public function needsRestock(): bool
    {
        // Verificar si el stock es bajo y no hay órdenes de compra pendientes
        if ($this->cantidad <= 10) {
            $pendingOrders = $this->purchaseOrders()
                ->whereIn('status', ['pendiente', 'pedido'])
                ->count();
                
            return $pendingOrders === 0;
        }
        
        return false;
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
                
                // Si el stock baja a un nivel crítico, crear una orden de compra automáticamente
                if ($newCantidad <= 10 && $stock->needsRestock()) {
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
