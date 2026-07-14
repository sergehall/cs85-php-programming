<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Module 7B remains a standalone Laravel project, while this namespace
        // lets the coursework application render its Blade views as an embedded app.
        View::addNamespace('module7b', base_path('assignments/module7b/resources/views'));
    }
}
