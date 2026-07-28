# CS85 PHP Programming

[![CI](https://github.com/SergeHall/cs85-php-programming/actions/workflows/ci.yml/badge.svg)](https://github.com/SergeHall/cs85-php-programming/actions/workflows/ci.yml)
![Laravel 13](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)
![Course Roadmap](https://img.shields.io/badge/Modules-12%2F12-0f766e)
![Final Project](https://img.shields.io/badge/AI%20Final%20Project-Complete-2563eb)

Completed Laravel coursework and final project for Santa Monica College CS85,
Summer 2026.

The repository brings all 12 course modules, their assignments, and the
AI-Powered Web Application final project into one organized Laravel codebase.
The implementation begins with PHP fundamentals and progresses through forms,
databases, OOP, MVC, Laravel, Eloquent, CRUD, authentication, API data, and a
hybrid local/online AI assistant.

## Application Demo

<p>
  <a href="https://sergehall.github.io/cs85-php-programming/assets/cs85-ai-powered-application-demo.mp4">
    <img
      alt="Watch the CS85 AI-powered Laravel application demo"
      src="https://img.shields.io/badge/Watch%20Application%20Demo-Video%20Walkthrough-0f766e?style=for-the-badge&logo=github&logoColor=white"
    />
  </a>
</p>

<table>
  <tr>
    <td align="center">
      <a href="https://sergehall.github.io/cs85-php-programming/assets/cs85-ai-powered-application-demo.mp4">
        <img
          src="docs/assets/cs85-ai-powered-application-demo-poster.jpg"
          width="720"
          alt="AI Study Studio application demo preview with four connected AI models"
        />
      </a>
      <br/><sub>CS85 Laravel and AI Study Studio walkthrough — click the preview to watch the Application Demo</sub>
    </td>
  </tr>
</table>

The eight-minute Application Demo starts with the local Laravel environment,
tours representative coursework, verifies the Module 12A OpenAI integration,
and demonstrates the final hybrid AI workspace. The video shows three local LM
Studio specialists alongside the online OpenAI `gpt-4o-mini` model, live
provider health, model selection, persistent conversations, and streamed AI
responses.

[Watch the full Application Demo through GitHub Pages](https://sergehall.github.io/cs85-php-programming/assets/cs85-ai-powered-application-demo.mp4).

> GitHub Pages publishes the static project documentation. The Laravel
> application and its AI providers run locally because they require PHP, a
> database, authentication, environment secrets, LM Studio, and the OpenAI API.

## Project Goals

- Practice PHP fundamentals, forms, Composer, routing, Blade, databases,
  authentication, authorization, and Laravel application structure.
- Keep all CS85 work in one versioned repository instead of separate loose
  folders.
- Make each assignment easy to submit with a stable local URL and a GitHub file
  link.
- Keep the Laravel `public/` directory limited to the front controller and
  browser-safe assets, so source code, configuration, templates, and domain
  classes are not web-served directly.
- Present the completed coursework as a portfolio-quality application with
  tests, static analysis, CI, Docker-backed infrastructure, and security
  controls.

## Current Status

The completed project includes:

- Public Laravel pages for home, roadmap, stack, and contact.
- All 12 CS85 modules and their assignment evidence, driven by
  `config/course.php`.
- Session authentication with email/password registration and login.
- GitHub OAuth login and authenticated account connection.
- A protected user cabinet and admin-only cabinet area.
- Editable profile fields for first name, last name, portfolio links, bio, and
  technical skills.
- Security headers and a strict Content Security Policy.
- Docker Compose services for MySQL, Redis, Mailpit, and Adminer.
- Assignment pages served through explicit Laravel routes.
- A Module 9 Contact List CRUD workbench with a versioned JSON importer,
  Eloquent relationships, filters, validation, and complete UI operations.
- PHPUnit feature and unit tests.
- Module 11A with both the required static weather JSON exercise and an advanced
  clean-architecture API workbench.
- Module 12A with a verified server-side OpenAI `gpt-4o-mini` content generator.
- A completed hybrid AI learning assistant with persistent multi-turn
  conversations, three streamed LM Studio models, one OpenAI `gpt-4o-mini`
  online model, live connection monitoring, specialized routing, and read-only
  course tools.
- Laravel Pint, Larastan/PHPStan, Prettier, Vite build checks, and GitHub
  Actions CI.

## Stack

- PHP 8.5 locally through Homebrew
- PHP 8.4 in GitHub Actions CI
- Laravel 13
- Composer 2
- Blade templates
- Tailwind CSS 4 through Vite
- Docker Compose local infrastructure
- MySQL 9 for local persistent development data
- SQLite for fast testing and default Laravel startup
- Redis available for cache and queue workloads
- Mailpit for local email testing
- Adminer for local database inspection
- PHPUnit for tests
- Laravel Pint for PHP formatting
- Larastan/PHPStan for static analysis
- Prettier for project documentation, JavaScript, and workflow formatting
- Laravel HTTP client for external JSON and OpenAI API integration
- `openai-php/client` installed as an additional OpenAI SDK option

## Architecture

Detailed engineering documentation is maintained under [`docs/`](docs/README.md):

- [Authentication and Account Security](docs/authentication/README.md)
- [Authentication Architecture](docs/authentication/architecture.md)
- [Authentication Audit Events](docs/authentication/audit-events.md)
- [Authentication Operations Runbook](docs/authentication/operations.md)
- [Authentication Testing Strategy](docs/authentication/testing.md)
- [AI Platform SRS](docs/AI_PLATFORM_SRS.md)
- [AI Architecture](docs/architecture/ai-architecture.md)
- [AI Model Runtime and Request Lifecycle](docs/architecture/ai-model-runtime.md)
- [AI Course Progression](docs/architecture/ai-course-progression.md)
- [AI Local Setup](docs/architecture/ai-local-setup.md)
- [Final Project Implementation](assignments/final-project-ai/README.md)

```text
app/                         Laravel application code
app/Http/Controllers         Auth, assignment, cabinet, and workflow controllers
app/Http/Middleware          Security and role middleware
app/Models                   Eloquent models
app/Services/AI              Provider routing, conversations, tools, and telemetry
app/Services/Modules         Reusable coursework services and domain classes
assignments/                 Course assignment source files outside public web root
assignments/final-project-ai Final Project requirements and implementation evidence
bootstrap/                   Laravel application bootstrap
config/                      Application, course, security, navigation, and cabinet config
database/factories           Test and seed factories
database/migrations          Database schema changes
database/seeders             Seed data
labs/                        Practice exercises
notes/                       Course notes and reading summaries
projects/                    Larger module projects
public/                      Laravel front controller, compiled assets, favicons, robots, sitemap
resources/prompts/ai         Versioned prompts for the four AI modes
resources/css                Tailwind CSS entrypoint
resources/js                 Vite JavaScript entrypoint
resources/views              Blade pages, layouts, cabinet screens, and partials
routes/web.php               Public, assignment, auth, cabinet, and admin web routes
scripts/                     Local development and infrastructure automation
storage/                     Laravel runtime storage
tests/Feature                Route, security, auth, cabinet, and workflow tests
tests/Unit                   Configuration, pricing, and project invariant tests
```

## Public Directory Policy

`public/` is intentionally limited to browser-safe files:

- `index.php`
- compiled Vite assets under `public/build`
- brand images and favicons
- `robots.txt`, `sitemap.xml`, and web manifests

Coursework PHP source files do not live in `public/`. Assignment source files
live in `assignments/`, and reusable PHP classes live under `app/Services`.
Laravel routes expose only allowlisted assignment pages.

This keeps the project closer to production Laravel conventions:

- source code is not directly web-browsable
- configuration is not exposed as public files
- templates and domain classes remain inside application-controlled paths
- URLs are registered explicitly in `routes/web.php`

## Twelve-Module Coursework Map

Every module is represented on the config-driven roadmap and has working
implementation evidence in the repository. Start at
`http://127.0.0.1:8000/roadmap`, or open a module route directly.

| Module | Main topic and completed work                                    | Primary local route                 | Implementation evidence                                                                           |
| ------ | ---------------------------------------------------------------- | ----------------------------------- | ------------------------------------------------------------------------------------------------- |
| 1      | PHP setup and Laravel Hello World                                | `/roadmap/module-1/assignment-1a`   | [Assignment 1A Blade view](resources/views/pages/assignments/module1/assignment1a.blade.php)      |
| 2      | Control flow, pricing rules, loops, and dates                    | `/roadmap/module-2`                 | [Module 2A source](assignments/module2a/) and [Module 2B service](app/Services/Modules/Module2B/) |
| 3      | Forms, request handling, validation, and safe output             | `/roadmap/module-3`                 | [Assignment 3A](assignments/module3a/) and [Assignment 3B](assignments/module3b/)                 |
| 4      | SQL, MySQL, PDO, and prepared statements                         | `/roadmap/module-4`                 | [Assignment 4A](assignments/module4a/) and [Assignment 4B](assignments/module4b/)                 |
| 5      | Object-oriented PHP and domain behavior                          | `/roadmap/module-5`                 | [Assignment 5A](assignments/module5a/)                                                            |
| 6      | Composer autoloading and MVC boundaries                          | `/roadmap/module-6`                 | [Assignment 6A](assignments/module6a/)                                                            |
| 7      | Laravel routes, controllers, Blade layouts, and 404 handling     | `/roadmap/module-7`                 | [Assignment 7A](assignments/module7a/) and [Assignment 7B](assignments/module7b/)                 |
| 8      | MySQL environment, migrations, and Eloquent                      | `/roadmap/module-8`                 | [Assignment 8A](assignments/module8a/) and [Assignment 8B](assignments/module8b/)                 |
| 9      | Full Contact List CRUD, filtering, relationships, and validation | `/contacts`                         | [Assignment 9A](assignments/module9a/)                                                            |
| 10     | Authentication plus an advanced account-security track           | `/roadmap/module-10/assignment`     | [Assignment 10A](assignments/module10a/)                                                          |
| 11     | Static JSON weather data plus an advanced API workbench          | `/weather` and `/roadmap/module-11` | [Assignment 11A](assignments/module11a/)                                                          |
| 12     | OpenAI integration and the AI-Powered Web Application            | `/ai-form` and `/cabinet/ai`        | [Assignment 12A](assignments/module12a/) and [Final Project](assignments/final-project-ai/)       |

The early course-required standalone PHP pages remain under `assignments/` and
are served through an explicit allowlist. Later work uses Laravel controllers,
Form Requests, services, Eloquent models, Blade views, named routes, and
automated tests.

## Module 11 to Final Project Progression

The final three deliverables intentionally show both the required assignment
fundamentals and the more advanced implementation built from them.

| Deliverable                               | Course-aligned requirement                                                                             | Professional extension                                                                                                                                                                       |
| ----------------------------------------- | ------------------------------------------------------------------------------------------------------ | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Module 11A: API Data                      | Read private `weather.json`, call `json_decode()`, pass the array to Blade, and render a weather table | Fetch a fixed HTTPS API, normalize records into `ApiContact` DTOs, validate filters, cache successful data, and use a versioned fallback                                                     |
| Module 12A: Integrating OpenAI            | Submit title, content type, and tone; call OpenAI `gpt-4o-mini` from Laravel; return an editable draft | Structured prompts, validation, timeouts, safe errors, throttling, and HTTP-fake tests without API charges                                                                                   |
| Final Project: AI-Powered Web Application | Provide a usable Laravel interface powered by AI                                                       | Route four specialist modes across three private LM Studio models and one online OpenAI model with authentication, live health checks, streaming, persistence, tools, retries, and telemetry |

Module 12A remains available as a small, grader-friendly implementation at
`/ai-form`. The Final Project reuses its server-side OpenAI configuration as the
online mode at `/cabinet/ai`, while keeping credentials out of the browser.

## Assignment Routing

`App\Http\Controllers\Assignments\AssignmentPhpPageController` is a transitional
bridge for early course assignments that are still written as single PHP files.
It only serves files listed in its allowlist.

The professional target is:

```text
Route -> Controller -> Form Request -> Service/Action -> Blade View -> Tests
```

Use the bridge for course-required single-file PHP assignments. For larger or
newer assignments, prefer Laravel controllers, services, Blade templates,
database migrations, models, and tests.

## Adding New Coursework

For a simple course-required PHP file:

1. Create a folder under `assignments/`, for example `assignments/module5a/`.
2. Add the assignment PHP file there.
3. Register the file in the assignment controller allowlist.
4. Add a route entry in `routes/web.php`.
5. Add the assignment to `config/course.php`.
6. Add a feature test that verifies the assignment URL returns `200`.
7. Run `composer quality` and `npm run quality`.

For a Laravel-native assignment:

1. Add a controller under `app/Http/Controllers/Assignments`.
2. Put business logic in `app/Services/Modules/ModuleX`.
3. Put the Blade view under `resources/views/pages/assignments`.
4. Register a named route in `routes/web.php`.
5. Add the named route to `config/course.php`.
6. Add feature and unit tests.

## Runtime Architecture

The Laravel application runs on macOS through PHP, Composer, Node.js, and Vite.
Project infrastructure runs in Docker Compose and persists between sessions.

```mermaid
flowchart LR
    Developer["Developer / VS Code"] --> Script["npm run dev"]
    Script --> Infra["npm run infra:up"]
    Script --> Migrate["npm run db:migrate:local"]
    Script --> Laravel["Laravel dev server\n127.0.0.1:8000"]
    Script --> Vite["Vite dev server\n127.0.0.1:5173"]
    Script --> Browser["Browser\n127.0.0.1:8000"]

    Infra --> Compose["Docker Compose\ncompose.yaml"]
    Compose --> MySQL["MySQL 9\n127.0.0.1:3307"]
    Compose --> Redis["Redis\n127.0.0.1:6379"]
    Compose --> Mailpit["Mailpit\nSMTP 1025 / UI 8025"]
    Compose --> Adminer["Adminer\n127.0.0.1:8081"]

    Laravel --> MySQL
    Laravel --> Mailpit
    Laravel -. "optional cache/queues" .-> Redis
    Adminer --> MySQL
```

Homebrew MySQL is not required. The app connects to Docker MySQL on
`127.0.0.1:3307`, which avoids conflicts with other local database installations.

## Application Areas

| Area                  | Route                | Purpose                                            |
| --------------------- | -------------------- | -------------------------------------------------- |
| Home                  | `/`                  | Project entry point and readiness overview         |
| Roadmap               | `/roadmap`           | Complete 12-module path with assignment links      |
| Module detail         | `/roadmap/{module}`  | Module-specific assignments, notes, and resources  |
| Module 9 Contact List | `/contacts`          | Eloquent CRUD and JSON import workbench            |
| Module 11 weather     | `/weather`           | Course-aligned static JSON and Blade exercise      |
| Module 11 API         | `/roadmap/module-11` | Advanced normalized API data workbench             |
| Module 12A generator  | `/ai-form`           | OpenAI `gpt-4o-mini` content generator             |
| Stack                 | `/stack`             | Installed tooling and technical foundation         |
| Contact               | `/contact`           | Course and project contact channels                |
| Register              | `/register`          | Create a standard user account                     |
| Login                 | `/login`             | Session login with email/password and GitHub OAuth |
| Cabinet               | `/cabinet`           | Authenticated user workspace                       |
| Final Project AI      | `/cabinet/ai`        | Hybrid local and OpenAI learning assistant         |
| Admin cabinet         | `/cabinet/admin`     | Admin-only operational workspace                   |
| Health                | `/up`                | Laravel health route                               |

`/admin` redirects to `/cabinet` as a legacy convenience route.

## Authentication And Roles

The cabinet is protected with Laravel session authentication.

Supported entry points:

- Email/password registration through `/register`
- Email/password login through `/login`
- GitHub OAuth through `/auth/github/redirect`
- Email verification through signed, expiring links
- Password recovery through `/forgot-password`
- TOTP MFA and one-time recovery codes
- Recent-authentication step-up for sensitive account and admin actions
- Logout through `POST /logout`

GitHub OAuth supports two flows:

- guest sign-in from `/login`
- authenticated account connection from `/cabinet/security`

GitHub account MFA is managed inside GitHub. The application also enforces its
own TOTP MFA challenge after either password or GitHub first-factor login when
application MFA is enabled. GitHub identities must be linked explicitly from an
authenticated, recently confirmed session; matching an existing email address
does not automatically link accounts.

Authentication abuse controls include named rate limiters for login, MFA,
registration, recovery, OAuth, and sensitive actions. Security events are
written to the user/admin activity timeline and to the dedicated rotating
`storage/logs/security.log` channel without passwords, OAuth tokens, MFA codes,
or recovery codes.

The security page supports password changes, active database-session review,
individual session revocation, and revocation of every other session. Password,
MFA, role, login-access, and identity-provider changes rotate remember tokens or
revoke affected sessions as appropriate.

Roles are configured in `config/navigation.php` and enforced for admin routes
with the `admin` middleware.

- `user`: can view the cabinet, manage profile readiness, and track coursework
- `admin`: can access protected user-management and admin-only coursework tools

Newly registered and GitHub-created users receive the `user` role by default.
Admin access must be assigned intentionally.

## User and Admin Cabinet

The authenticated cabinet combines config-driven navigation with
database-backed profile, security, activity, administration, and AI workflows.

User areas:

- Overview
- Profile
- Coursework
- Security
- Activity
- AI Assistant

Admin areas:

- Users
- Content
- Access-request approval
- Role and login-access management

Sensitive actions require recent authentication, and administrative routes are
protected by the `admin` middleware.

## Environment

Create a local environment file:

```bash
cp .env.example .env
php artisan key:generate
```

Default quick-start storage is SQLite. Docker-backed local development uses
MySQL:

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

GitHub OAuth:

```dotenv
GITHUB_CLIENT_ID=
GITHUB_CLIENT_SECRET=
GITHUB_REDIRECT_URI="${APP_URL}/auth/github/callback"
```

Module 12 and Final Project AI providers:

```dotenv
AI_LM_STUDIO_BASE_URL=http://127.0.0.1:1234/v1
AI_LM_STUDIO_API_KEY=lm-studio

OPENAI_API_KEY=your_openai_api_key_here
OPENAI_API_URL=https://api.openai.com/v1
OPENAI_MODEL=gpt-4o-mini
```

Start the LM Studio local server on port `1234` and load the model configured
for the selected General, Coding, or Architecture mode. The OpenAI Online mode
reuses the Module 12A server-side key and `gpt-4o-mini` configuration. Follow
the complete [AI provider setup guide](docs/architecture/ai-local-setup.md) for
provider settings, model verification, connection monitoring, startup order,
streaming checks, and troubleshooting. The rest of the Laravel application
continues to work when either AI provider is offline.

Never commit real secrets.

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

Start the full local stack:

```bash
npm run dev
```

Local startup routes email to Mailpit by default. To use the SMTP credentials
from `.env` instead, set the explicit opt-out and restart the full stack:

```env
CS85_USE_MAILPIT=false
```

The startup log prints either `Mailpit` or `external SMTP from .env` without
exposing credentials. Keep the default enabled unless real delivery is the
specific test objective.

Stop the Laravel and Vite processes started by this project:

```bash
npm run dev:stop
```

Stop any existing local application instance and start a fresh one:

```bash
npm run dev:restart
```

Both commands verify that the process belongs to this project before stopping
it. Docker infrastructure and database volumes remain running and are not
deleted.

Run only Laravel:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Run only Vite:

```bash
npm run dev:assets
```

Start or stop the local LM Studio server:

```bash
npm run ai:server:start
npm run ai:server:stop
```

Build frontend assets:

```bash
npm run build
```

Run Docker MySQL migrations:

```bash
npm run db:migrate:local
```

## Docker Infrastructure

| Service      | URL / Port              | Purpose                   |
| ------------ | ----------------------- | ------------------------- |
| MySQL        | `127.0.0.1:3307`        | Local Laravel database    |
| Redis        | `127.0.0.1:6379`        | Cache-ready local service |
| Mailpit UI   | `http://127.0.0.1:8025` | Local email inbox         |
| Mailpit SMTP | `127.0.0.1:1025`        | Local SMTP endpoint       |
| Adminer      | `http://127.0.0.1:8081` | Database browser          |

Default MySQL credentials:

```text
database: cs85_php_programming
username: cs85
password: cs85_password
```

Start infrastructure:

```bash
npm run infra:up
```

Stop infrastructure but keep containers and volumes:

```bash
npm run infra:down
```

Remove Compose containers without deleting data volumes:

```bash
npm run infra:destroy
```

Use `npm run infra:down` for normal shutdown. Use `npm run infra:destroy` only
when you want Docker Desktop to remove the stopped containers. Avoid deleting
Docker volumes unless you intentionally want to reset local data.

## Quality Gates

Run PHP tests:

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

Run PHP quality gate:

```bash
composer quality
```

Run documentation, workflow, and frontend formatting:

```bash
npm run format
```

Run frontend/documentation quality gate:

```bash
npm run quality
```

Run the full local gate:

```bash
npm run test:all
```

Run dependency audits when network access is available:

```bash
npm run test:all:audit
```

Recommended before committing:

```bash
composer quality
npm run quality
```

## CI

GitHub Actions runs on pushes and pull requests to `main`.

The CI workflow:

- validates Composer configuration
- installs PHP dependencies
- creates a testing `.env`
- generates a Laravel app key during the run
- runs migrations against SQLite
- runs `composer quality`
- audits PHP dependencies
- installs Node.js dependencies
- runs `npm run quality`
- audits Node.js dependencies

The workflow should never store a real `APP_KEY`, OAuth secret, database
password, or API key in repository files.

## Security Controls

The app sends security headers through `App\Http\Middleware\SecurityHeaders`.

Current controls:

- authenticated cabinet security hub at `/cabinet/security`
- GitHub OAuth login and account linking with state validation
- explicit GitHub linking with verified primary email checks
- GitHub account ownership checks before linking an authenticated user
- email verification and password reset notifications
- TOTP MFA challenge TTL, replay protection, and hashed one-time recovery codes
- recent password, MFA, or GitHub step-up for sensitive changes
- per-flow rate limiting for authentication and recovery endpoints
- active-session review and revocation
- structured security audit events plus a dedicated rotating security log
- strict Content Security Policy with `default-src 'none'`
- `script-src 'self'` and `style-src 'self'`
- no `unsafe-inline` or `unsafe-eval` in production policy
- `object-src 'none'`
- `base-uri 'none'`
- `form-action 'self'`
- `frame-ancestors 'none'`
- HSTS outside local development
- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: DENY`
- `Referrer-Policy: strict-origin-when-cross-origin`
- `Cross-Origin-Resource-Policy: same-origin`
- `Cross-Origin-Opener-Policy: same-origin`
- Vite production assets with Subresource Integrity hashes

Local development allows the Vite dev server only when `APP_ENV=local` and
`APP_DEBUG=true`.

## Test Coverage

The test suite currently verifies:

- public pages render successfully
- roadmap modules and assignment links stay registered
- assignment PHP pages render through Laravel
- authenticated users can edit profile identity, portfolio links, bio, and
  technical skills
- Module 2A pricing rules and escaping behavior
- Module 9 contact import, filtering, CRUD, validation, and write protections
- Module 11 static weather decoding, remote API normalization, caching, and
  fallback behavior
- Module 12A validation, prompt variants, OpenAI request shape, and safe
  provider failures through deterministic fakes
- Final Project conversation ownership, model routing, provider health,
  streaming, retry behavior, read-only tools, and telemetry
- security headers and CSP expectations
- registration, login, logout, GitHub OAuth, and GitHub account linking behavior
- email verification, password reset/change, and email normalization
- login/MFA rate limiting and failed-attempt audit behavior
- MFA challenge expiry, TOTP replay resistance, and recovery-code consumption
- recent-authentication step-up for security and admin mutations
- session ownership checks and session revocation
- guests are redirected from protected cabinet pages
- user and admin cabinet access boundaries
- standard users cannot access admin cabinet pages
- navigation config points only to registered routes
- role rules keep user and admin abilities separated
- SEO assets, robots, sitemap, and brand files exist
- CSS entrypoint remains Tailwind-only

## Development Standards

- Keep new source code, configuration, templates, and domain classes out of
  `public/`.
- Prefer named routes over hardcoded URLs for Laravel-native pages.
- Keep controllers thin; move business logic into services or actions.
- Use Form Request classes when an assignment becomes a real Laravel form.
- Escape output in raw PHP assignments with `htmlspecialchars`.
- Use Laravel CSRF protection for POST forms.
- Add tests for every new public route and important business rule.
- Keep `resources/css/app.css` as a Tailwind entrypoint only.
- Keep secrets in `.env`, not in repository files.

## Completion and Possible Next Steps

The 12-module roadmap, Assignment 12A, and the AI-Powered Web Application are
complete. The repository already includes server-side OpenAI calls,
environment-only secrets, provider routing, authentication, rate limiting,
conversation persistence, request telemetry, automated tests, and architecture
documentation.

Possible post-course improvements:

- deploy the dynamic Laravel application to a PHP-capable host; GitHub Pages
  remains the static documentation and demo surface
- add per-user OpenAI token budgets and a visual usage/cost dashboard
- add an automated prompt-evaluation dataset for the four specialist modes
- activate Redis-backed queues and caching for longer-running production work
- add production observability dashboards and alerting for provider latency and
  error rates

## Submission References

- [Application Demo](https://sergehall.github.io/cs85-php-programming/assets/cs85-ai-powered-application-demo.mp4)
- [Module 11A documentation](assignments/module11a/README.md)
- [Module 12A documentation](assignments/module12a/README.md)
- [Final Project implementation evidence](assignments/final-project-ai/README.md)
- [AI architecture documentation](docs/architecture/ai-architecture.md)
- [Complete engineering documentation index](docs/README.md)
