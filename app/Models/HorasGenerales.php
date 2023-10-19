<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorasGenerales extends Model
{
    protected $fillable = ['fecha', 'hora','codigo','presente','tipo'];

    protected $cast = [
        'presente' => 'boolean',
    ];


public function personal()
{
    return $this->belongsTo(Personal::class, 'codigo', 'nro_identificacion');
}


}
