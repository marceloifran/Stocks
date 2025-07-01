<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comida extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'hora',
        'codigo',
        'tipo_comida',
        'presente'
    ];

    public function personal()
    {
        return $this->belongsTo(personal::class, 'codigo', 'nro_identificacion');
    }
}
