<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GitHubOAuthController;
use App\Http\Controllers\Auth\RegisteredUserController;
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

    Route::get('/auth/github/redirect', [GitHubOAuthController::class, 'redirect'])->name('auth.github.redirect');
    Route::get('/auth/github/callback', [GitHubOAuthController::class, 'callback'])->name('auth.github.callback');
});

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

    foreach (array_keys(config('cabinet.sections', [])) as $sectionKey) {
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

        foreach (array_keys(config('cabinet.admin.sections', [])) as $sectionKey) {
            Route::get("/{$sectionKey}", function () use ($sectionKey) {
                return view('cabinet.admin-section', [
                    'section' => config("cabinet.admin.sections.{$sectionKey}"),
                ]);
            })->name($sectionKey);
        }
    });
});
