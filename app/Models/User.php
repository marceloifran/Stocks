<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Panel;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;

use Spatie\EloquentSortable\SortableTrait;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends  Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SortableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];




    /**
     * Get the tenant that owns the user.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Permitir acceso a superadmins
        if ($this->hasRole('superadmin')) {
            return true;
        }

        // Permitir acceso a usuarios de cualquier tenant siempre que tengan email verificado
        if ($this->tenant_id && $this->hasVerifiedEmail()) {
            return true;
        }

        // BMI siempre tiene acceso (mantener compatibilidad con código anterior)
        if (str_ends_with($this->email, '@bmi.com.ar') && $this->hasVerifiedEmail()) {
            return true;
        }

        return false;
    }

    public function hasVerifiedEmail()
    {
        return ! is_null($this->email_verified_at);
    }
}
