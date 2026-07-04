<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable([
    'name',
    'email',
    'email_verified_at',
    'password',
    'role',
    'github_id',
    'github_username',
    'github_avatar_url',
    'mfa_secret',
    'mfa_recovery_codes',
    'mfa_confirmed_at',
    'first_name',
    'last_name',
    'github_profile_url',
    'linkedin_profile_url',
    'bio',
    'technical_skills',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected static function booted(): void
    {
        static::creating(function (User $user): void {
            if (! $user->getAttribute('public_uuid')) {
                $user->setAttribute('public_uuid', (string) Str::uuid());
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'mfa_confirmed_at' => 'datetime',
            'mfa_recovery_codes' => 'encrypted:array',
            'mfa_secret' => 'encrypted',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function hasMfaEnabled(): bool
    {
        return is_string($this->mfa_secret) && $this->mfa_confirmed_at !== null;
    }

    /**
     * @return HasOne<AdminAccessRequest, $this>
     */
    public function adminAccessRequest(): HasOne
    {
        return $this->hasOne(AdminAccessRequest::class);
    }
}
