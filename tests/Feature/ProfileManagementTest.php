<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_profile_page_renders_real_editable_fields_without_manual_user_id(): void
    {
        $user = User::factory()->create([
            'name' => 'Serge Hall',
            'first_name' => 'Serge',
            'last_name' => 'Hall',
            'github_profile_url' => 'https://github.com/sergehall',
            'linkedin_profile_url' => 'https://www.linkedin.com/in/sergehall',
            'bio' => 'CS85 student building a Laravel coursework portfolio.',
            'technical_skills' => 'PHP, Laravel, MySQL, Docker',
        ]);

        $response = $this->actingAs($user)->get(route('cabinet.profile'));

        $response->assertOk();
        $response->assertSee('Editable profile fields');
        $response->assertSee('First name');
        $response->assertSee('Last name');
        $response->assertSee('GitHub profile link');
        $response->assertSee('LinkedIn profile link');
        $response->assertSee('Short bio');
        $response->assertSee('Technical skills');
        $response->assertSee('Account summary');
        $response->assertSee('Account UUID');
        $response->assertSee($user->public_uuid);
        $response->assertSee('Serge');
        $response->assertSee('Hall');
        $response->assertSee('https://github.com/sergehall');
        $response->assertSee('https://www.linkedin.com/in/sergehall');
        $response->assertSee('CS85 student building a Laravel coursework portfolio.');
        $response->assertSee('PHP, Laravel, MySQL, Docker');
        $response->assertDontSee('#'.$user->id);
        $response->assertDontSee('generated automatically');
        $response->assertDontSee('User ID');
    }

    public function test_user_public_uuid_is_generated_when_account_is_created(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->public_uuid);
        $this->assertTrue(Str::isUuid($user->public_uuid));
    }

    public function test_authenticated_user_can_update_their_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'student@example.com',
        ]);

        $response = $this->actingAs($user)->put(route('cabinet.profile.update'), [
            'first_name' => 'Siarhei',
            'last_name' => 'Hancharou',
            'github_profile_url' => 'https://github.com/sergehall',
            'linkedin_profile_url' => 'https://www.linkedin.com/in/sergehall',
            'bio' => 'CS85 PHP programming student building a Laravel coursework portfolio.',
            'technical_skills' => 'PHP, Laravel, MySQL, Docker, GitHub',
        ]);

        $response->assertRedirect(route('cabinet.profile'));
        $response->assertSessionHasNoErrors();

        $user->refresh();

        $this->assertSame('Siarhei', $user->first_name);
        $this->assertSame('Hancharou', $user->last_name);
        $this->assertSame('Siarhei Hancharou', $user->name);
        $this->assertSame('https://github.com/sergehall', $user->github_profile_url);
        $this->assertSame('https://www.linkedin.com/in/sergehall', $user->linkedin_profile_url);
        $this->assertSame('CS85 PHP programming student building a Laravel coursework portfolio.', $user->bio);
        $this->assertSame('PHP, Laravel, MySQL, Docker, GitHub', $user->technical_skills);
    }

    public function test_profile_update_validates_portfolio_links(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from(route('cabinet.profile'))->put(route('cabinet.profile.update'), [
            'first_name' => 'Serge',
            'last_name' => 'Hall',
            'github_profile_url' => 'not-a-url',
            'linkedin_profile_url' => 'also-not-a-url',
            'bio' => '',
            'technical_skills' => '',
        ]);

        $response->assertRedirect(route('cabinet.profile'));
        $response->assertSessionHasErrors(['github_profile_url', 'linkedin_profile_url']);
    }
}
