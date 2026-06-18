# CS85 PHP Programming

Expandable Laravel workspace for Santa Monica College CS85, Summer 2026.

This project is intentionally structured as more than a disposable course sandbox. It starts with the CS85 syllabus requirements, then leaves clean extension points for database-backed CRUD, user cabinet workflows, admin-only operations, and an AI-powered final project.

## Goals

- Practice PHP fundamentals, object-oriented PHP, forms, Composer, Laravel routing, Blade views, and MySQL-backed web development.
- Keep assignments, labs, notes, projects, and final-project work in one organized repository.
- Build toward a three-tier Laravel application with a public area, user cabinet, prepared admin rules, database persistence, tests, and AI integration.
- Maintain portfolio-quality habits from the beginning: readable structure, reproducible commands, documented architecture, and quality gates.

## Stack

- PHP 8.5 via Homebrew
- Laravel 13
- Composer 2
- Blade templates
- Tailwind CSS 4 through Vite
- Docker Compose local infrastructure
- MySQL 9 for local database persistence
- Redis for cache-ready local development
- Mailpit for local email testing
- Adminer for database inspection
- SQLite for fast default Laravel startup
- PHPUnit feature tests
- Laravel Pint formatting
- Larastan and PHPStan static analysis
- Prettier formatting for JavaScript and project documentation
- Laravel Debugbar for local debugging
- Laravel Tinker for interactive exploration
- OpenAI PHP client for the final project

## Architecture

```text
app/                    Laravel application code
config/course.php        CS85 roadmap, stack, and contact data
config/navigation.php    Public, cabinet, admin, and role navigation rules
routes/web.php           Public, cabinet, and prepared admin routes
resources/views/layouts  Shared Blade application layout
resources/views/pages    Public pages
resources/views/cabinet  User cabinet and admin-rule pages
resources/views/partials Shared Blade partials
resources/css/app.css    Tailwind entrypoint only
scripts/                 Local app and infrastructure automation
tests/Feature            Route, navigation, and access-surface tests
tests/Unit               Configuration and project invariant tests
.github/workflows        GitHub Actions quality automation
assignments/             Weekly assignment work
labs/                    Practice exercises
notes/                   Course notes and reading summaries
projects/                Larger module projects
final-project/           AI-powered final project work
```

## Application Areas

| Area        | Route                 | Purpose                                                        |
| ----------- | --------------------- | -------------------------------------------------------------- |
| Home        | `/`                   | Project entry point and current readiness overview             |
| Roadmap     | `/roadmap`            | CS85 six-week module path                                      |
| Stack       | `/stack`              | Installed tooling and technical foundation                     |
| Contact     | `/contact`            | Course and project contact channels                            |
| Cabinet     | `/cabinet`            | Future authenticated user workspace                            |
| Profile     | `/cabinet/profile`    | Prepared user profile area                                     |
| Coursework  | `/cabinet/coursework` | Prepared assignments, labs, notes, and final-project workspace |
| Messages    | `/cabinet/messages`   | Prepared user message area                                     |
| Admin Tools | `/cabinet/admin`      | Prepared admin-only operational area                           |

`/admin` is kept as a legacy convenience route and redirects to `/cabinet`.

## Prepared Roles

The project currently exposes role intent in `config/navigation.php` before real authentication is added.

- `user`: can view the cabinet, manage their own profile, track coursework, and send messages.
- `admin`: can access future operational tools for users, content, and message review.

When the course reaches sessions, authentication, and authorization, these rules should move behind Laravel middleware, policies, gates, and database-backed role fields.

## Commands

Install dependencies:

```bash
composer install
npm install
```

Prepare the Laravel app:

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate
```

Start the full local application:

```bash
npm run dev-local
```

This command runs Docker infrastructure first, then starts Laravel, Vite, and opens the app in the browser.
It also runs local MySQL migrations before the Laravel server starts.

Alias:

```bash
npm run dev
npm run start:app
```

Run only the frontend asset dev server:

```bash
npm run dev:assets
```

Run only the Laravel server:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Build frontend assets:

```bash
npm run build
```

Run local MySQL migrations against Docker infrastructure:

```bash
npm run db:migrate:local
```

Run tests:

```bash
php artisan test
```

Run PHP formatting:

```bash
composer format
```

Run PHP formatting check:

```bash
composer format:check
```

Run PHP static analysis:

```bash
composer lint
```

Auto-fix PHP style and then run static analysis:

```bash
composer lint-fix
```

VS Code npm script alias:

```bash
npm run lint-fix
```

Run the PHP quality gate:

```bash
composer quality
```

Run frontend/documentation formatting:

```bash
npm run format
```

Run frontend/documentation formatting check:

```bash
npm run format:check
```

Run frontend quality gate:

```bash
npm run quality
```

Check that GitHub Actions workflows do not contain hardcoded Laravel app keys:

```bash
npm run security:ci
```

## Docker Infrastructure

The local infrastructure is managed with Docker Compose:

| Service      | URL / Port              | Purpose                   |
| ------------ | ----------------------- | ------------------------- |
| MySQL        | `127.0.0.1:3307`        | Local Laravel database    |
| Redis        | `127.0.0.1:6379`        | Cache-ready local service |
| Mailpit UI   | `http://127.0.0.1:8025` | Local email inbox         |
| Mailpit SMTP | `127.0.0.1:1025`        | Local SMTP endpoint       |
| Adminer      | `http://127.0.0.1:8081` | Database browser          |

Default database credentials:

```text
database: cs85_php_programming
username: cs85
password: cs85_password
```

Start infrastructure:

```bash
npm run infra:up
```

Stop infrastructure:

```bash
npm run infra:down
```

Alias:

```bash
npm run stop:infra
```

`infra:up` starts Docker Compose services with `--no-recreate`, waits until MySQL accepts connections inside the container, and reuses the same project environment after the first creation.
If Docker Desktop is not running on macOS, the script opens it and waits for Docker to become ready.
`infra:down` stops containers but preserves Docker volumes so local data is not deleted.

The environment is intentionally persistent:

- existing containers are reused on every `npm run infra:up`
- MySQL data is stored in the `cs85-php-programming_mysql-data` Docker volume
- Redis data is stored in the `cs85-php-programming_redis-data` Docker volume
- containers are recreated only if you intentionally change Compose definitions and choose to recreate them manually

## Environment

The default Laravel database is SQLite for quick startup.

For MySQL, update `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=cs85_php_programming
DB_USERNAME=cs85
DB_PASSWORD=cs85_password
CACHE_STORE=database
QUEUE_CONNECTION=database
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
```

For the AI-powered final project, keep the API key local:

```dotenv
OPENAI_API_KEY=
```

Never commit real secrets.

## Quality Gates

Recommended before committing:

```bash
composer quality
npm run quality
composer audit
npm audit
```

Current test coverage verifies:

- public routes render successfully
- cabinet navigation is visible
- `/admin` redirects to `/cabinet`
- user cabinet routes render successfully
- admin-rule routes render inside the cabinet
- the old phrase `A minimal Laravel project` does not appear
- navigation config points only to registered routes
- user and admin role rules remain separated
- the roadmap keeps the six-week CS85 structure
- starter stack and contact data are ready for Blade views
- `resources/css/app.css` stays Tailwind-only

GitHub Actions runs the same quality gates on pushes and pull requests to `main`.
The workflow generates its Laravel `APP_KEY` during the CI run and does not store it in repository files.

## Development Notes

- Blade views use Tailwind utility classes only.
- `resources/css/app.css` is a Tailwind entrypoint, not a place for raw project CSS.
- Course data and navigation rules live in config files so pages can grow without duplicating arrays across views.
- Admin screens are not protected yet. They are placeholders for future middleware, policies, role checks, and audit logging.
- Contact details are adapted from the Lavoval contact surface.

## Roadmap

- Add authentication when the course reaches sessions and authorization.
- Persist profile, coursework, content, and messages in MySQL.
- Replace route closures with controllers as workflows become more complex.
- Add Form Request validation for write operations.
- Add policies for cabinet user ownership and admin-only actions.
- Build CRUD flows for assignments, labs, notes, and final project milestones.
- Add OpenAI-powered final project features with server-side API calls and safe key handling.
