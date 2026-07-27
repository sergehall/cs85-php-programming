<?php

declare(strict_types=1);

namespace App\Services\Modules\Module11A\Domain;

final readonly class ApiContact
{
    public function __construct(
        public int $id,
        public string $name,
        public string $username,
        public string $email,
        public string $phone,
        public string $website,
        public string $company,
        public string $city,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): ?self
    {
        $id = filter_var($payload['id'] ?? null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);
        $name = self::requiredString($payload['name'] ?? null);
        $username = self::requiredString($payload['username'] ?? null);
        $email = filter_var($payload['email'] ?? null, FILTER_VALIDATE_EMAIL);
        $phone = self::requiredString($payload['phone'] ?? null);
        $website = self::requiredString($payload['website'] ?? null);
        $company = is_array($payload['company'] ?? null)
            ? self::requiredString($payload['company']['name'] ?? null)
            : null;
        $city = is_array($payload['address'] ?? null)
            ? self::requiredString($payload['address']['city'] ?? null)
            : null;

        if ($id === false || $name === null || $username === null || $email === false || $phone === null || $website === null || $company === null || $city === null) {
            return null;
        }

        return new self(
            id: $id,
            name: $name,
            username: $username,
            email: strtolower($email),
            phone: $phone,
            website: strtolower($website),
            company: $company,
            city: $city,
        );
    }

    /**
     * @return array{id: int, name: string, username: string, email: string, phone: string, website: string, company: string, city: string}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'company' => $this->company,
            'city' => $this->city,
        ];
    }

    private static function requiredString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
