<?php

use App\Http\Controllers\Assignments\AssignmentPhpPageController;
use App\Http\Controllers\Assignments\Module1Assignment1AController;
use App\Http\Controllers\Assignments\Module2BCosmicCalendarController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GitHubOAuthController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Cabinet\ActivityController;
use App\Http\Controllers\Cabinet\Admin\AdminUserRoleController;
use App\Http\Controllers\Cabinet\Admin\AdminUsersController;
use App\Http\Controllers\Cabinet\AdminAccessRequestController;
use App\Http\Controllers\Cabinet\CourseworkController;
use App\Http\Controllers\Cabinet\ProfileController;
use App\Http\Controllers\Cabinet\SecurityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/roadmap', function () {
    return view('pages.roadmap', [
        'modules' => config('course.modules'),
    ]);
})->name('roadmap');

Route::redirect('/modules/module-1', '/roadmap/module-1');
Route::redirect('/modules/module-2', '/roadmap/module-2');

Route::get('/roadmap/module-1/assignment-1a', Module1Assignment1AController::class)
    ->name('assignments.module1.assignment1a');

Route::get('/roadmap/module-2/cosmic-calendar', Module2BCosmicCalendarController::class)
    ->name('assignments.module2.cosmic-calendar');

$phpAssignments = [
    'module2a/price_engine.php' => 'module2a/price_engine.php',
    'module2a/price_engine_refactored.php' => 'module2a/price_engine_refactored.php',
    'module3a/ContactForm.php' => 'module3a/ContactForm.php',
    'module3/SecureProductContactForm.php' => 'module3b/SecureProductContactForm.php',
    'module3b/SecureProductContactForm.php' => 'module3b/SecureProductContactForm.php',
    'module4/database-setup.php' => 'module4a/database-setup.php',
    'module4a/database-setup.php' => 'module4a/database-setup.php',
    'module4b/show_inventory.php' => 'module4b/show_inventory.php',
];

foreach ($phpAssignments as $publicPath => $assignmentPath) {
    Route::match(['GET', 'POST'], "/{$publicPath}", AssignmentPhpPageController::class)
        ->defaults('assignmentPath', $assignmentPath);

    Route::match(['GET', 'POST'], "/assignments/{$assignmentPath}", AssignmentPhpPageController::class)
        ->defaults('assignmentPath', $assignmentPath);
}

Route::get('/roadmap/{module}', function (string $module) {
    $modules = collect(config('course.modules'));
    $selectedModule = $modules->firstWhere('slug', $module);

    abort_unless($selectedModule, 404);

    return view('pages.roadmap-module', [
        'module' => $selectedModule,
        'modules' => $modules,
        'position' => $modules->search(fn (array $item): bool => $item['slug'] === $module) + 1,
    ]);
})->name('roadmap.module');

Route::get('/stack', function () {
    return view('pages.stack', [
        'stack' => config('course.stack'),
    ]);
})->name('stack');

Route::get('/contact', function () {
    return view('pages.contact', [
        'contact' => config('course.contact'),
    ]);
})->name('contact');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
});

Route::get('/auth/github/redirect', [GitHubOAuthController::class, 'redirect'])->name('auth.github.redirect');
Route::get('/auth/github/callback', [GitHubOAuthController::class, 'callback'])->name('auth.github.callback');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::redirect('/admin', '/cabinet')->name('admin.redirect');

Route::prefix('cabinet')->middleware('auth')->name('cabinet.')->group(function () {
    Route::get('/', function (Request $request) {
        $user = $request->user();
        $configuredAccount = config('cabinet.account');
        $initials = collect(explode(' ', trim($user->name)))
            ->filter()
            ->map(fn (string $name): string => Str::upper(Str::substr($name, 0, 1)))
            ->take(2)
            ->implode('');

        return view('cabinet.dashboard', [
            'account' => array_merge($configuredAccount, [
                'name' => $user->name,
                'initials' => $initials ?: 'U',
                'email' => $user->email,
                'role' => config("navigation.roles.{$user->role}.label", Str::headline($user->role)),
                'status' => $user->github_id ? 'GitHub connected' : 'Password account',
            ]),
            'metrics' => config('cabinet.metrics'),
            'roles' => config('navigation.roles'),
            'focusItems' => config('cabinet.dashboard.focus'),
            'activityItems' => config('cabinet.dashboard.activity'),
        ]);
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/coursework', CourseworkController::class)->name('coursework');
    Route::get('/security', SecurityController::class)->name('security');
    Route::post('/security/admin-access-request', AdminAccessRequestController::class)->name('security.admin-access-request');

    Route::get('/activity', ActivityController::class)->name('activity');

    foreach (array_diff(array_keys(config('cabinet.sections', [])), ['profile', 'coursework', 'security', 'activity']) as $sectionKey) {
        Route::get("/{$sectionKey}", function () use ($sectionKey) {
            return view('cabinet.section', [
                'section' => config("cabinet.sections.{$sectionKey}"),
            ]);
        })->name($sectionKey);
    }

    Route::prefix('admin')->middleware('admin')->name('admin.')->group(function () {
        Route::get('/', function () {
            return view('cabinet.admin-dashboard', [
                'summary' => config('cabinet.admin.dashboard.summary'),
                'sections' => config('cabinet.admin.dashboard.sections'),
            ]);
        })->name('dashboard');

        Route::get('/users', AdminUsersController::class)->name('users');
        Route::patch('/access-requests/{adminAccessRequest}/approve', [AdminUserRoleController::class, 'approve'])
            ->name('access-requests.approve');
        Route::patch('/users/{user}/revoke-admin', [AdminUserRoleController::class, 'revoke'])
            ->name('users.revoke-admin');

        foreach (array_diff(array_keys(config('cabinet.admin.sections', [])), ['users']) as $sectionKey) {
            Route::get("/{$sectionKey}", function () use ($sectionKey) {
                return view('cabinet.admin-section', [
                    'section' => config("cabinet.admin.sections.{$sectionKey}"),
                ]);
            })->name($sectionKey);
        }
    });
});
