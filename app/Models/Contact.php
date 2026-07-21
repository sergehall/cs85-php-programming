<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'contact_group_id',
    'name',
    'first_name',
    'last_name',
    'email',
    'phone',
    'company',
    'role',
    'is_active',
    'notes',
])]
class Contact extends Model
{
    public const ROLE_USER = 'user';

    public const ROLE_ADMIN = 'admin';

    protected static function booted(): void
    {
        static::saving(function (Contact $contact): void {
            if ($contact->isDirty('name')) {
                [$firstName, $lastName] = self::splitName((string) $contact->name);
                $contact->first_name = $firstName;
                $contact->last_name = $lastName;

                return;
            }

            if ($contact->isDirty('first_name') || $contact->isDirty('last_name')) {
                $contact->name = $contact->fullName();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<ContactGroup, $this>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(ContactGroup::class, 'contact_group_id');
    }

    public function fullName(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    /**
     * @return array{string, string}
     */
    private static function splitName(string $name): array
    {
        $parts = preg_split('/\s+/u', trim($name), 2);

        return [
            $parts[0] ?? '',
            $parts[1] ?? '',
        ];
    }
}
