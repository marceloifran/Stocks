<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class asistencia extends Model
{
    protected $fillable = ['fecha', 'hora', 'estado','codigo'];



public function personal()
{
    return $this->belongsTo(Personal::class, 'codigo', 'nro_identificacion');
}



}
