<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class equipos extends Model
{
    protected $fillable = ['nombre','patente','tipo','personal_id','estado','fecha_ultimo_mantenimiento','seguro','rto','poliza'];

    public function personal()
    {
        return $this->belongsTo(\App\Models\personal::class, 'personal_id');

    }
}
