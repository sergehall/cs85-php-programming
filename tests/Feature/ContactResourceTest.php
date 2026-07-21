<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ContactResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_resource_pages_use_separate_index_create_and_edit_views(): void
    {
        $contact = Contact::query()->create([
            'name' => 'Maya Chen',
            'email' => 'maya@example.com',
            'phone' => '+1-310-555-0101',
        ]);

        $this->get(route('contacts.index'))
            ->assertOk()
            ->assertViewIs('contacts.index')
            ->assertSee('Contact List App')
            ->assertSee('Maya Chen');

        $this->get(route('contacts.create'))
            ->assertOk()
            ->assertViewIs('contacts.create')
            ->assertSee('Add contact');

        $this->get(route('contacts.edit', $contact))
            ->assertOk()
            ->assertViewIs('contacts.edit')
            ->assertSee('Edit contact')
            ->assertSee('value="Maya Chen"', false);
    }

    public function test_store_requires_name_email_and_phone(): void
    {
        $this->post(route('contacts.store'), [])
            ->assertSessionHasErrors(['name', 'email', 'phone']);

        $this->post(route('contacts.store'), [
            'name' => ' Jordan Lee ',
            'email' => ' JORDAN.LEE@EXAMPLE.COM ',
            'phone' => ' +1-310-555-0199 ',
        ])->assertRedirect(route('contacts.index'));

        $this->assertDatabaseHas('contacts', [
            'name' => 'Jordan Lee',
            'first_name' => 'Jordan',
            'last_name' => 'Lee',
            'email' => 'jordan.lee@example.com',
            'phone' => '+1-310-555-0199',
        ]);
    }

    public function test_update_and_delete_complete_the_resource_crud_flow(): void
    {
        $contact = Contact::query()->create([
            'name' => 'Before Name',
            'email' => 'before@example.com',
            'phone' => '+1-310-555-0102',
        ]);

        $this->put(route('contacts.update', $contact), [
            'name' => 'After Name',
            'email' => 'after@example.com',
            'phone' => '+1-424-555-0199',
        ])->assertRedirect(route('contacts.index'));

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->getKey(),
            'name' => 'After Name',
            'first_name' => 'After',
            'last_name' => 'Name',
            'email' => 'after@example.com',
            'phone' => '+1-424-555-0199',
        ]);

        $this->delete(route('contacts.destroy', $contact))
            ->assertRedirect(route('contacts.index'));

        $this->assertDatabaseMissing('contacts', ['id' => $contact->getKey()]);
    }

    public function test_name_and_phone_columns_are_required_by_the_database_schema(): void
    {
        $columns = collect(Schema::getColumns('contacts'))->keyBy('name');

        $this->assertFalse((bool) $columns->get('name')['nullable']);
        $this->assertFalse((bool) $columns->get('phone')['nullable']);
    }
}
