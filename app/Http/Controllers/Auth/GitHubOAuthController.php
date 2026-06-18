<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GitHubOAuthController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        if (! config('services.github.client_id') || ! config('services.github.client_secret')) {
            return redirect()
                ->route('login')
                ->withErrors(['github' => 'GitHub OAuth is not configured yet.']);
        }

        $state = Str::random(40);
        $request->session()->put('oauth.github_state', $state);

        $query = http_build_query([
            'client_id' => config('services.github.client_id'),
            'redirect_uri' => config('services.github.redirect'),
            'scope' => 'read:user user:email',
            'state' => $state,
            'allow_signup' => 'true',
        ]);

        return redirect()->away("https://github.com/login/oauth/authorize?{$query}");
    }

    public function callback(Request $request): RedirectResponse
    {
        $expectedState = $request->session()->pull('oauth.github_state');

        if (! $expectedState || ! hash_equals($expectedState, (string) $request->query('state'))) {
            throw ValidationException::withMessages([
                'github' => 'GitHub sign-in state could not be verified.',
            ]);
        }

        if ($request->filled('error')) {
            throw ValidationException::withMessages([
                'github' => 'GitHub sign-in was cancelled or denied.',
            ]);
        }

        $tokenResponse = Http::asForm()
            ->acceptJson()
            ->post('https://github.com/login/oauth/access_token', [
                'client_id' => config('services.github.client_id'),
                'client_secret' => config('services.github.client_secret'),
                'code' => $request->query('code'),
                'redirect_uri' => config('services.github.redirect'),
            ]);

        if ($tokenResponse->failed() || ! $tokenResponse->json('access_token')) {
            throw ValidationException::withMessages([
                'github' => 'GitHub token exchange failed.',
            ]);
        }

        $token = (string) $tokenResponse->json('access_token');
        $github = $this->githubClient($token);
        $profileResponse = $github->get('https://api.github.com/user');

        if ($profileResponse->failed()) {
            throw ValidationException::withMessages([
                'github' => 'GitHub profile could not be loaded.',
            ]);
        }

        $profile = $profileResponse->json();
        $email = $profile['email'] ?? $this->primaryEmail($github);

        if (! $email) {
            throw ValidationException::withMessages([
                'github' => 'GitHub did not return a usable email address.',
            ]);
        }

        $user = User::query()
            ->where('github_id', (string) $profile['id'])
            ->orWhere('email', $email)
            ->first();

        $userData = [
            'name' => $profile['name'] ?: $profile['login'],
            'email' => $email,
            'github_id' => (string) $profile['id'],
            'github_username' => $profile['login'],
            'github_avatar_url' => $profile['avatar_url'] ?? null,
            'email_verified_at' => now(),
        ];

        if ($user) {
            $user->forceFill($userData)->save();
        } else {
            $user = User::query()->create($userData + [
                'password' => Str::password(48),
                'role' => 'user',
            ]);
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return redirect()->intended(route('cabinet.dashboard'));
    }

    private function githubClient(string $token): PendingRequest
    {
        return Http::withToken($token)
            ->acceptJson()
            ->withHeaders(['X-GitHub-Api-Version' => '2022-11-28']);
    }

    private function primaryEmail(PendingRequest $github): ?string
    {
        $response = $github->get('https://api.github.com/user/emails');

        if ($response->failed()) {
            return null;
        }

        $primary = collect($response->json())
            ->first(fn (array $email): bool => ($email['primary'] ?? false) && ($email['verified'] ?? false));

        return is_array($primary) && is_string($primary['email'] ?? null) ? $primary['email'] : null;
    }
}
