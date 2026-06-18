<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Content-Security-Policy', $this->contentSecurityPolicy());
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=(), fullscreen=(self)');

        return $response;
    }

    private function contentSecurityPolicy(): string
    {
        $directives = config('security.csp.directives');

        if (config('security.csp.allow_vite_dev_server')) {
            $directives['script-src'] = array_merge($directives['script-src'], config('security.csp.vite.script_src'));
            $directives['style-src'] = array_merge($directives['style-src'], config('security.csp.vite.style_src'));
            $directives['connect-src'] = array_merge($directives['connect-src'], config('security.csp.vite.connect_src'));
        }

        return collect($directives)
            ->map(function (array $values, string $directive): string {
                return $values === [] ? $directive : $directive.' '.implode(' ', array_unique($values));
            })
            ->implode('; ');
    }
}
