<?php

declare(strict_types=1);

namespace App\Services\AI\Enums;

enum AiMode: string
{
    case General = 'general';
    case Coding = 'coding';
    case Architecture = 'architecture';
}
