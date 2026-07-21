<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/roadmap/module-10/assignment', function () {
    return view()->file(base_path('assignments/module10a/resources/views/overview.blade.php'));
})->name('assignments.module10a.overview');

Route::middleware(['auth', 'login.enabled'])->group(function (): void {
    Route::get('/dashboard', function (Request $request) {
        return view()->file(base_path('assignments/module10a/resources/views/dashboard.blade.php'), [
            'user' => $request->user(),
        ]);
    })->name('dashboard');

    Route::get('/secret', function (Request $request) {
        return view()->file(base_path('assignments/module10a/resources/views/secret.blade.php'), [
            'user' => $request->user(),
        ]);
    })->name('secret');
});
