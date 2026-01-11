<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_premium',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'is_premium' => 'boolean',
        ];
    }

    public function memorySetting(): HasOne
    {
        return $this->hasOne(UserMemorySetting::class);
    }

    public function memories(): HasMany
    {
        return $this->hasMany(UserMemory::class);
    }

    /**
     * Check if user has memory feature enabled
     */
    public function hasMemoryEnabled(): bool
    {
        if (!$this->is_premium) {
            return false;
        }

        return $this->memorySetting?->memory_enabled ?? false;
    }

    /**
     * Get user's default save level preference
     */
    public function getDefaultSaveLevel(): string
    {
        return $this->memorySetting?->default_save_level ?? 'summary';
    }
}
