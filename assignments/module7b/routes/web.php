<?php

use App\Http\Controllers\HobbyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Closure-based routes
|--------------------------------------------------------------------------
|
| These two routes use anonymous functions. Each closure prepares simple
| PHP variables and passes them to a Blade view as an associative array.
|
*/

Route::get('/', function () {
    $name = 'Siarhei Hancharou';
    $title = 'Welcome to My Personal Route Lab';

    return view('home', [
        'embedded' => false,
        'name' => $name,
        'routePrefix' => '',
        'title' => $title,
    ]);
})->name('home');

Route::get('/about', function () {
    $age = 'Prefer not to disclose';
    $school = 'Santa Monica College';
    $major = 'Web Development (A.S.)';

    return view('about', [
        'age' => $age,
        'embedded' => false,
        'major' => $major,
        'routePrefix' => '',
        'school' => $school,
    ]);
})->name('about');

/*
|--------------------------------------------------------------------------
| Controller-based routes
|--------------------------------------------------------------------------
|
| The hobbies pages delegate request handling to HobbyController. The {id}
| segment makes the detail page dynamic, while whereNumber rejects invalid
| route values before the controller runs.
|
*/

Route::get('/hobbies', [HobbyController::class, 'index'])->name('hobbies.index');

Route::get('/hobbies/{id}', [HobbyController::class, 'show'])
    ->whereNumber('id')
    ->name('hobbies.show');
