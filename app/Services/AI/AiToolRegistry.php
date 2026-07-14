<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Services\AI\DTOs\AiToolCall;
use App\Services\AI\Exceptions\AiProviderException;
use JsonException;

final class AiToolRegistry
{
    /**
     * @return list<array<string, mixed>>
     */
    public function definitions(): array
    {
        if (! config('ai.tools.enabled')) {
            return [];
        }

        return [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'list_course_modules',
                    'description' => 'List the CS85 course modules and their current status.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object) [],
                        'required' => [],
                        'additionalProperties' => false,
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_course_module',
                    'description' => 'Get the configured title, description, assignments, resources, and notes for one CS85 module.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'module' => [
                                'type' => 'string',
                                'description' => 'Module slug or label, for example module-8 or Module 8.',
                                'maxLength' => 40,
                            ],
                        ],
                        'required' => ['module'],
                        'additionalProperties' => false,
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_project_stack',
                    'description' => 'Get the public technology stack configured for this CS85 project.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object) [],
                        'required' => [],
                        'additionalProperties' => false,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(AiToolCall $toolCall): array
    {
        try {
            $arguments = json_decode($toolCall->arguments === '' ? '{}' : $toolCall->arguments, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new AiProviderException('The model supplied invalid tool arguments.', 'invalid_tool_arguments');
        }

        if (! is_array($arguments)) {
            throw new AiProviderException('The model supplied invalid tool arguments.', 'invalid_tool_arguments');
        }

        return match ($toolCall->name) {
            'list_course_modules' => $this->listCourseModules($arguments),
            'get_course_module' => $this->getCourseModule($arguments),
            'get_project_stack' => $this->getProjectStack($arguments),
            default => throw new AiProviderException('The model requested an unavailable tool.', 'tool_not_allowed'),
        };
    }

    /**
     * @param  array<string, mixed>  $arguments
     * @return array<string, mixed>
     */
    private function listCourseModules(array $arguments): array
    {
        $this->requireNoArguments($arguments);

        return [
            'modules' => collect(config('course.modules', []))
                ->map(fn (array $module): array => [
                    'module' => $module['module'],
                    'slug' => $module['slug'],
                    'title' => $module['title'],
                    'status' => $module['status'],
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $arguments
     * @return array<string, mixed>
     */
    private function getCourseModule(array $arguments): array
    {
        if (array_keys($arguments) !== ['module'] || ! is_string($arguments['module']) || mb_strlen($arguments['module']) > 40) {
            throw new AiProviderException('The course module tool received invalid arguments.', 'invalid_tool_arguments');
        }

        $requestedModule = mb_strtolower(trim($arguments['module']));
        $module = collect(config('course.modules', []))->first(function (array $module) use ($requestedModule): bool {
            return in_array($requestedModule, [
                mb_strtolower((string) $module['slug']),
                mb_strtolower((string) $module['module']),
            ], true);
        });

        if (! is_array($module)) {
            return ['found' => false, 'module' => null];
        }

        return [
            'found' => true,
            'module' => [
                'module' => $module['module'],
                'title' => $module['title'],
                'description' => $module['description'],
                'status' => $module['status'],
                'assignments' => collect($module['assignments'] ?? [])->map(fn (array $assignment): array => [
                    'title' => $assignment['title'] ?? 'Untitled assignment',
                    'description' => $assignment['description'] ?? null,
                ])->values()->all(),
                'resources' => $module['resources'] ?? [],
                'notes' => $module['notes'] ?? [],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $arguments
     * @return array<string, mixed>
     */
    private function getProjectStack(array $arguments): array
    {
        $this->requireNoArguments($arguments);

        return ['stack' => config('course.stack', [])];
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    private function requireNoArguments(array $arguments): void
    {
        if ($arguments !== []) {
            throw new AiProviderException('The tool does not accept arguments.', 'invalid_tool_arguments');
        }
    }
}
