<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class permiso extends Model
{
    protected $fillable = [
        'tipo',
        'descripcion',
        'fecha',
    ];


    public function personal()
    {
        return $this->belongsToMany(personal::class, 'permiso_personal');
    }



}
