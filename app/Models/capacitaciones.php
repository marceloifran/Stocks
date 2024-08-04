<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class capacitaciones extends Model
{
    use HasFactory;
    protected $table = 'capacitacions';


    protected $fillable = [
        'fecha',
        'tematica',
        'capacitador',
        'modalidad',
        'observaciones',
    ];

    public function personal()
    {
        return $this->belongsToMany(personal::class, 'capacitacion_personal', 'capacitacion_id', 'personal_id');
    }

   


}

