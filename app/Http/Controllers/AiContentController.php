<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\AiContentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

final class AiContentController extends Controller
{
    public function showForm(): View
    {
        return view('ai_form', $this->viewData());
    }

    public function generate(Request $request, AiContentService $ai): View|RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'type' => ['required', 'in:blog post,meta description,email subject line'],
            'tone' => ['required', 'in:professional,casual,humorous'],
        ]);

        try {
            $output = $ai->generateDraft(
                $validated['title'],
                $validated['type'],
                $validated['tone'],
            );

            return view('ai_form', $this->viewData([
                'output' => $output,
                'title' => $validated['title'],
                'selectedType' => $validated['type'],
                'selectedTone' => $validated['tone'],
            ]));
        } catch (Throwable $exception) {
            Log::warning('Assignment 12A draft generation could not be completed.', [
                'exception' => $exception::class,
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'The AI draft could not be generated right now. Please try again.',
                ]);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function viewData(array $data = []): array
    {
        $modules = collect(config('course.modules'));
        $module = $modules->firstWhere('slug', 'module-12');

        abort_unless(is_array($module), 404);

        return [
            'module' => $module,
            'modules' => $modules,
            ...$data,
        ];
    }
}
