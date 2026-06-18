<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/roadmap', function () {
    return view('pages.roadmap', [
        'modules' => config('course.modules'),
    ]);
})->name('roadmap');

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

Route::redirect('/admin', '/cabinet')->name('admin.redirect');

Route::prefix('cabinet')->name('cabinet.')->group(function () {
    Route::get('/', function () {
        return view('cabinet.dashboard', [
            'account' => config('cabinet.account'),
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

    Route::prefix('admin')->name('admin.')->group(function () {
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
