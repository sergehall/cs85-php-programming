<?php

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class Module8bInventoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        config()->set('database.connections.module8b', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);

        DB::purge('module8b');

        $this->artisan('migrate', [
            '--database' => 'module8b',
            '--path' => 'database/migrations/module8b',
        ])->assertExitCode(0);

        Item::query()->create([
            'item_name' => 'Notebook',
            'category' => 'Stationery',
            'quantity' => 10,
            'purchase_date' => '2024-07-01',
        ]);
        Item::query()->create([
            'item_name' => 'Wireless Mouse',
            'category' => 'Electronics',
            'quantity' => 2,
            'purchase_date' => '2024-07-10',
        ]);
        Item::query()->create([
            'item_name' => 'Drawing Markers',
            'category' => 'Art Supplies',
            'quantity' => 12,
            'purchase_date' => '2025-04-28',
        ]);
        Item::query()->create([
            'item_name' => 'Coffee Grinder',
            'category' => 'Kitchen',
            'quantity' => 1,
            'purchase_date' => '2025-05-10',
        ]);
    }

    public function test_inventory_page_displays_eloquent_items_at_the_required_url(): void
    {
        $this->get('/inventory')
            ->assertOk()
            ->assertSee('Module 8 · Assignment 8B')
            ->assertSee('Inventory rebuilt with Eloquent')
            ->assertSee('Notebook')
            ->assertSee('Wireless Mouse')
            ->assertSee('Stationery')
            ->assertSee('Jul 1, 2024')
            ->assertSee('Thinking in objects instead of rows')
            ->assertSee('App\Models\Item');
    }

    public function test_inventory_search_and_category_filters_use_eloquent_queries(): void
    {
        $this->get('/inventory?search=mouse')
            ->assertOk()
            ->assertSee('Wireless Mouse')
            ->assertDontSee('Notebook');

        $this->get('/inventory?category=Kitchen')
            ->assertOk()
            ->assertSee('Coffee Grinder')
            ->assertDontSee('Wireless Mouse');
    }

    public function test_minimum_quantity_filter_and_sorting_are_allowlisted(): void
    {
        $this->get('/inventory?min_quantity=10&sort=quantity_desc')
            ->assertOk()
            ->assertSeeInOrder(['Drawing Markers', 'Notebook'])
            ->assertDontSee('Wireless Mouse')
            ->assertDontSee('Coffee Grinder');

        $this->get('/inventory?sort=not-a-real-column')
            ->assertOk()
            ->assertSeeInOrder(['Drawing Markers', 'Wireless Mouse', 'Coffee Grinder', 'Notebook']);
    }

    public function test_item_model_has_required_mass_assignment_and_cast_behavior(): void
    {
        $item = Item::query()->where('item_name', 'Notebook')->firstOrFail();

        $this->assertSame('module8b', $item->getConnectionName());
        $this->assertSame(10, $item->quantity);
        $this->assertSame('2024-07-01', $item->purchase_date?->toDateString());
        $this->assertSame(
            ['item_name', 'category', 'quantity', 'purchase_date'],
            $item->getFillable(),
        );
    }

    public function test_module8b_migration_creates_the_required_schema(): void
    {
        $schema = Schema::connection('module8b');

        $this->assertTrue($schema->hasTable('items'));
        $this->assertTrue($schema->hasColumns('items', [
            'id',
            'item_name',
            'category',
            'quantity',
            'purchase_date',
            'created_at',
            'updated_at',
        ]));
    }

    public function test_module8_roadmap_and_assignment_alias_link_to_inventory(): void
    {
        $this->get('/roadmap/module-8')
            ->assertOk()
            ->assertSee('Assignment 8B')
            ->assertSee('Inventory Rebuilt with Laravel Eloquent')
            ->assertSee('/inventory', false);

        $this->get('/assignments/module8b')->assertRedirect('/inventory');
        $this->get('/assignments/module8b/inventory')->assertRedirect('/inventory');
    }

    public function test_required_submission_files_exist(): void
    {
        $this->assertFileExists(base_path('assignments/module8b/.env.example'));
        $this->assertFileExists(base_path('assignments/module8b/README.md'));
        $this->assertFileExists(base_path('assignments/module8b/docs/screenshots/README.md'));
    }
}
