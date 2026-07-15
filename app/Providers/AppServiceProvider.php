<?php

namespace App\Providers;

use App\Models\User;
use App\Services\AI\Contracts\AiProviderInterface;
use App\Services\AI\Providers\LmStudioProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
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
        VerifyEmail::createUrlUsing(static function (object $notifiable): string {
            if (! $notifiable instanceof User) {
                throw new LogicException('Email verification requires a user public UUID.');
            }

            return URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes((int) config('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->public_uuid,
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ],
            );
        });

        Password::defaults(fn (): Password => Password::min(12)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols());

        RateLimiter::for('auth-login', function (Request $request): array {
            $email = Str::lower(trim((string) $request->input('email')));

            return [
                Limit::perMinute(5)->by('auth-login:identity:'.hash('sha256', $email.'|'.$request->ip())),
                Limit::perMinute(20)->by('auth-login:ip:'.$request->ip()),
            ];
        });

        RateLimiter::for('auth-mfa', function (Request $request): array {
            $pendingUser = (string) $request->session()->get('auth.mfa.user_id', 'guest');

            return [
                Limit::perMinute(5)->by('auth-mfa:user:'.$pendingUser.'|'.$request->ip()),
                Limit::perMinute(20)->by('auth-mfa:ip:'.$request->ip()),
            ];
        });

        RateLimiter::for('auth-registration', fn (Request $request): array => [
            Limit::perMinute(3)->by('auth-registration:'.$request->ip()),
            Limit::perHour(10)->by('auth-registration-hour:'.$request->ip()),
        ]);

        RateLimiter::for('auth-recovery', function (Request $request): array {
            $email = Str::lower(trim((string) $request->input('email')));

            return [
                Limit::perMinute(3)->by('auth-recovery:identity:'.hash('sha256', $email.'|'.$request->ip())),
                Limit::perHour(20)->by('auth-recovery:ip:'.$request->ip()),
            ];
        });

        RateLimiter::for('auth-oauth', fn (Request $request): array => [
            Limit::perMinute(10)->by('auth-oauth:'.$request->ip()),
            Limit::perHour(60)->by('auth-oauth-hour:'.$request->ip()),
        ]);

        RateLimiter::for('auth-sensitive', function (Request $request): Limit {
            $key = $request->user()?->getAuthIdentifier() ?? $request->ip();

            return Limit::perMinute(5)->by('auth-sensitive:'.$key.'|'.$request->ip());
        });

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
