<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable([
    'name',
    'email',
    'email_verified_at',
    'password',
    'password_login_enabled',
    'role',
    'login_enabled',
    'github_id',
    'github_username',
    'github_avatar_url',
    'profile_photo_url',
    'mfa_secret',
    'mfa_recovery_codes',
    'mfa_confirmed_at',
    'mfa_last_used_time_slice',
    'first_name',
    'last_name',
    'github_profile_url',
    'linkedin_profile_url',
    'bio',
    'technical_skills',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected static function booted(): void
    {
        static::creating(function (User $user): void {
            $publicUuid = $user->getAttribute('public_uuid');

            if (! is_string($publicUuid) || ! Str::isUuid($publicUuid)) {
                $user->setAttribute('public_uuid', (string) Str::uuid());
            }

            if ($user->getAttribute('login_enabled') === null) {
                $user->setAttribute('login_enabled', true);
            }
        });

        static::saving(function (User $user): void {
            $email = $user->getAttribute('email');

            if (is_string($email)) {
                $user->setAttribute('email', self::normalizeEmail($email));
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
            'login_enabled' => 'boolean',
            'password_login_enabled' => 'boolean',
            'mfa_confirmed_at' => 'datetime',
            'mfa_last_used_time_slice' => 'integer',
            'mfa_recovery_codes' => 'encrypted:array',
            'mfa_secret' => 'encrypted',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function getRouteKeyName(): string
    {
        return 'public_uuid';
    }

    public function hasMfaEnabled(): bool
    {
        return is_string($this->mfa_secret) && $this->mfa_confirmed_at !== null;
    }

    public function canLogIn(): bool
    {
        return (bool) $this->getAttribute('login_enabled');
    }

    public static function normalizeEmail(string $email): string
    {
        return Str::lower(trim($email));
    }

    public function profilePhotoUrl(): ?string
    {
        foreach (['profile_photo_url', 'github_avatar_url'] as $attribute) {
            $value = $this->getAttribute($attribute);

            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    /**
     * @return HasOne<AdminAccessRequest, $this>
     */
    public function adminAccessRequest(): HasOne
    {
        return $this->hasOne(AdminAccessRequest::class);
    }

    /**
     * @return HasMany<AiConversation, $this>
     */
    public function aiConversations(): HasMany
    {
        return $this->hasMany(AiConversation::class);
    }
}
