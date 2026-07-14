<?php

use App\Http\Controllers\Assignments\AssignmentPhpPageController;
use App\Http\Controllers\Assignments\Module1Assignment1AController;
use App\Http\Controllers\Assignments\Module2BCosmicCalendarController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GitHubOAuthController;
use App\Http\Controllers\Auth\MfaChallengeController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Cabinet\ActivityController;
use App\Http\Controllers\Cabinet\Admin\AdminDashboardController;
use App\Http\Controllers\Cabinet\Admin\AdminUserLoginAccessController;
use App\Http\Controllers\Cabinet\Admin\AdminUserRoleController;
use App\Http\Controllers\Cabinet\Admin\AdminUsersController;
use App\Http\Controllers\Cabinet\AdminAccessRequestController;
use App\Http\Controllers\Cabinet\CourseworkController;
use App\Http\Controllers\Cabinet\MfaController;
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
    'module5a/MyObject.php' => 'module5a/MyObject.php',
    'module6a/public/index.php' => 'module6a/public/index.php',
];

foreach ($phpAssignments as $publicPath => $assignmentPath) {
    Route::match(['GET', 'POST'], "/{$publicPath}", AssignmentPhpPageController::class)
        ->defaults('assignmentPath', $assignmentPath);

    Route::match(['GET', 'POST'], "/assignments/{$assignmentPath}", AssignmentPhpPageController::class)
        ->defaults('assignmentPath', $assignmentPath);
}

Route::prefix('assignments/module7a')->name('assignments.module7a.')->group(function () {
    Route::get('/', function () {
        return view()->file(base_path('assignments/module7a/resources/views/welcome.blade.php'), [
            'embedded' => true,
        ]);
    })->name('index');

    Route::get('/hello', function () {
        return 'Hello from Laravel!';
    })->name('hello');

    Route::get('/greet/{name}', function (string $name) {
        $displayName = str_replace('-', ' ', ucwords(strtolower($name), '-'));

        if (strtolower($name) === 'vicky-seno') {
            return view()->file(base_path('assignments/module7a/resources/views/greeting.blade.php'), [
                'assignmentBase' => '/assignments/module7a',
                'displayName' => $displayName,
            ]);
        }

        return 'Hello, '.$displayName.'!';
    })->where('name', '[A-Za-z]+(?:-[A-Za-z]+)*')->name('greet');
});

// Assignment 7B is also a complete Laravel project. These routes mount its
// presentation inside the coursework roadmap without changing its standalone URLs.
$module7bHobbies = [
    1 => [
        'id' => 1,
        'name' => 'Photography',
        'eyebrow' => 'Creative practice',
        'description' => 'I create portraits and visual stories through my SERGIOARTG photography work.',
        'why_i_like_it' => 'Photography helps me combine technical camera skills with observation, timing, and communication.',
        'detail' => 'I enjoy shaping light, directing a portrait session, and refining the final image so it communicates a clear mood.',
        'icon' => 'camera',
    ],
    2 => [
        'id' => 2,
        'name' => 'Web Development',
        'eyebrow' => 'Continuous learning',
        'description' => 'I build web experiences and study how frontend and backend systems work together.',
        'why_i_like_it' => 'Web development gives me a practical way to turn an idea into something useful that other people can interact with.',
        'detail' => 'My current focus includes PHP, Laravel, accessible interfaces, secure application structure, and maintainable code.',
        'icon' => 'code',
    ],
    3 => [
        'id' => 3,
        'name' => 'Technology Projects',
        'eyebrow' => 'Hands-on exploration',
        'description' => 'I like experimenting with software tools and connecting small technical ideas into complete projects.',
        'why_i_like_it' => 'Personal projects make abstract concepts easier to understand because every decision produces a visible result.',
        'detail' => 'I use these experiments to practice debugging, version control, documentation, and thoughtful product presentation.',
        'icon' => 'spark',
    ],
];

Route::prefix('assignments/module7b')->name('assignments.module7b.')->group(function () use ($module7bHobbies) {
    $sharedViewData = [
        'embedded' => true,
        'layout' => 'module7b::layouts.app',
        'routePrefix' => 'assignments.module7b.',
    ];

    Route::get('/', function () use ($sharedViewData) {
        return view()->file(base_path('assignments/module7b/resources/views/home.blade.php'), [
            ...$sharedViewData,
            'name' => 'Siarhei Hancharou',
            'title' => 'Welcome to My Personal Route Lab',
        ]);
    })->name('home');

    Route::get('/about', function () use ($sharedViewData) {
        return view()->file(base_path('assignments/module7b/resources/views/about.blade.php'), [
            ...$sharedViewData,
            'age' => 'Prefer not to disclose',
            'school' => 'Santa Monica College',
            'major' => 'Web Development (A.S.)',
        ]);
    })->name('about');

    Route::get('/hobbies', function () use ($module7bHobbies, $sharedViewData) {
        return view()->file(base_path('assignments/module7b/resources/views/hobbies/index.blade.php'), [
            ...$sharedViewData,
            'hobbies' => $module7bHobbies,
        ]);
    })->name('hobbies.index');

    Route::get('/hobbies/{id}', function (int $id) use ($module7bHobbies, $sharedViewData) {
        abort_unless(isset($module7bHobbies[$id]), 404, 'Hobby not found');

        return view()->file(base_path('assignments/module7b/resources/views/hobbies/show.blade.php'), [
            ...$sharedViewData,
            'hobby' => $module7bHobbies[$id],
        ]);
    })->whereNumber('id')->name('hobbies.show');
});

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

Route::get('/mfa-challenge', [MfaChallengeController::class, 'create'])->name('mfa.challenge');
Route::post('/mfa-challenge', [MfaChallengeController::class, 'store'])->name('mfa.challenge.store');

Route::get('/auth/github/redirect', [GitHubOAuthController::class, 'redirect'])->name('auth.github.redirect');
Route::get('/auth/github/callback', [GitHubOAuthController::class, 'callback'])->name('auth.github.callback');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::redirect('/admin', '/cabinet')->name('admin.redirect');

Route::prefix('cabinet')->middleware(['auth', 'login.enabled'])->name('cabinet.')->group(function () {
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
                'photo_url' => $user->profilePhotoUrl(),
                'role' => config("navigation.roles.{$user->role}.label", Str::headline($user->role)),
                'status' => $user->hasMfaEnabled()
                    ? 'MFA protected'
                    : ($user->github_id ? 'GitHub connected' : 'Password account'),
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
    Route::post('/security/mfa/start', [MfaController::class, 'start'])->name('security.mfa.start');
    Route::post('/security/mfa/confirm', [MfaController::class, 'confirm'])->name('security.mfa.confirm');
    Route::delete('/security/mfa', [MfaController::class, 'destroy'])->name('security.mfa.destroy');

    Route::get('/activity', ActivityController::class)->name('activity');

    foreach (array_diff(array_keys(config('cabinet.sections', [])), ['profile', 'coursework', 'security', 'activity']) as $sectionKey) {
        Route::get("/{$sectionKey}", function () use ($sectionKey) {
            return view('cabinet.section', [
                'section' => config("cabinet.sections.{$sectionKey}"),
            ]);
        })->name($sectionKey);
    }

    Route::prefix('admin')->middleware('admin')->name('admin.')->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');

        Route::get('/users', AdminUsersController::class)->name('users');
        Route::patch('/access-requests/{adminAccessRequest}/approve', [AdminUserRoleController::class, 'approve'])
            ->name('access-requests.approve');
        Route::patch('/users/{user}/revoke-admin', [AdminUserRoleController::class, 'revoke'])
            ->name('users.revoke-admin');
        Route::patch('/users/{user}/disable-login', [AdminUserLoginAccessController::class, 'disable'])
            ->name('users.disable-login');
        Route::patch('/users/{user}/enable-login', [AdminUserLoginAccessController::class, 'enable'])
            ->name('users.enable-login');

        foreach (array_diff(array_keys(config('cabinet.admin.sections', [])), ['users']) as $sectionKey) {
            Route::get("/{$sectionKey}", function () use ($sectionKey) {
                return view('cabinet.admin-section', [
                    'section' => config("cabinet.admin.sections.{$sectionKey}"),
                ]);
            })->name($sectionKey);
        }
    });
});
