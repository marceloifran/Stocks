<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class entidad extends Model
{

    use HasFactory;


    protected $fillable = [
        'razon_social',
        'cuit',
        'direccion',
        'localidad',
        'cp',
        'pcia',
        'logo',
        'created_at',
        'updated_at',
    ];
}
