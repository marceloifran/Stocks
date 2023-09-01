<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sueldo extends Model
{
    protected $fillable = ['precio_hora','total_hrs_comunes','precio_hrs_extas','total_hrs_extras','fecha','personal_id','tipo','total_hrs_otras'];

    public function personal()
    {
        return $this->belongsTo(\App\Models\personal::class, 'personal_id');

    }

}
