<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class Module8aDatabaseEnvironmentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_database_environment_page_documents_the_required_workflow(): void
    {
        $this->get('/assignments/module8a/database-environment.php')
            ->assertOk()
            ->assertSee('Module 8 · Assignment 8A')
            ->assertSee('Laravel with a database environment')
            ->assertSee('orm_practice_db')
            ->assertSee('MODULE8A_DB_PORT=3307')
            ->assertSee('php artisan migrate')
            ->assertSee('Run connection query')
            ->assertSee('SELECT')
            ->assertSee('information_schema.tables')
            ->assertSee('placeholder="3307"', false)
            ->assertSee('placeholder="module8a"', false)
            ->assertSee('placeholder="orm_practice_db"', false)
            ->assertSee('placeholder="Uses local .env when blank"', false);
    }

    public function test_connection_form_rejects_remote_database_hosts(): void
    {
        $password = 'must-not-appear-in-response';

        $response = $this->from('/assignments/module8a/database-environment.php')
            ->post('/assignments/module8a/database-environment.php', [
                'host' => 'database.example.com',
                'port' => '3307',
                'database' => 'orm_practice_db',
                'username' => 'module8a',
                'password' => $password,
            ]);

        $response
            ->assertRedirect('/assignments/module8a/database-environment.php')
            ->assertSessionHasErrors('host');

        $this->get('/assignments/module8a/database-environment.php')
            ->assertDontSee($password);
    }

    public function test_connection_form_validates_database_identifiers_and_port(): void
    {
        $this->from('/assignments/module8a/database-environment.php')
            ->post('/assignments/module8a/database-environment.php', [
                'host' => '127.0.0.1',
                'port' => '70000',
                'database' => 'orm practice; DROP DATABASE mysql;',
                'username' => 'module8a',
                'password' => '',
            ])
            ->assertRedirect('/assignments/module8a/database-environment.php')
            ->assertSessionHasErrors(['port', 'database']);
    }

    public function test_failed_local_connection_returns_safe_feedback_without_echoing_password(): void
    {
        $password = 'local-secret-that-must-stay-private';

        $this->post('/assignments/module8a/database-environment.php', [
            'host' => '127.0.0.1',
            'port' => '1',
            'database' => 'orm_practice_db',
            'username' => 'module8a',
            'password' => $password,
        ])
            ->assertOk()
            ->assertSee('Connection needs attention')
            ->assertSee('The submitted password was not stored')
            ->assertDontSee($password);
    }

    public function test_module_8_roadmap_links_to_the_interactive_assignment(): void
    {
        $this->get('/roadmap/module-8')
            ->assertOk()
            ->assertSee('Assignment 8A')
            ->assertSee('Laravel with a Database Environment')
            ->assertSee('/assignments/module8a/database-environment.php', false);

        $this->assertNotNull(Route::getRoutes()->getByName('assignments.module8a.database.show'));
        $this->assertNotNull(Route::getRoutes()->getByName('assignments.module8a.database.test'));
    }

    public function test_required_submission_files_exist(): void
    {
        $this->assertFileExists(base_path('assignments/module8a/.env.example'));
        $this->assertFileExists(base_path('assignments/module8a/README.md'));
        $this->assertFileExists(base_path('assignments/module8a/docs/screenshots/README.md'));
    }
}
