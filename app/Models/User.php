<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'telepon',
        'status_aktif',
        'pengurus_id',
        'entitas_id',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'status_aktif' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function pengurus(): BelongsTo
    {
        return $this->belongsTo(Pengurus::class, 'pengurus_id');
    }

    /** Bila diisi, user hanya bisa melihat/mengelola data entitas ini. */
    public function entitas(): BelongsTo
    {
        return $this->belongsTo(Entitas::class, 'entitas_id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->status_aktif;
    }
}
