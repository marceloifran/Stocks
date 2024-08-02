<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class capacitaciones extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'duracion',
        'instructor',
        'lugar',
        'cupo_maximo',
        'estado',
    ];

    public function personal(): BelongsToMany
    {
        return $this->belongsToMany(Personal::class)->withPivot('aprobado', 'certificado_id');
    }


}

