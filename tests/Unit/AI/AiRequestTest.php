<?php

namespace Tests\Unit\AI;

use App\Models\AiRequest;
use Tests\TestCase;

class AiRequestTest extends TestCase
{
    public function test_failed_and_stale_processing_requests_are_retryable(): void
    {
        $failed = (new AiRequest)->forceFill([
            'provider' => 'lm_studio',
            'status' => AiRequest::STATUS_FAILED,
            'created_at' => now(),
        ]);
        $active = (new AiRequest)->forceFill([
            'provider' => 'lm_studio',
            'status' => AiRequest::STATUS_PROCESSING,
            'created_at' => now(),
        ]);
        $interrupted = (new AiRequest)->forceFill([
            'provider' => 'lm_studio',
            'status' => AiRequest::STATUS_PROCESSING,
            'created_at' => now()->subMinutes(10),
        ]);
        $completed = (new AiRequest)->forceFill([
            'provider' => 'lm_studio',
            'status' => AiRequest::STATUS_COMPLETED,
            'created_at' => now(),
        ]);

        $this->assertTrue($failed->isRetryable());
        $this->assertFalse($active->isRetryable());
        $this->assertTrue($interrupted->isRetryable());
        $this->assertFalse($completed->isRetryable());
    }
}
