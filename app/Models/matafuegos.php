<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class matafuegos extends Model
{
    use HasFactory;
    protected $fillable = [
      'fecha_vencimiento',
        'ubicacion',
        'capacidad',
        'tipo',
    ];
}
