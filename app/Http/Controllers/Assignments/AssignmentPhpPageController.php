<?php

declare(strict_types=1);

namespace App\Http\Controllers\Assignments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class AssignmentPhpPageController extends Controller
{
    /**
     * @var array<string, true>
     */
    private const ALLOWED_ASSIGNMENTS = [
        'module2a/price_engine.php' => true,
        'module2a/price_engine_refactored.php' => true,
        'module3a/ContactForm.php' => true,
        'module3b/SecureProductContactForm.php' => true,
        'module4a/database-setup.php' => true,
        'module4b/show_inventory.php' => true,
    ];

    public function __invoke(Request $request, string $assignmentPath): Response
    {
        abort_unless(isset(self::ALLOWED_ASSIGNMENTS[$assignmentPath]), 404);

        $filePath = base_path("assignments/{$assignmentPath}");

        abort_unless(is_file($filePath), 404);

        ob_start();
        require $filePath;
        $contents = ob_get_clean();

        return response((string) $contents);
    }
}
