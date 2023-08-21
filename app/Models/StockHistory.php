<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    protected $table = 'stock_histories';

    protected $fillable = [
        'stock_id',
        'user_id',
        'nombre_campo',
        'valor_anterior',
        'valor_nuevo',
        'fecha_nueva',

    ];

    // Define la relación con el elemento de stock
    public function stock()
    {
        return $this->belongsTo(\App\Models\stock::class, 'stock_id');
    }

    // Define la relación con el usuario si es necesario
     public function user()
     {
    return $this->belongsTo(User::class, 'user_id');
     }
}
