<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class checklists extends Model
{
    use HasFactory;
    protected $table = 'checklists'; // Nombre de la tabla

    protected $fillable = [
        'fecha',
        'opciones',
        'autorizacion',
        'peso_carga_bruta',
        'capacidad_bruta',
        // 'personal_id',
    ];

    //
    // 

    protected $casts = [
        'opciones' => 'array',
    ];

  
    public function setPesoCargaBrutaAttribute($value)
    {
        $this->attributes['peso_carga_bruta'] = $value;
        $this->calculateCriticidad();
    }

    public function setCapacidadBrutaAttribute($value)
    {
        $this->attributes['capacidad_bruta'] = $value;
        $this->calculateCriticidad();
    }

    protected function calculateCriticidad()
    {
        if (isset($this->attributes['peso_carga_bruta']) && isset($this->attributes['capacidad_bruta'])) {
            $this->attributes['criticidad'] = ($this->attributes['peso_carga_bruta'] / $this->attributes['capacidad_bruta']) * 100;
        }
    }


    public function personal()
    {
        return $this->belongsToMany(Personal::class, 'checklist_personal', 'checklist_id', 'personal_id');
    }



}
