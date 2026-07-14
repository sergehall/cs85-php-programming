<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthorizationMatrixTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    #[DataProvider('cabinetRoutes')]
    public function test_guests_are_redirected_from_all_cabinet_routes(string $path): void
    {
        $this->get($path)->assertRedirect('/login');
    }

    #[DataProvider('adminRoutes')]
    public function test_standard_users_cannot_access_admin_routes(string $path): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get($path)->assertForbidden();
    }

    #[DataProvider('adminRoutes')]
    public function test_admin_users_can_access_admin_routes(string $path): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get($path)->assertOk();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function cabinetRoutes(): array
    {
        return [
            'dashboard' => ['/cabinet'],
            'profile' => ['/cabinet/profile'],
            'coursework' => ['/cabinet/coursework'],
            'security' => ['/cabinet/security'],
            'activity' => ['/cabinet/activity'],
            'ai' => ['/cabinet/ai'],
            'admin-dashboard' => ['/cabinet/admin'],
            'admin-users' => ['/cabinet/admin/users'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function adminRoutes(): array
    {
        return [
            'dashboard' => ['/cabinet/admin'],
            'users' => ['/cabinet/admin/users'],
        ];
    }
}
