# Module 8 Assignment 8B: Rebuild Your Inventory with Laravel Eloquent

Course: CS 85 - PHP Programming  
Student: Siarhei Hancharou

## Objective

This assignment rebuilds the personal inventory from Module 4B with Laravel migrations and Eloquent. The original page used raw PDO and manually written SQL; Module 8B uses an `Item` model, `InventoryController`, Blade, and a migration-managed MySQL table.

## Project Structure

Module 8B is implemented as an isolated feature of the existing CS85 Laravel application instead of generating a second nested framework project. It uses the root `artisan`, Composer dependencies, routes, and layout while keeping its assignment documentation in `assignments/module8b`.

The required test URL is unchanged:

```text
http://localhost:8000/inventory
```

Important files:

- `database/migrations/module8b/2026_07_14_000001_create_items_table.php`
- `app/Models/Item.php`
- `app/Http/Controllers/Assignments/Module8bInventoryController.php`
- `resources/views/assignments/module8b/inventory.blade.php`
- `routes/web.php`

## Original Tutorial Reference

The standalone tutorial begins with:

```bash
laravel new inventory_eloquent
cd inventory_eloquent
code .
```

Because this repository is already a complete Laravel project, those commands were not repeated. The same migration, model, controller, Blade, Tinker, and MySQL learning objectives are implemented in the existing application.

## Database Configuration

The tutorial uses MySQL on port `3306` with the root account. This repository already runs the healthy `cs85-mysql` Docker container on host port `3307`, so Module 8B uses a named and least-privilege connection:

```dotenv
MODULE8B_DB_HOST=127.0.0.1
MODULE8B_DB_PORT=3307
MODULE8B_DB_DATABASE=inventory_db
MODULE8B_DB_USERNAME=module8b
MODULE8B_DB_PASSWORD=replace_with_local_password
```

The real password is stored only in the ignored root `.env`. The committed `assignments/module8b/.env.example` contains a placeholder.

Start MySQL and clear cached configuration:

```bash
docker compose up -d mysql
php artisan config:clear
```

## Migration

The migration creates the required `items` table:

```php
Schema::connection('module8b')->create('items', function (Blueprint $table): void {
    $table->id();
    $table->string('item_name');
    $table->string('category')->nullable();
    $table->integer('quantity')->default(0);
    $table->date('purchase_date')->nullable();
    $table->timestamps();
});
```

Run only the Module 8B migration path against `inventory_db`:

```bash
php artisan migrate --database=module8b --path=database/migrations/module8b
php artisan migrate:status --database=module8b --path=database/migrations/module8b
```

Keeping the migration in its own folder prevents unrelated coursework migrations from being applied to `inventory_db`.

## Eloquent Model

`App\Models\Item` maps PHP objects to rows in the `items` table. Its `$fillable` list allows the four assignment fields to be mass-assigned, while casts convert `quantity` to an integer and `purchase_date` to a date object.

## Required Tinker Data

Open Tinker from the project root:

```bash
php artisan tinker
```

Insert the two required records:

```php
App\Models\Item::create([
    'item_name' => 'Notebook',
    'category' => 'Stationery',
    'quantity' => 10,
    'purchase_date' => '2024-07-01',
]);

App\Models\Item::create([
    'item_name' => 'Wireless Mouse',
    'category' => 'Electronics',
    'quantity' => 2,
    'purchase_date' => '2024-07-10',
]);
```

The six personalized Module 4B records are also restored so the old PDO page and new Eloquent page display the same inventory history.

## Controller and Blade

`Module8bInventoryController@index` starts with `Item::query()` and retrieves records as Eloquent objects. It also recreates the useful Module 4B interactivity with Eloquent methods:

- search by item or category;
- filter by category;
- filter by minimum quantity;
- sort by category, name, quantity, or purchase date.

The Blade template renders each object with escaped `{{ }}` output and formats the cast purchase date without using raw PDO or SQL in the view.

## Run the Project

```bash
php artisan serve
```

Open:

```text
http://localhost:8000/inventory
```

The integrated roadmap also links to this page from Module 8.

## Reflection

Eloquent simplified how I interacted with the database because each inventory record became an `Item` object with readable properties. I wrote less connection and query code, and the controller could focus on preparing data for the Blade view instead of manually creating PDO statements.

Migrations also changed my workflow by keeping the table structure in PHP and Git. Compared with the raw PDO version from Module 4B, the Eloquent version is easier to recreate, test, and extend as the inventory grows.

## Troubleshooting

### MySQL is not reachable

Run `docker compose up -d mysql` and verify that `cs85-mysql` is healthy. Use host port `3307`; port `3306` is internal to the container.

### Access denied

Confirm `MODULE8B_DB_USERNAME` and `MODULE8B_DB_PASSWORD` in the ignored root `.env`, then run `php artisan config:clear`.

### Unknown database

Confirm that `inventory_db` exists in MySQL. It can be inspected through Adminer at `http://127.0.0.1:8081`.

### Table already exists

Check migration status before running the migration again:

```bash
php artisan migrate:status --database=module8b --path=database/migrations/module8b
```

Use `migrate:fresh` only on an isolated assignment database and only when deleting all its records is intentional.

### Inventory page is empty

Insert the required records with Tinker and confirm they use `App\Models\Item`, which is configured for the `module8b` connection.

## Testing

Run the focused feature suite:

```bash
php artisan test tests/Feature/Module8bInventoryTest.php
```

The tests use an isolated in-memory SQLite replacement for the named connection. They do not modify the local Docker database.

## Submission

Required GitHub repository name:

```text
cs85-module8b-inventory-eloquent
```

Submit:

- GitHub repository URL;
- screenshot of the inventory page;
- test URL: `http://localhost:8000/inventory`.

## Checklist

- [x] Laravel migration created.
- [x] `Item` Eloquent model configured.
- [x] Required fillable fields added.
- [x] Controller retrieves inventory data.
- [x] Blade template displays each item.
- [x] Reflection added.
- [x] Exact `/inventory` test URL added.
- [x] Feature tests added.
- [ ] Inventory screenshot added.
- [ ] GitHub repository pushed.
