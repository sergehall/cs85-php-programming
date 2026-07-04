<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GitHubOAuthController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        $failureRoute = $request->user() ? 'cabinet.security' : 'login';

        if (! config('services.github.client_id') || ! config('services.github.client_secret')) {
            return redirect()
                ->route($failureRoute)
                ->withErrors(['github' => 'GitHub OAuth is not configured yet.']);
        }

        $state = Str::random(40);
        $request->session()->put('oauth.github_state', $state);

        if ($request->user()) {
            $request->session()->put('oauth.github_link_user_id', $request->user()->getKey());
        } else {
            $request->session()->forget('oauth.github_link_user_id');
        }

        $query = http_build_query([
            'client_id' => config('services.github.client_id'),
            'redirect_uri' => config('services.github.redirect'),
            'scope' => 'read:user user:email',
            'state' => $state,
            'allow_signup' => 'true',
        ]);

        return redirect()->away("https://github.com/login/oauth/authorize?{$query}");
    }

    public function callback(Request $request, ActivityLogger $activity): RedirectResponse
    {
        $expectedState = $request->session()->pull('oauth.github_state');

        if (! $expectedState || ! hash_equals($expectedState, (string) $request->query('state'))) {
            return $this->fail($request, 'GitHub sign-in state could not be verified.');
        }

        if ($request->filled('error')) {
            return $this->fail($request, 'GitHub sign-in was cancelled or denied.');
        }

        $linkUserId = $request->session()->pull('oauth.github_link_user_id');

        $tokenResponse = Http::asForm()
            ->acceptJson()
            ->post('https://github.com/login/oauth/access_token', [
                'client_id' => config('services.github.client_id'),
                'client_secret' => config('services.github.client_secret'),
                'code' => $request->query('code'),
                'redirect_uri' => config('services.github.redirect'),
            ]);

        if ($tokenResponse->failed() || ! $tokenResponse->json('access_token')) {
            return $this->fail($request, 'GitHub token exchange failed.');
        }

        $token = (string) $tokenResponse->json('access_token');
        $github = $this->githubClient($token);
        $profileResponse = $github->get('https://api.github.com/user');

        if ($profileResponse->failed()) {
            return $this->fail($request, 'GitHub profile could not be loaded.');
        }

        $profile = $profileResponse->json();
        $email = $profile['email'] ?? $this->primaryEmail($github);

        if (! $email) {
            return $this->fail($request, 'GitHub did not return a usable email address.');
        }

        $userData = [
            'name' => $profile['name'] ?: $profile['login'],
            'email' => $email,
            'github_id' => (string) $profile['id'],
            'github_username' => $profile['login'],
            'github_avatar_url' => $profile['avatar_url'] ?? null,
            'email_verified_at' => now(),
        ];

        if ($request->user() && $linkUserId === $request->user()->getKey()) {
            return $this->linkCurrentUser($request, $userData['github_id'], $email, $userData, $activity);
        }

        $githubUser = User::query()
            ->where('github_id', $userData['github_id'])
            ->first();
        $emailUser = User::query()
            ->where('email', $email)
            ->first();

        if ($githubUser && $emailUser && ! $githubUser->is($emailUser)) {
            return $this->fail($request, $this->accountLinkingConflictMessage());
        }

        $user = $githubUser ?: $emailUser;

        if ($user && ! $user->canLogIn()) {
            return $this->fail($request, 'This account is not allowed to sign in right now. Contact an administrator.');
        }

        $shouldLogGithubConnection = ! $user || ! $user->github_id;

        if ($user) {
            $user->forceFill($userData)->save();
        } else {
            $user = User::query()->create($userData + [
                'password' => Str::password(48),
                'role' => 'user',
            ]);
        }

        if ($shouldLogGithubConnection) {
            $activity->record(
                subject: $user,
                actor: $user,
                category: 'security',
                event: 'security.github_connected',
                title: 'GitHub connected',
                description: 'GitHub OAuth was connected as an external identity provider.',
            );
        }

        if ($user->hasMfaEnabled()) {
            $request->session()->put('auth.mfa.user_id', $user->getKey());
            $request->session()->put('auth.mfa.remember', true);

            return redirect()->route('mfa.challenge');
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return redirect()->intended(route('cabinet.dashboard'));
    }

    /**
     * @param  array{name:mixed,email:string,github_id:string,github_username:mixed,github_avatar_url:mixed,email_verified_at:mixed}  $userData
     */
    private function linkCurrentUser(Request $request, string $githubId, string $email, array $userData, ActivityLogger $activity): RedirectResponse
    {
        $currentUser = $request->user();

        if (! $currentUser instanceof User) {
            return $this->fail($request, 'You must be signed in before connecting GitHub.');
        }

        $githubOwner = User::query()
            ->where('github_id', $githubId)
            ->whereKeyNot($currentUser->getKey())
            ->first();

        if ($githubOwner) {
            return $this->fail($request, $this->accountLinkingConflictMessage());
        }

        $emailOwner = User::query()
            ->where('email', $email)
            ->whereKeyNot($currentUser->getKey())
            ->first();

        if ($emailOwner) {
            return $this->fail($request, $this->accountLinkingConflictMessage());
        }

        $currentUser->forceFill([
            'github_id' => $userData['github_id'],
            'github_username' => $userData['github_username'],
            'github_avatar_url' => $userData['github_avatar_url'],
            'email_verified_at' => $currentUser->email === $email ? now() : $currentUser->email_verified_at,
        ])->save();

        $activity->record(
            subject: $currentUser,
            actor: $currentUser,
            category: 'security',
            event: 'security.github_connected',
            title: 'GitHub connected',
            description: 'GitHub OAuth was connected as an external identity provider.',
        );

        $request->session()->regenerate();

        return redirect()
            ->route('cabinet.security')
            ->with('status', 'GitHub account connected successfully.');
    }

    private function fail(Request $request, string $message): RedirectResponse
    {
        $route = $request->user() ? 'cabinet.security' : 'login';

        return redirect()
            ->route($route)
            ->withErrors(['github' => $message]);
    }

    private function accountLinkingConflictMessage(): string
    {
        return 'We could not connect that GitHub account. Sign in to the correct GitHub account on github.com or use a private browser window, then try again.';
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
