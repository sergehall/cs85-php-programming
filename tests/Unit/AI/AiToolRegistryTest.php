<?php

namespace Tests\Unit\AI;

use App\Services\AI\AiToolRegistry;
use App\Services\AI\DTOs\AiToolCall;
use App\Services\AI\Exceptions\AiProviderException;
use Tests\TestCase;

class AiToolRegistryTest extends TestCase
{
    public function test_tools_expose_only_allowlisted_read_only_course_context(): void
    {
        $registry = app(AiToolRegistry::class);
        $names = collect($registry->definitions())->pluck('function.name')->all();

        $this->assertSame(['list_course_modules', 'get_course_module', 'get_project_stack'], $names);
        $this->assertNotContains('execute_shell', $names);
        $this->assertNotContains('read_file', $names);
        $this->assertNotContains('run_sql', $names);
    }

    public function test_course_module_tool_returns_minimal_configured_context(): void
    {
        $result = app(AiToolRegistry::class)->execute(new AiToolCall(
            id: 'tool-1',
            name: 'get_course_module',
            arguments: '{"module":"module-8"}',
        ));

        $this->assertTrue($result['found']);
        $this->assertSame('Module 8', $result['module']['module']);
        $this->assertArrayNotHasKey('contact', $result);
    }

    public function test_unknown_tools_and_invalid_arguments_are_rejected(): void
    {
        $registry = app(AiToolRegistry::class);

        try {
            $registry->execute(new AiToolCall('tool-1', 'read_file', '{"path":".env"}'));
            $this->fail('Unknown tools must be rejected.');
        } catch (AiProviderException $exception) {
            $this->assertSame('tool_not_allowed', $exception->errorCode);
        }

        $this->expectException(AiProviderException::class);
        $registry->execute(new AiToolCall('tool-2', 'get_course_module', '{invalid-json}'));
    }
}
