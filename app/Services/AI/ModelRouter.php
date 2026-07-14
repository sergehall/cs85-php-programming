<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Services\AI\Enums\AiMode;
use InvalidArgumentException;

final class ModelRouter
{
    /**
     * @return array{label:string,description:string,model:string,model_name:string,model_profile:string,recommended_for:string,temperature:float,prompt:string}
     */
    public function configuration(AiMode $mode): array
    {
        $configuration = config("ai.modes.{$mode->value}");

        if (! is_array($configuration)) {
            throw new InvalidArgumentException("AI mode [{$mode->value}] is not configured.");
        }

        return $configuration;
    }

    public function model(AiMode $mode): string
    {
        return $this->configuration($mode)['model'];
    }
}
