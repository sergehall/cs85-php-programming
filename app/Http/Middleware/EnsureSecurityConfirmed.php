<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\SecurityConfirmationService;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSecurityConfirmed
{
    public function __construct(private readonly SecurityConfirmationService $confirmation) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $user = $request->user();

        if ($user instanceof User && $this->confirmation->isRecent($request, $user)) {
            return $next($request);
        }

        $request->session()->put('url.intended', url()->previous());

        return redirect()->route('security.confirm');
    }
}
