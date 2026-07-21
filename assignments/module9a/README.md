# Module 9 Assignment 9A: Contact List App

## Assignment Goal

Build a conventional Laravel resource CRUD application that can create, list,
edit, and delete contacts with required name, email, and phone fields.

The assignment application is available at:

```text
/contacts
```

An extended CRUD workbench with filters, JSON import, and contact groups remains
available at `/assignments/module9a/contacts` as an additional demonstration.

The Module 9 roadmap links to the workbench from:

```text
/roadmap/module-9
```

## Data Model

The assignment uses two isolated training tables:

```text
contact_groups
  id
  name
  description
  created_at
  updated_at

contacts
  id
  contact_group_id -> contact_groups.id
  name
  first_name
  last_name
  email
  phone
  company
  role
  is_active
  notes
  created_at
  updated_at
```

A contact optionally belongs to one contact group. Deleting a group sets the
contact's foreign key to `NULL` instead of deleting the contact.

These tables are intentionally separate from the application's real `users`
table. Deleting or resetting Module 9 records cannot delete authentication
accounts. The contact `role` field is educational record data and does not grant
application permissions.

## Default JSON Dataset

The versioned source file is:

```text
assignments/module9a/data/contacts.json
```

It contains fictional data only. The import operation is idempotent:

- groups are matched by name;
- contacts are matched by normalized email;
- missing records are inserted;
- matching records are updated;
- repeated imports do not create duplicates.

## HTTP and CRUD Map

| Operation        | Method   | Route                      | Laravel behavior                         |
| ---------------- | -------- | -------------------------- | ---------------------------------------- |
| List contacts    | `GET`    | `/contacts`                | `index()` renders `contacts.index`       |
| Open create form | `GET`    | `/contacts/create`         | `create()` renders `contacts.create`     |
| Create contact   | `POST`   | `/contacts`                | `store()` validates and inserts          |
| Read one contact | `GET`    | `/contacts/{contact}`      | `show()` redirects to the edit page      |
| Open edit form   | `GET`    | `/contacts/{contact}/edit` | `edit()` renders `contacts.edit`         |
| Update contact   | `PUT`    | `/contacts/{contact}`      | `update()` uses route model binding      |
| Delete contact   | `DELETE` | `/contacts/{contact}`      | `destroy()` deletes the selected contact |

The seven conventional CRUD routes are registered with:

```php
Route::resource('contacts', ContactController::class);
```

All mutating routes use CSRF protection and rate limiting. Mutations are
available in local and testing environments. In production they require an
authenticated application administrator.

The advanced workbench keeps its additional endpoints under
`/assignments/module9a/contacts` for JSON import, filtered JSON output, focused
detail updates, and dataset reset operations.

The focused PUT editor intentionally allows any fictional Module 9 contact to
be selected in local and testing environments. A real self-service profile
would use an ownership policy so regular users could update only their own
record. That production profile concern is separate from these training tables.

## GET Filters

The Eloquent query supports:

- global search across name, email, phone, and company;
- first name;
- last name;
- email;
- phone;
- contact group;
- sample role (`user` or `admin`);
- active/inactive status;
- allowlisted sorting.

User input is always passed through Eloquent query bindings. Sort columns and
directions come from a server-side allowlist.

## Laravel Structure

```text
app/Http/Controllers/ContactController.php
app/Http/Controllers/Assignments/Module9aContactController.php
app/Http/Controllers/Assignments/Module9aContactDatasetController.php
app/Http/Requests/StoreContactRequest.php
app/Http/Requests/UpdateContactRequest.php
app/Http/Requests/Assignments/*Module9aContactRequest.php
app/Models/Contact.php
app/Models/ContactGroup.php
app/Services/Modules/Module9A/ContactDatasetImporter.php
app/Services/Modules/Module9A/ContactDirectoryQuery.php
app/Services/Modules/Module9A/Module9aWriteAccess.php
database/migrations/2026_07_14_000014_create_module9_contact_tables.php
database/migrations/2026_07_20_000016_align_contacts_with_resource_crud_requirements.php
resources/views/contacts/index.blade.php
resources/views/contacts/create.blade.php
resources/views/contacts/edit.blade.php
resources/views/assignments/module9a/contacts.blade.php
tests/Feature/ContactResourceTest.php
tests/Feature/Module9aContactListTest.php
```

## Local Setup

Run the normal project migration:

```bash
php artisan migrate
```

Open `/contacts` for the required resource CRUD application. Open the advanced
workbench and select **POST · Import JSON** when the extended demonstration data
is needed. No separate seeder or manual SQL is required.

## Validation Rules

- name is required and limited to 255 characters;
- email is required, normalized to lowercase, valid, and unique;
- phone is required and limited to 32 characters;
- the extended first and last name fields are required and synchronized with
  the standard `name` column;
- company, group, and notes are optional;
- the focused PUT editor validates the selected training contact and changes
  only its phone, company, and optional group;
- selected groups must exist;
- role must be `user` or `admin`;
- active status is normalized to a boolean;
- delete-by-ID requires an existing positive primary key.

## Quality Checks

```bash
php artisan test tests/Feature/Module9aContactListTest.php
php artisan test tests/Feature/ContactResourceTest.php
php artisan test
composer lint
vendor/bin/pint --test
npm run quality
```
