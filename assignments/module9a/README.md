# Module 9 Assignment 9A: Contact List App

## Assignment Goal

Build a complete Laravel CRUD application that can load a default JSON dataset,
read and filter database records, create new contacts, update existing contacts,
and delete one record or clear the training tables.

The interactive workbench is available at:

```text
/assignments/module9a/contacts
```

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

| Operation             | Method   | Route                                      | Laravel behavior                                 |
| --------------------- | -------- | ------------------------------------------ | ------------------------------------------------ |
| Open workbench        | `GET`    | `/assignments/module9a/contacts`           | Render filters, forms, table, and JSON preview   |
| Read JSON response    | `GET`    | `/assignments/module9a/contacts/data`      | Return filtered records as JSON                  |
| Import default data   | `POST`   | `/assignments/module9a/contacts/dataset`   | Transactionally upsert JSON groups and contacts  |
| Create contact        | `POST`   | `/assignments/module9a/contacts`           | Validate and insert a contact                    |
| Update contact        | `PUT`    | `/assignments/module9a/contacts/{contact}` | Validate and update through route model binding  |
| Delete contact        | `DELETE` | `/assignments/module9a/contacts/{contact}` | Delete the bound contact                         |
| Delete by entered ID  | `DELETE` | `/assignments/module9a/contacts/by-id`     | Validate the primary key and delete the row      |
| Clear training tables | `DELETE` | `/assignments/module9a/contacts/dataset`   | Delete contacts, then groups, in one transaction |

All mutating routes use CSRF protection and rate limiting. Mutations are
available in local and testing environments. In production they require an
authenticated application administrator.

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
app/Http/Controllers/Assignments/Module9aContactController.php
app/Http/Controllers/Assignments/Module9aContactDatasetController.php
app/Http/Requests/Assignments/*Module9aContactRequest.php
app/Models/Contact.php
app/Models/ContactGroup.php
app/Services/Modules/Module9A/ContactDatasetImporter.php
app/Services/Modules/Module9A/ContactDirectoryQuery.php
app/Services/Modules/Module9A/Module9aWriteAccess.php
database/migrations/2026_07_14_000014_create_module9_contact_tables.php
resources/views/assignments/module9a/contacts.blade.php
tests/Feature/Module9aContactListTest.php
```

## Local Setup

Run the normal project migration:

```bash
php artisan migrate
```

Then open the workbench and select **POST · Import JSON**. No separate seeder or
manual SQL is required.

## Validation Rules

- first and last name are required and limited to 100 characters;
- email is required, normalized to lowercase, valid, and unique;
- phone, company, group, and notes are optional;
- selected groups must exist;
- role must be `user` or `admin`;
- active status is normalized to a boolean;
- delete-by-ID requires an existing positive primary key.

## Quality Checks

```bash
php artisan test tests/Feature/Module9aContactListTest.php
php artisan test
composer lint
vendor/bin/pint --test
npm run quality
```
