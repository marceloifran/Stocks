<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'codigo_barras',
        'cantidad_stock',
        'cantidad_minima',
        'precio_compra',
        'precio_venta',
        'proveedor',
        'ubicacion_almacen',
        'fecha_vencimiento',
    ];

}
