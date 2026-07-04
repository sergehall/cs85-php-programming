<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\AdminAccessRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class SecurityController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $githubConfigured = (bool) config('services.github.client_id') && (bool) config('services.github.client_secret');
        $githubConnected = (bool) $user?->github_id;
        $adminAccessRequest = $user
            ? AdminAccessRequest::query()->where('user_id', $user->getKey())->first()
            : null;

        return view('cabinet.security', [
            'section' => config('cabinet.sections.security'),
            'user' => $user,
            'adminAccessRequest' => $adminAccessRequest,
            'githubConfigured' => $githubConfigured,
            'githubConnected' => $githubConnected,
            'githubRedirectRouteReady' => Route::has('auth.github.redirect'),
            'checks' => [
                [
                    'label' => 'Session authentication',
                    'status' => 'Active',
                    'tone' => 'success',
                    'detail' => 'Cabinet routes require an authenticated Laravel session.',
                ],
                [
                    'label' => 'Role boundary',
                    'status' => $user?->isAdmin() ? 'Admin' : 'User',
                    'tone' => 'success',
                    'detail' => 'Admin routes are protected by the admin middleware and standard users receive 403 responses.',
                ],
                [
                    'label' => 'CSRF forms',
                    'status' => 'Active',
                    'tone' => 'success',
                    'detail' => 'Profile, login, registration, and logout forms use Laravel CSRF protection.',
                ],
                [
                    'label' => 'Security headers',
                    'status' => 'Active',
                    'tone' => 'success',
                    'detail' => 'The web middleware applies CSP, frame protection, and related response headers.',
                ],
                [
                    'label' => 'GitHub OAuth configuration',
                    'status' => $githubConfigured ? 'Configured' : 'Needs env',
                    'tone' => $githubConfigured ? 'success' : 'warning',
                    'detail' => $githubConfigured
                        ? 'GitHub client credentials are available through config/services.php.'
                        : 'Set GITHUB_CLIENT_ID, GITHUB_CLIENT_SECRET, and GITHUB_REDIRECT_URI in the local environment.',
                ],
                [
                    'label' => 'GitHub account connection',
                    'status' => $githubConnected ? 'Connected' : 'Not connected',
                    'tone' => $githubConnected ? 'success' : 'warning',
                    'detail' => $githubConnected
                        ? 'This user can sign in through the connected GitHub identity.'
                        : 'Connect GitHub to use the external identity provider from this account.',
                ],
                [
                    'label' => 'Application MFA',
                    'status' => 'Planned',
                    'tone' => 'neutral',
                    'detail' => 'App-level authenticator codes and recovery codes are planned as a later database-backed feature.',
                ],
                [
                    'label' => 'GitHub MFA visibility',
                    'status' => 'Managed by GitHub',
                    'tone' => 'neutral',
                    'detail' => 'GitHub does not expose a personal 2FA-enabled flag to this OAuth app; users manage GitHub 2FA in GitHub account settings.',
                ],
            ],
        ]);
    }
}
