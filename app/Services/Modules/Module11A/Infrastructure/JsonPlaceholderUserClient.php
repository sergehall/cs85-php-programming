<?php

declare(strict_types=1);

namespace App\Services\Modules\Module11A\Infrastructure;

use App\Services\Modules\Module11A\Application\ApiContactResult;
use App\Services\Modules\Module11A\Domain\ApiContact;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use JsonException;
use RuntimeException;
use Throwable;

final class JsonPlaceholderUserClient
{
    private const string CACHE_KEY = 'module11a.json-placeholder.users.v1';

    public function fetch(bool $refresh = false): ApiContactResult
    {
        if ($refresh) {
            Cache::forget(self::CACHE_KEY);
        }

        $cached = Cache::get(self::CACHE_KEY);

        if (is_array($cached)) {
            $contacts = $this->hydrate($cached['payload'] ?? null);

            if ($contacts !== []) {
                return new ApiContactResult(
                    contacts: $contacts,
                    source: 'cache',
                    fetchedAt: (string) ($cached['fetched_at'] ?? now()->toIso8601String()),
                    degraded: false,
                    message: 'A validated live response was loaded from the Laravel cache.',
                );
            }
        }

        try {
            $payload = Http::acceptJson()
                ->timeout(max(1, (int) config('services.module11a.timeout_seconds', 3)))
                ->get((string) config('services.module11a.endpoint'))
                ->throw()
                ->json();
            $contacts = $this->hydrate($payload);

            if ($contacts === []) {
                throw new RuntimeException('The API response did not contain valid user records.');
            }

            $fetchedAt = now()->toIso8601String();

            Cache::put(self::CACHE_KEY, [
                'payload' => $payload,
                'fetched_at' => $fetchedAt,
            ], max(1, (int) config('services.module11a.cache_seconds', 600)));

            return new ApiContactResult(
                contacts: $contacts,
                source: 'live',
                fetchedAt: $fetchedAt,
                degraded: false,
                message: 'Fresh JSON was fetched and validated on the Laravel server.',
            );
        } catch (Throwable) {
            return new ApiContactResult(
                contacts: $this->fallbackContacts(),
                source: 'fallback',
                fetchedAt: now()->toIso8601String(),
                degraded: true,
                message: 'The remote API was unavailable, so the versioned fallback keeps the assignment usable.',
            );
        }
    }

    /**
     * @return list<ApiContact>
     */
    private function fallbackContacts(): array
    {
        $path = base_path('assignments/module11a/data/users.json');
        $json = file_get_contents($path);

        if ($json === false) {
            throw new RuntimeException('The Module 11 fallback dataset is missing.');
        }

        try {
            return $this->hydrate(json_decode($json, true, flags: JSON_THROW_ON_ERROR));
        } catch (JsonException $exception) {
            throw new RuntimeException('The Module 11 fallback dataset is invalid.', previous: $exception);
        }
    }

    /**
     * @return list<ApiContact>
     */
    private function hydrate(mixed $payload): array
    {
        if (! is_array($payload)) {
            return [];
        }

        $contacts = [];

        foreach ($payload as $record) {
            if (! is_array($record)) {
                continue;
            }

            $contact = ApiContact::fromPayload($record);

            if ($contact !== null) {
                $contacts[] = $contact;
            }
        }

        return $contacts;
    }
}
