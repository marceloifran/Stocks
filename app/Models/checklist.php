<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class checklist extends Model
{
    use HasFactory;
    protected $fillable = [
        'fecha',
        'opciones',
        'autorizacion',
        // 'personal_id',
    ];

    protected $casts = [
        'opciones' => 'array',
    ];

    public function personal()
    {
        return $this->belongsToMany(personal::class, 'checklist_personal');
    }



}
