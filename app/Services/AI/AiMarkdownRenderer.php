<?php

declare(strict_types=1);

namespace App\Services\AI;

use Illuminate\Support\Str;

final class AiMarkdownRenderer
{
    public function render(string $markdown): string
    {
        return Str::markdown($markdown, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }
}
