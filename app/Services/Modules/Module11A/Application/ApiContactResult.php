<?php

declare(strict_types=1);

namespace App\Services\Modules\Module11A\Application;

use App\Services\Modules\Module11A\Domain\ApiContact;

final readonly class ApiContactResult
{
    /**
     * @param  list<ApiContact>  $contacts
     */
    public function __construct(
        public array $contacts,
        public string $source,
        public string $fetchedAt,
        public bool $degraded,
        public string $message,
    ) {}
}
