<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Blameable;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'username', 'password', 'is_active'])]
#[Hidden(['password', 'remember_token', 'is_active'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use Blameable, HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    protected function getPanelRoles(): array
    {
        return [
            'super-admin' => ['super-admin'],
            'auditor' => ['auditor'],
            'fakultas' => ['ketua-lpm', 'admin-mutu'],
            'prodi' => ['kaprodi', 'sekprodi', 'kepala-unit', 'dosen', 'tendik'],
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $roles = $this->getPanelRoles()[$panel->getId()] ?? [];

        return $this->hasRole($roles);
    }

    public function userPositions()
    {
        return $this->hasMany(UserPosition::class, 'user_id');
    }

    public function positions()
    {
        return $this->belongsToMany(Position::class, 'user_positions')
            ->withPivot(['organization_unit_id', 'start_date', 'end_date', 'is_active'])
            ->withTimestamps();
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->latest();
    }

    public function unreadNotifications(): HasMany
    {
        return $this->notifications()->where('is_read', false);
    }

    public function readNotifications(): HasMany
    {
        return $this->notifications()->where('is_read', true);
    }
}
