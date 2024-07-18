<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use EightyNine\Approvals\Models\ApprovableModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ingresos extends Model
{
    protected $fillable = [
        'nombre',
        'fecha',
        'dni',
        'firma',
    ];
    
    
}
