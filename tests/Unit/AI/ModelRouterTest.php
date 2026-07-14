<?php

namespace Tests\Unit\AI;

use App\Services\AI\Enums\AiMode;
use App\Services\AI\ModelRouter;
use Tests\TestCase;

class ModelRouterTest extends TestCase
{
    public function test_each_mode_routes_to_the_model_declared_in_ai_configuration(): void
    {
        $router = app(ModelRouter::class);

        $this->assertSame('qwen/qwen3.6-35b-a3b', $router->model(AiMode::General));
        $this->assertSame('qwen/qwen3-coder-next', $router->model(AiMode::Coding));
        $this->assertSame('openai/gpt-oss-120b', $router->model(AiMode::Architecture));

        foreach (AiMode::cases() as $mode) {
            $configuration = $router->configuration($mode);

            $this->assertFileExists($configuration['prompt']);
            $this->assertNotEmpty($configuration['model_name']);
            $this->assertNotEmpty($configuration['model_profile']);
            $this->assertNotEmpty($configuration['recommended_for']);
            $this->assertGreaterThanOrEqual(0.0, $configuration['temperature']);
        }
    }
}
