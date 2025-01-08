<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class obra extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'estado',
        'presupuesto',
        'descripcion',
    ];

    public function personal()
    {
        return $this->hasMany(personal::class);
    }
}
