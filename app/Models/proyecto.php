<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class proyecto extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'fecha_inicio', 'fecha_fin'];

    public function tareas()
    {
        return $this->hasMany(tarea::class);
    }
}
