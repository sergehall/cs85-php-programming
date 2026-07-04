<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_public_pages_include_security_headers(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->assertHeader('Cross-Origin-Resource-Policy', 'same-origin');
        $response->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');
    }

    public function test_content_security_policy_matches_strict_mdn_observatory_expectations(): void
    {
        $response = $this->get('/');
        $policy = $response->headers->get('Content-Security-Policy');

        $this->assertIsString($policy);
        $this->assertStringContainsString("default-src 'none'", $policy);
        $this->assertStringContainsString("script-src 'self'", $policy);
        $this->assertStringContainsString("style-src 'self'", $policy);
        $this->assertStringContainsString("img-src 'self' data: https:", $policy);
        $this->assertStringContainsString("object-src 'none'", $policy);
        $this->assertStringContainsString("frame-ancestors 'none'", $policy);
        $this->assertStringContainsString("base-uri 'none'", $policy);
        $this->assertStringContainsString("form-action 'self'", $policy);
        $this->assertStringContainsString('upgrade-insecure-requests', $policy);
        $this->assertStringContainsString('block-all-mixed-content', $policy);
        $this->assertStringNotContainsString("'unsafe-inline'", $policy);
        $this->assertStringNotContainsString("'unsafe-eval'", $policy);
        $this->assertStringNotContainsString('http:', $policy);
    }

    public function test_local_development_policy_does_not_force_https_for_vite(): void
    {
        config([
            'security.headers.hsts.enabled' => false,
            'security.csp.allow_vite_dev_server' => true,
            'security.csp.allow_debug_tooling' => true,
            'security.csp.enforce_https_upgrades' => false,
        ]);

        $response = $this->get('/');
        $policy = $response->headers->get('Content-Security-Policy');

        $this->assertIsString($policy);
        $response->assertHeaderMissing('Strict-Transport-Security');
        $this->assertStringContainsString('http://127.0.0.1:5173', $policy);
        $this->assertStringContainsString('ws://127.0.0.1:5173', $policy);
        $this->assertStringContainsString("'unsafe-inline'", $policy);
        $this->assertStringNotContainsString('upgrade-insecure-requests', $policy);
        $this->assertStringNotContainsString('block-all-mixed-content', $policy);
    }
}
