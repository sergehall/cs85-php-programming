<?php

declare(strict_types=1);

namespace App\Services\AI\Exceptions;

use RuntimeException;

final class AiProviderException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly string $errorCode = 'provider_error',
    ) {
        parent::__construct($message);
    }
}
