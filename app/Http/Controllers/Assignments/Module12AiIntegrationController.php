<?php

declare(strict_types=1);

namespace App\Http\Controllers\Assignments;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class Module12AiIntegrationController extends Controller
{
    public function __invoke(): View
    {
        $modules = collect(config('course.modules'));
        $module = $modules->firstWhere('slug', 'module-12');

        abort_unless(is_array($module), 404);

        return view('pages.assignments.module12.ai-integration', [
            'module' => $module,
            'modules' => $modules,
            'modes' => config('ai.modes'),
        ]);
    }
}
