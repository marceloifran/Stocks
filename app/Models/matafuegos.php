<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class matafuegos extends Model
{
    use HasFactory;
    protected $fillable = [
      'fecha_vencimiento',
        'ubicacion',
        'capacidad',
    ];

   
}
