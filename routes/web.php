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
            'roles' => config('navigation.roles'),
            'sections' => [
                [
                    'title' => 'Profile',
                    'route' => 'cabinet.profile',
                    'status' => 'User',
                    'description' => 'A future place for account identity, preferences, and student profile data.',
                ],
                [
                    'title' => 'Coursework',
                    'route' => 'cabinet.coursework',
                    'status' => 'User',
                    'description' => 'A future workspace for assignments, labs, notes, and final project progress.',
                ],
                [
                    'title' => 'Messages',
                    'route' => 'cabinet.messages',
                    'status' => 'User',
                    'description' => 'A future inbox for course questions, feedback, and contact submissions.',
                ],
                [
                    'title' => 'Admin Tools',
                    'route' => 'cabinet.admin.dashboard',
                    'status' => 'Admin',
                    'description' => 'A protected future area for users, content, messages, and operational review.',
                ],
            ],
        ]);
    })->name('dashboard');

    Route::get('/profile', function () {
        return view('cabinet.placeholder', [
            'title' => 'Profile',
            'role' => 'User',
            'description' => 'This screen will grow into personal profile management after authentication is introduced.',
            'nextSteps' => [
                'Add authentication scaffolding when the course reaches sessions and authorization.',
                'Connect profile fields to the users table and a profile table.',
                'Protect edits with policies so users can only update their own data.',
            ],
        ]);
    })->name('profile');

    Route::get('/coursework', function () {
        return view('cabinet.placeholder', [
            'title' => 'Coursework',
            'role' => 'User',
            'description' => 'This screen will organize assignment, lab, note, and final project progress.',
            'nextSteps' => [
                'Create database tables for assignments, labs, notes, and milestones.',
                'Build CRUD screens using controllers and form requests.',
                'Add feature tests for create, update, validation, and delete flows.',
            ],
        ]);
    })->name('coursework');

    Route::get('/messages', function () {
        return view('cabinet.placeholder', [
            'title' => 'Messages',
            'role' => 'User',
            'description' => 'This screen will collect user messages and project feedback once forms are added.',
            'nextSteps' => [
                'Add a contact form with server-side validation.',
                'Persist messages in MySQL with reviewed and archived states.',
                'Notify the owner without exposing mail credentials.',
            ],
        ]);
    })->name('messages');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', function () {
            return view('cabinet.admin-dashboard', [
                'sections' => [
                    [
                        'title' => 'Users',
                        'route' => 'cabinet.admin.users',
                        'status' => 'Prepared',
                        'description' => 'A future place for student accounts, roles, and access review.',
                    ],
                    [
                        'title' => 'Content',
                        'route' => 'cabinet.admin.content',
                        'status' => 'Prepared',
                        'description' => 'A future editorial surface for assignments, labs, notes, and project pages.',
                    ],
                    [
                        'title' => 'Messages',
                        'route' => 'cabinet.admin.messages',
                        'status' => 'Prepared',
                        'description' => 'A future admin inbox for contact requests, project feedback, and operational notes.',
                    ],
                ],
            ]);
        })->name('dashboard');

        Route::get('/users', function () {
            return view('cabinet.placeholder', [
                'title' => 'Users',
                'role' => 'Admin',
                'description' => 'This screen will grow into user and role management after authentication is introduced.',
                'nextSteps' => [
                    'Add role and status columns for user governance.',
                    'Protect this route with admin middleware and policies.',
                    'Add audit records for privileged account changes.',
                ],
            ]);
        })->name('users');

        Route::get('/content', function () {
            return view('cabinet.placeholder', [
                'title' => 'Content',
                'role' => 'Admin',
                'description' => 'This screen will manage course content records once project data moves into the database.',
                'nextSteps' => [
                    'Create content migrations and models.',
                    'Build admin CRUD controllers and form requests.',
                    'Add validation and authorization tests for each mutation.',
                ],
            ]);
        })->name('content');

        Route::get('/messages', function () {
            return view('cabinet.placeholder', [
                'title' => 'Messages',
                'role' => 'Admin',
                'description' => 'This screen will review user messages and contact submissions.',
                'nextSteps' => [
                    'List submitted messages with reviewed and archived filters.',
                    'Add status changes protected by admin policy checks.',
                    'Add tests for user visibility versus admin visibility.',
                ],
            ]);
        })->name('messages');
    });
});
