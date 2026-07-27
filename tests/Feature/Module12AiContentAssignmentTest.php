<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\AiContentService;
use RuntimeException;
use Tests\TestCase;

final class Module12AiContentAssignmentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_assignment_12a_form_exposes_every_required_input(): void
    {
        $this->get(route('ai.form'))
            ->assertOk()
            ->assertSeeText('Assignment 12A')
            ->assertSeeText('Integrating OpenAI')
            ->assertSeeText('gpt-4o-mini')
            ->assertSee('name="title"', false)
            ->assertSee('name="type"', false)
            ->assertSee('name="tone"', false)
            ->assertSee(route('ai.generate'), false)
            ->assertSee(route('roadmap.module', 'module-12'), false);
    }

    public function test_generate_returns_the_mocked_services_output(): void
    {
        $fake = new class extends AiContentService
        {
            /** @var list<string> */
            public array $received = [];

            public function generateDraft(
                string $title,
                string $type = 'blog post',
                string $tone = 'professional',
            ): string {
                $this->received = [$title, $type, $tone];

                return 'A generated draft.';
            }
        };
        $this->app->instance(AiContentService::class, $fake);

        $this->post(route('ai.generate'), [
            'title' => 'A meaningful test title',
            'type' => 'blog post',
            'tone' => 'professional',
        ])
            ->assertOk()
            ->assertSeeText('A generated draft.')
            ->assertSeeText('Editable result');

        $this->assertSame(
            ['A meaningful test title', 'blog post', 'professional'],
            $fake->received,
        );
    }

    public function test_assignment_12a_validates_title_type_and_tone_before_calling_ai(): void
    {
        $fake = new class extends AiContentService
        {
            public bool $called = false;

            public function generateDraft(
                string $title,
                string $type = 'blog post',
                string $tone = 'professional',
            ): string {
                $this->called = true;

                return 'This should never be returned.';
            }
        };
        $this->app->instance(AiContentService::class, $fake);

        $this->from(route('ai.form'))
            ->post(route('ai.generate'), [
                'title' => 'Tiny',
                'type' => 'untrusted format',
                'tone' => 'hostile',
            ])
            ->assertRedirect(route('ai.form'))
            ->assertSessionHasErrors(['title', 'type', 'tone']);

        $this->assertFalse($fake->called);
    }

    public function test_assignment_12a_preserves_input_and_returns_a_safe_provider_error(): void
    {
        $fake = new class extends AiContentService
        {
            public function generateDraft(
                string $title,
                string $type = 'blog post',
                string $tone = 'professional',
            ): string {
                throw new RuntimeException('Provider detail that must stay private.');
            }
        };
        $this->app->instance(AiContentService::class, $fake);

        $this->from(route('ai.form'))
            ->post(route('ai.generate'), [
                'title' => 'Safe failure behavior',
                'type' => 'meta description',
                'tone' => 'casual',
            ])
            ->assertRedirect(route('ai.form'))
            ->assertSessionHasInput('title', 'Safe failure behavior')
            ->assertSessionHasErrors([
                'error' => 'The AI draft could not be generated right now. Please try again.',
            ]);
    }

    public function test_module_12_page_maps_assignment_12a_to_the_final_project(): void
    {
        $this->get(route('roadmap.module', 'module-12'))
            ->assertOk()
            ->assertSeeText('AI-Powered Web Application')
            ->assertSeeText('Assignment 12A proves the API call. The final project proves the system.')
            ->assertSeeText('OpenAI cloud · gpt-4o-mini')
            ->assertSeeText('LM Studio through an OpenAI-compatible provider contract')
            ->assertSeeText('Production concerns are implemented, not simulated.')
            ->assertSee(route('ai.form'), false)
            ->assertSee(route('cabinet.ai'), false);
    }

    public function test_module_12_course_configuration_registers_both_deliverables(): void
    {
        $module = collect(config('course.modules'))->firstWhere('slug', 'module-12');

        $this->assertIsArray($module);
        $this->assertSame('Complete', $module['status']);
        $this->assertSame('pages.assignments.module12.ai-integration', $module['view']);
        $this->assertCount(2, $module['assignments']);
        $this->assertSame('Assignment 12A', $module['assignments'][0]['label']);
        $this->assertSame('ai.form', $module['assignments'][0]['route']);
        $this->assertSame('Final Project', $module['assignments'][1]['label']);
        $this->assertSame('cabinet.ai', $module['assignments'][1]['route']);
        $this->assertFileExists(base_path('assignments/module12a/README.md'));
        $this->assertFileExists(base_path('assignments/final-project-ai/README.md'));
    }
}
