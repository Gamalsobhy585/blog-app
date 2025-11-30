<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use App\Traits\HasUuid;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasUuid;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'role',
        'uuid',
        'profile_photo_path',

    ];
    public function getRoleNameAttribute(): string
    {
        return match ($this->role) {
            2 => 'librarian',
            3 => 'user',
            default => 'unknown',
        };
    }
    public function getActiveStringAttribute(): string
    {
          return $this->is_active ? 'yes' : 'no';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
            'profile_photo_url',
             'role_name',
            'active_string',

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
            'is_active' => 'boolean',

        ];
    }
    public function scopeActive(Builder $query): Builder
        {
            return $query->where('is_active', true);
        }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

        // In your User model or wherever you need the URL
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return Storage::disk('s3')->url($this->profile_photo_path);
        }
        return null;
    }
    
}
