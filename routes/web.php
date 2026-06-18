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

    Route::get('/profile', function () {
        return view('cabinet.section', [
            'section' => config('cabinet.sections.profile'),
        ]);
    })->name('profile');

    Route::get('/coursework', function () {
        return view('cabinet.section', [
            'section' => config('cabinet.sections.coursework'),
        ]);
    })->name('coursework');

    Route::get('/messages', function () {
        return view('cabinet.section', [
            'section' => config('cabinet.sections.messages'),
        ]);
    })->name('messages');

    Route::get('/security', function () {
        return view('cabinet.section', [
            'section' => config('cabinet.sections.security'),
        ]);
    })->name('security');

    Route::get('/activity', function () {
        return view('cabinet.section', [
            'section' => config('cabinet.sections.activity'),
        ]);
    })->name('activity');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', function () {
            return view('cabinet.admin-dashboard', [
                'summary' => config('cabinet.admin.dashboard.summary'),
                'sections' => config('cabinet.admin.dashboard.sections'),
            ]);
        })->name('dashboard');

        Route::get('/users', function () {
            return view('cabinet.admin-section', [
                'section' => config('cabinet.admin.sections.users'),
            ]);
        })->name('users');

        Route::get('/content', function () {
            return view('cabinet.admin-section', [
                'section' => config('cabinet.admin.sections.content'),
            ]);
        })->name('content');

        Route::get('/messages', function () {
            return view('cabinet.admin-section', [
                'section' => config('cabinet.admin.sections.messages'),
            ]);
        })->name('messages');
    });
});
