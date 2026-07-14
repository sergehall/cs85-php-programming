<?php

namespace App\Providers;

use App\Services\AI\Contracts\AiProviderInterface;
use App\Services\AI\Providers\LmStudioProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use LogicException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AiProviderInterface::class, function ($app): AiProviderInterface {
            return match (config('ai.provider')) {
                'lm_studio' => $app->make(LmStudioProvider::class),
                default => throw new LogicException('The configured AI provider is not supported.'),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('ai', function (Request $request): Limit {
            $key = $request->user()?->getAuthIdentifier() ?? $request->ip();

            return Limit::perMinute(max(1, (int) config('ai.limits.requests_per_minute')))
                ->by('ai:'.$key);
        });

        // Module 7B remains a standalone Laravel project, while this namespace
        // lets the coursework application render its Blade views as an embedded app.
        View::addNamespace('module7b', base_path('assignments/module7b/resources/views'));
    }
}
