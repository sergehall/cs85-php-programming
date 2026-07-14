<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthSessionService;
use App\Services\SecurityAuditLogger;
use App\Services\SecurityConfirmationService;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GitHubOAuthController extends Controller
{
    public function redirect(Request $request, SecurityConfirmationService $confirmation): RedirectResponse
    {
        $failureRoute = $request->user() ? 'cabinet.security' : 'login';

        if (! config('services.github.client_id') || ! config('services.github.client_secret')) {
            return redirect()
                ->route($failureRoute)
                ->withErrors(['github' => 'GitHub OAuth is not configured yet.']);
        }

        $user = $request->user();
        $purpose = $request->query('purpose') === 'step_up' ? 'step_up' : 'sign_in_or_link';

        if ($user instanceof User && $purpose === 'sign_in_or_link' && ! $confirmation->isRecent($request, $user)) {
            $request->session()->put('url.intended', $request->fullUrl());

            return redirect()->route('security.confirm');
        }

        if ($user instanceof User
            && $purpose === 'step_up'
            && (! $user->github_id || $user->password_login_enabled || $user->hasMfaEnabled())) {
            return redirect()->route('security.confirm')->withErrors([
                'github' => 'Use the strongest confirmation method available for this account.',
            ]);
        }

        $state = Str::random(40);
        $request->session()->put('oauth.github_state', $state);
        $request->session()->put('oauth.github_purpose', $purpose);

        if ($user instanceof User) {
            $request->session()->put('oauth.github_link_user_id', $user->getKey());
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

    public function callback(
        Request $request,
        SecurityAuditLogger $audit,
        SecurityConfirmationService $confirmation,
        AuthSessionService $sessions,
    ): RedirectResponse {
        $expectedState = $request->session()->pull('oauth.github_state');

        if (! $expectedState || ! hash_equals($expectedState, (string) $request->query('state'))) {
            return $this->fail($request, $audit, 'invalid_state', 'GitHub sign-in state could not be verified.');
        }

        if ($request->filled('error')) {
            return $this->fail($request, $audit, 'provider_denied', 'GitHub sign-in was cancelled or denied.');
        }

        $linkUserId = $request->session()->pull('oauth.github_link_user_id');
        $purpose = $request->session()->pull('oauth.github_purpose', 'sign_in_or_link');

        $tokenResponse = Http::asForm()
            ->acceptJson()
            ->connectTimeout(5)
            ->timeout(10)
            ->post('https://github.com/login/oauth/access_token', [
                'client_id' => config('services.github.client_id'),
                'client_secret' => config('services.github.client_secret'),
                'code' => $request->query('code'),
                'redirect_uri' => config('services.github.redirect'),
            ]);

        if ($tokenResponse->failed() || ! $tokenResponse->json('access_token')) {
            return $this->fail($request, $audit, 'token_exchange_failed', 'GitHub token exchange failed.');
        }

        $token = (string) $tokenResponse->json('access_token');
        $github = $this->githubClient($token);
        $profileResponse = $github->get('https://api.github.com/user');

        if ($profileResponse->failed()) {
            return $this->fail($request, $audit, 'profile_fetch_failed', 'GitHub profile could not be loaded.');
        }

        $profile = $profileResponse->json();
        $githubId = is_array($profile) && is_numeric($profile['id'] ?? null) ? (string) $profile['id'] : null;
        $githubUsername = is_array($profile) && is_string($profile['login'] ?? null) ? $profile['login'] : null;
        $email = $this->primaryEmail($github);

        if (! $githubId || ! $githubUsername || ! $email) {
            return $this->fail($request, $audit, 'invalid_identity', 'GitHub did not return a usable verified identity.');
        }

        $email = User::normalizeEmail($email);

        $userData = [
            'name' => is_string($profile['name'] ?? null) && $profile['name'] !== '' ? $profile['name'] : $githubUsername,
            'email' => $email,
            'github_id' => $githubId,
            'github_username' => $githubUsername,
            'github_avatar_url' => $profile['avatar_url'] ?? null,
            'email_verified_at' => now(),
        ];

        if ($purpose === 'step_up') {
            $currentUser = $request->user();

            if (! $currentUser instanceof User
                || $linkUserId !== $currentUser->getKey()
                || ! is_string($currentUser->github_id)
                || ! hash_equals($currentUser->github_id, $githubId)) {
                return $this->fail($request, $audit, 'step_up_identity_mismatch', 'GitHub could not confirm this account.');
            }

            $confirmation->confirm($request, $currentUser, 'github');
            $audit->record(
                request: $request,
                event: 'security.step_up_succeeded',
                outcome: 'success',
                title: 'Security confirmation completed',
                subject: $currentUser,
                actor: $currentUser,
                description: 'Recent authentication was confirmed through GitHub.',
                metadata: ['method' => 'github'],
            );

            return redirect()->intended(route('cabinet.security'));
        }

        if ($request->user() instanceof User
            && $linkUserId === $request->user()->getKey()
            && ! $confirmation->isRecent($request, $request->user())) {
            return $this->fail(
                $request,
                $audit,
                'step_up_required',
                'Recent security confirmation is required before connecting GitHub.',
                $request->user(),
            );
        }

        if ($request->user() && $linkUserId === $request->user()->getKey()) {
            return $this->linkCurrentUser($request, $userData['github_id'], $email, $userData, $audit, $sessions);
        }

        $githubUser = User::query()
            ->where('github_id', $userData['github_id'])
            ->first();
        $emailUser = User::query()
            ->where('email', $email)
            ->first();

        if ($githubUser && $emailUser && ! $githubUser->is($emailUser)) {
            return $this->fail($request, $audit, 'identity_conflict', $this->accountLinkingConflictMessage());
        }

        if (! $githubUser && $emailUser) {
            return $this->fail($request, $audit, 'explicit_link_required', $this->accountLinkingConflictMessage());
        }

        $user = $githubUser;

        if ($user && ! $user->canLogIn()) {
            return $this->fail($request, $audit, 'login_disabled', 'This account is not allowed to sign in right now. Contact an administrator.', $user);
        }

        $shouldLogGithubConnection = ! $user || ! $user->github_id;

        if ($user) {
            $user->forceFill([
                'github_username' => $userData['github_username'],
                'github_avatar_url' => $userData['github_avatar_url'],
            ])->save();
        } else {
            $user = User::query()->create($userData + [
                'password' => Str::password(48),
                'password_login_enabled' => false,
                'role' => 'user',
            ]);
        }

        if ($shouldLogGithubConnection) {
            $audit->record(
                request: $request,
                event: 'security.github_connected',
                outcome: 'success',
                title: 'GitHub connected',
                subject: $user,
                actor: $user,
                description: 'GitHub OAuth was connected as an external identity provider.',
                metadata: ['provider' => 'github'],
            );
        }

        if ($user->hasMfaEnabled()) {
            $request->session()->put('auth.mfa.user_id', $user->getKey());
            $request->session()->put('auth.mfa.remember', true);
            $request->session()->put('auth.mfa.started_at', now()->getTimestamp());

            return redirect()->route('mfa.challenge');
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        $audit->record(
            request: $request,
            event: 'auth.login_succeeded',
            outcome: 'success',
            title: 'GitHub sign-in completed',
            subject: $user,
            actor: $user,
            description: 'A GitHub OAuth sign-in completed successfully.',
            metadata: ['provider' => 'github', 'remembered' => true],
        );

        return redirect()->intended(route('cabinet.dashboard'));
    }

    /**
     * @param  array{name:mixed,email:string,github_id:string,github_username:mixed,github_avatar_url:mixed,email_verified_at:mixed}  $userData
     */
    private function linkCurrentUser(
        Request $request,
        string $githubId,
        string $email,
        array $userData,
        SecurityAuditLogger $audit,
        AuthSessionService $sessions,
    ): RedirectResponse {
        $currentUser = $request->user();

        if (! $currentUser instanceof User) {
            return $this->fail($request, $audit, 'authentication_required', 'You must be signed in before connecting GitHub.');
        }

        $githubOwner = User::query()
            ->where('github_id', $githubId)
            ->whereKeyNot($currentUser->getKey())
            ->first();

        if ($githubOwner) {
            return $this->fail($request, $audit, 'identity_conflict', $this->accountLinkingConflictMessage(), $currentUser);
        }

        $emailOwner = User::query()
            ->where('email', $email)
            ->whereKeyNot($currentUser->getKey())
            ->first();

        if ($emailOwner) {
            return $this->fail($request, $audit, 'email_conflict', $this->accountLinkingConflictMessage(), $currentUser);
        }

        $currentUser->forceFill([
            'github_id' => $userData['github_id'],
            'github_username' => $userData['github_username'],
            'github_avatar_url' => $userData['github_avatar_url'],
            'email_verified_at' => $currentUser->email === $email ? now() : $currentUser->email_verified_at,
        ])->save();

        $sessions->revokeOtherSessions($request, $currentUser);

        $audit->record(
            request: $request,
            event: 'security.github_connected',
            outcome: 'success',
            title: 'GitHub connected',
            subject: $currentUser,
            actor: $currentUser,
            description: 'GitHub OAuth was connected as an external identity provider.',
            metadata: ['provider' => 'github'],
        );

        $request->session()->regenerate();

        return redirect()
            ->route('cabinet.security')
            ->with('status', 'GitHub account connected successfully.');
    }

    private function fail(
        Request $request,
        SecurityAuditLogger $audit,
        string $reason,
        string $message,
        ?User $subject = null,
    ): RedirectResponse {
        $audit->record(
            request: $request,
            event: 'auth.oauth_failed',
            outcome: 'failure',
            title: 'GitHub authentication failed',
            subject: $subject,
            actor: $request->user() instanceof User ? $request->user() : null,
            description: 'A GitHub authentication or account-linking operation did not complete.',
            metadata: ['provider' => 'github', 'reason' => $reason],
        );

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
            ->connectTimeout(5)
            ->timeout(10)
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
