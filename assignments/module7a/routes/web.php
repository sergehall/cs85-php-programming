<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', ['embedded' => false]);
})->name('assignment');

Route::get('/hello', function () {
    return 'Hello from Laravel!';
})->name('hello');

Route::get('/greet/{name}', function (string $name) {
    $displayName = str_replace('-', ' ', ucwords(strtolower($name), '-'));

    if (strtolower($name) === 'vicky-seno') {
        return view('greeting', [
            'assignmentBase' => '',
            'displayName' => $displayName,
        ]);
    }

    return 'Hello, '.$displayName.'!';
})->where('name', '[A-Za-z]+(?:-[A-Za-z]+)*')->name('greet');
