<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BladeSecuritySurfaceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_auth_forms_render_csrf_tokens(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('name="_token"', false);

        $this->get('/register')
            ->assertOk()
            ->assertSee('name="_token"', false);
    }

    public function test_authenticated_layout_renders_logout_csrf_token(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/cabinet')
            ->assertOk()
            ->assertSee('action="'.route('logout').'"', false)
            ->assertSee('name="_token"', false);
    }

    public function test_user_controlled_identity_values_are_escaped_in_cabinet_layout(): void
    {
        $user = User::factory()->create([
            'name' => '<script>alert("name")</script>',
            'email' => 'xss@example.com',
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get('/cabinet');

        $response->assertOk();
        $response->assertDontSee('<script>alert("name")</script>', false);
        $response->assertSee('&lt;script&gt;alert(&quot;name&quot;)&lt;/script&gt;', false);
    }
}
