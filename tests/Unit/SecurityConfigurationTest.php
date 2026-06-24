<?php

namespace Tests\Unit;

use Tests\TestCase;

class SecurityConfigurationTest extends TestCase
{
    public function test_session_configuration_keeps_browser_cookie_safety_invariants(): void
    {
        $this->assertTrue(config('session.http_only'));
        $this->assertContains(config('session.same_site'), ['lax', 'strict']);
        $this->assertSame('json', config('session.serialization'));
    }

    public function test_production_security_policy_does_not_allow_unsafe_script_execution(): void
    {
        config([
            'security.csp.allow_vite_dev_server' => false,
            'security.csp.allow_debug_tooling' => false,
            'security.csp.enforce_https_upgrades' => true,
        ]);

        $directives = config('security.csp.directives');
        $scriptSource = $directives['script-src'];
        $styleSource = $directives['style-src'];

        $this->assertNotContains("'unsafe-inline'", $scriptSource);
        $this->assertNotContains("'unsafe-eval'", $scriptSource);
        $this->assertNotContains("'unsafe-inline'", $styleSource);
        $this->assertArrayHasKey('upgrade-insecure-requests', $directives);
        $this->assertArrayHasKey('block-all-mixed-content', $directives);
    }

    public function test_repository_security_gates_are_wired_into_ci_and_gitignore(): void
    {
        $workflow = file_get_contents(base_path('.github/workflows/ci.yml'));
        $package = file_get_contents(base_path('package.json'));
        $gitignore = file_get_contents(base_path('.gitignore'));

        $this->assertIsString($workflow);
        $this->assertIsString($package);
        $this->assertIsString($gitignore);
        $this->assertStringContainsString('actions/checkout@v5', $workflow);
        $this->assertStringContainsString('actions/setup-node@v5', $workflow);
        $this->assertStringContainsString('npm run quality', $workflow);
        $this->assertStringContainsString('npm run security:ci', $package);
        $this->assertStringContainsString('composer audit --locked', $workflow);
        $this->assertStringContainsString('npm audit --audit-level=moderate', $workflow);
        $this->assertMatchesRegularExpression('/^\.env$/m', $gitignore);
        $this->assertMatchesRegularExpression('/^\/vendor$/m', $gitignore);
        $this->assertMatchesRegularExpression('/^\/node_modules$/m', $gitignore);
    }
}
