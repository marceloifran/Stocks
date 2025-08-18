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
}
