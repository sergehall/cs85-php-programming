<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\User;
use App\Services\Modules\Module9A\Module9aWriteAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class Module9aContactListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_module_9_roadmap_links_to_the_contact_list_workbench(): void
    {
        $this->get(route('roadmap.module', 'module-9'))
            ->assertOk()
            ->assertSee('Assignment 9A')
            ->assertSee('Contact List App')
            ->assertSee(route('assignments.module9a.contacts.index'), false);

        $this->get('/assignments/module9a')
            ->assertRedirect('/assignments/module9a/contacts');
    }

    public function test_workbench_renders_default_json_and_empty_crud_controls(): void
    {
        $this->get(route('assignments.module9a.contacts.index'))
            ->assertOk()
            ->assertSee('Module 9 · Assignment 9A')
            ->assertSee('Contact List CRUD workbench')
            ->assertSee('POST · Import JSON')
            ->assertSee('GET · Run query')
            ->assertSee('POST · Create contact')
            ->assertSee('DELETE · Remove by ID')
            ->assertSee('maya.chen@example.com')
            ->assertSee('No contacts found');
    }

    public function test_default_json_import_is_idempotent_and_builds_relationships(): void
    {
        $this->post(route('assignments.module9a.contacts.dataset.store'))
            ->assertRedirect(route('assignments.module9a.contacts.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseCount('contact_groups', 4);
        $this->assertDatabaseCount('contacts', 8);
        $this->assertDatabaseHas('contacts', [
            'email' => 'maya.chen@example.com',
            'role' => Contact::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $maya = Contact::query()->where('email', 'maya.chen@example.com')->firstOrFail();
        $this->assertSame('Work', $maya->group?->name);

        $this->post(route('assignments.module9a.contacts.dataset.store'))
            ->assertRedirect(route('assignments.module9a.contacts.index'));

        $this->assertDatabaseCount('contact_groups', 4);
        $this->assertDatabaseCount('contacts', 8);
    }

    public function test_get_filters_search_each_requested_contact_field(): void
    {
        $this->post(route('assignments.module9a.contacts.dataset.store'));
        $work = ContactGroup::query()->where('name', 'Work')->firstOrFail();

        $this->get(route('assignments.module9a.contacts.index', ['first_name' => 'Maya']))
            ->assertOk()
            ->assertSee('Maya Chen')
            ->assertDontSee('Noah Williams');

        $this->get(route('assignments.module9a.contacts.index', ['last_name' => 'Nguyen']))
            ->assertOk()
            ->assertSee('Liam Nguyen')
            ->assertDontSee('Maya Chen');

        $this->get(route('assignments.module9a.contacts.index', ['email' => 'sofia.ramirez']))
            ->assertOk()
            ->assertSee('Sofia Ramirez')
            ->assertDontSee('Maya Chen');

        $this->get(route('assignments.module9a.contacts.index', ['phone' => '0104']))
            ->assertOk()
            ->assertSee('Ethan Brooks')
            ->assertDontSee('Maya Chen');

        $this->get(route('assignments.module9a.contacts.index', [
            'group_id' => $work->getKey(),
            'role' => Contact::ROLE_ADMIN,
            'status' => 'active',
        ]))
            ->assertOk()
            ->assertSee('Maya Chen')
            ->assertDontSee('Ethan Brooks');
    }

    public function test_raw_get_endpoint_returns_filtered_json(): void
    {
        $this->post(route('assignments.module9a.contacts.dataset.store'));

        $this->getJson(route('assignments.module9a.contacts.data', [
            'role' => Contact::ROLE_ADMIN,
            'status' => 'active',
        ]))
            ->assertOk()
            ->assertJsonPath('count', 2)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.role', Contact::ROLE_ADMIN);
    }

    public function test_create_validates_and_inserts_a_contact(): void
    {
        $group = ContactGroup::query()->create(['name' => 'Clients']);

        $this->post(route('assignments.module9a.contacts.store'), [
            'first_name' => '',
            'last_name' => '',
            'email' => 'not-an-email',
            'role' => 'owner',
        ])->assertSessionHasErrors(['first_name', 'last_name', 'email', 'role']);

        $response = $this->post(route('assignments.module9a.contacts.store'), [
            'first_name' => 'Jordan',
            'last_name' => 'Lee',
            'email' => ' JORDAN.LEE@EXAMPLE.COM ',
            'phone' => '+1-310-555-0199',
            'company' => 'Example Studio',
            'contact_group_id' => $group->getKey(),
            'role' => Contact::ROLE_USER,
            'is_active' => '1',
            'notes' => 'Created from the Module 9 UI.',
        ]);

        $contact = Contact::query()->where('email', 'jordan.lee@example.com')->firstOrFail();

        $response->assertRedirect(route('assignments.module9a.contacts.index', ['edit' => $contact->getKey()]));
        $this->assertSame('Clients', $contact->group?->name);
        $this->assertTrue($contact->is_active);
    }

    public function test_update_uses_route_model_binding_and_unique_validation(): void
    {
        $contact = Contact::query()->create([
            'first_name' => 'Before',
            'last_name' => 'Update',
            'email' => 'before@example.com',
            'role' => Contact::ROLE_USER,
            'is_active' => true,
        ]);

        $this->put(route('assignments.module9a.contacts.update', $contact), [
            'first_name' => 'After',
            'last_name' => 'Update',
            'email' => 'after@example.com',
            'phone' => '+1-310-555-0188',
            'company' => 'Updated Company',
            'role' => Contact::ROLE_ADMIN,
            'notes' => 'Updated from the PUT form.',
        ])->assertRedirect(route('assignments.module9a.contacts.index', ['edit' => $contact->getKey()]));

        $contact->refresh();
        $this->assertSame('After', $contact->first_name);
        $this->assertSame(Contact::ROLE_ADMIN, $contact->role);
        $this->assertFalse($contact->is_active);
    }

    public function test_delete_routes_remove_only_the_selected_contact(): void
    {
        $first = Contact::query()->create([
            'first_name' => 'Delete',
            'last_name' => 'One',
            'email' => 'delete.one@example.com',
            'role' => Contact::ROLE_USER,
            'is_active' => true,
        ]);
        $second = Contact::query()->create([
            'first_name' => 'Keep',
            'last_name' => 'Two',
            'email' => 'keep.two@example.com',
            'role' => Contact::ROLE_USER,
            'is_active' => true,
        ]);

        $this->delete(route('assignments.module9a.contacts.destroy', $first))
            ->assertRedirect(route('assignments.module9a.contacts.index'));

        $this->assertDatabaseMissing('contacts', ['id' => $first->getKey()]);
        $this->assertDatabaseHas('contacts', ['id' => $second->getKey()]);

        $this->delete(route('assignments.module9a.contacts.destroy-by-id'), [
            'contact_id' => $second->getKey(),
        ])->assertRedirect(route('assignments.module9a.contacts.index'));

        $this->assertDatabaseCount('contacts', 0);
    }

    public function test_dataset_reset_clears_only_module_9_tables(): void
    {
        User::factory()->create(['email' => 'real-login@example.com']);
        $this->post(route('assignments.module9a.contacts.dataset.store'));

        $this->delete(route('assignments.module9a.contacts.dataset.destroy'))
            ->assertRedirect(route('assignments.module9a.contacts.index'));

        $this->assertDatabaseCount('contacts', 0);
        $this->assertDatabaseCount('contact_groups', 0);
        $this->assertDatabaseHas('users', ['email' => 'real-login@example.com']);
    }

    public function test_module_9_migration_creates_the_expected_schema(): void
    {
        $this->assertTrue(Schema::hasTable('contact_groups'));
        $this->assertTrue(Schema::hasTable('contacts'));
        $this->assertTrue(Schema::hasColumns('contacts', [
            'id',
            'contact_group_id',
            'first_name',
            'last_name',
            'email',
            'phone',
            'company',
            'role',
            'is_active',
            'notes',
            'created_at',
            'updated_at',
        ]));
    }

    public function test_production_write_policy_requires_an_application_admin(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');
        $access = app(Module9aWriteAccess::class);
        $standardUser = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertFalse($access->allows(null));
        $this->assertFalse($access->allows($standardUser));
        $this->assertTrue($access->allows($admin));
    }
}
