<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tarea extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'proyecto_id', 'fecha_limite', 'estado'];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id', 'id');
    }
}
