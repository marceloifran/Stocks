<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class vacaciones extends Model
{
    use HasFactory;

    protected $fillable = ['id','personal_id','fecha_inicio','fecha_fin','comentario','estado'];
    public $timestamps = false;
    public function personal()
    {
        return $this->belongsTo(personal::class,'personal_id','id');
    }
}
