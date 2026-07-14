<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\AdminAccessRequest;
use App\Services\QrCodeRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class SecurityController extends Controller
{
    public function __invoke(Request $request, QrCodeRenderer $qrCode): View
    {
        $user = $request->user();
        $githubConfigured = (bool) config('services.github.client_id') && (bool) config('services.github.client_secret');
        $githubConnected = (bool) $user?->github_id;
        $mfaProvisioningUri = $request->session()->get('mfa_setup.provisioning_uri');
        $adminAccessRequest = $user
            ? AdminAccessRequest::query()->where('user_id', $user->getKey())->first()
            : null;
        $activeSessions = $user && config('session.driver') === 'database'
            ? DB::table((string) config('session.table', 'sessions'))
                ->where('user_id', $user->getKey())
                ->orderByDesc('last_activity')
                ->get()
            : collect();

        return view('cabinet.security', [
            'section' => config('cabinet.sections.security'),
            'user' => $user,
            'adminAccessRequest' => $adminAccessRequest,
            'mfaSetup' => [
                'secret' => $request->session()->get('mfa_setup.secret'),
                'provisioning_uri' => $mfaProvisioningUri,
            ],
            'mfaQrCode' => is_string($mfaProvisioningUri) ? $qrCode->dataUri($mfaProvisioningUri) : null,
            'mfaRecoveryCodes' => $request->session()->get('mfa_recovery_codes', []),
            'githubConfigured' => $githubConfigured,
            'githubConnected' => $githubConnected,
            'githubRedirectRouteReady' => Route::has('auth.github.redirect'),
            'activeSessions' => $activeSessions,
            'currentSessionId' => $request->session()->getId(),
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
                    'status' => $user?->hasMfaEnabled() ? 'Enabled' : 'Available',
                    'tone' => $user?->hasMfaEnabled() ? 'success' : 'warning',
                    'detail' => $user?->hasMfaEnabled()
                        ? 'Authenticator app MFA is enabled for this account.'
                        : 'Authenticator app MFA can be enabled from this security page.',
                ],
            ],
        ]);
    }
}
