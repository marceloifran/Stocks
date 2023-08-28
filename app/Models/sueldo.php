<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sueldo extends Model
{
    protected $fillable = ['monto','fecha','personal_id','tipo'];

    public function personal()
    {
        return $this->belongsTo(\App\Models\personal::class, 'personal_id');

    }
}
