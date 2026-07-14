# Module 8 Assignment 8A: Laravel with a Database Environment

Course: CS 85 - PHP Programming  
Student: Siarhei Hancharou

## Objective

This assignment configures a local MySQL database environment for Laravel, runs the framework migrations, and documents the result. Module 8A lives inside the main CS85 Laravel repository instead of creating a second nested Laravel installation. It uses the root project's `artisan`, Composer dependencies, database configuration, and migrations while keeping the assignment documentation in `assignments/module8a`.

## Requirements Verified

- PHP 8.4.22 is installed through Laravel Herd.
- Composer 2.9.5 is available.
- Laravel 13.16.1 is installed in the main coursework project.
- PHP extensions `PDO`, `pdo_mysql`, and `mysqlnd` are enabled.
- A sanitized MySQL environment template is committed as `.env.example`.
- The interactive assignment page tests a local MySQL connection without storing credentials.

## Why This Assignment Uses the Existing Laravel Project

The tutorial begins with `laravel new orm_practice`. This repository is already a complete Laravel application, so generating another framework inside it would duplicate `artisan`, `vendor`, configuration, routes, and application code. Reusing the existing installation fulfills the database-environment learning objective while keeping the coursework repository maintainable.

## Start MySQL

The course tutorial uses MySQL from Laravel Herd or XAMPP. This coursework repository already provides MySQL through the `cs85-mysql` Docker container, so Module 8A reuses that working local database service:

```text
Host: 127.0.0.1
Host port: 3307
Database: orm_practice_db
Username: module8a
Password: stored only in the ignored root .env
```

Start the existing service and confirm its health:

```bash
docker compose up -d mysql
docker inspect cs85-mysql --format='{{.State.Health.Status}}'
```

The container maps host port `3307` to MySQL port `3306`, avoiding conflicts with other database installations. Adminer is available at `http://127.0.0.1:8081` for visual database inspection.

## Create the Database

Create a database named `orm_practice_db` through the MySQL container or Adminer:

```sql
CREATE DATABASE orm_practice_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
```

## Configure Laravel

Copy the module-specific values from `assignments/module8a/.env.example` into the ignored root `.env` file. A named connection keeps Module 8A on MySQL without changing the main coursework application's SQLite connection:

```dotenv
MODULE8A_DB_HOST=127.0.0.1
MODULE8A_DB_PORT=3307
MODULE8A_DB_DATABASE=orm_practice_db
MODULE8A_DB_USERNAME=module8a
MODULE8A_DB_PASSWORD=replace_with_local_password
```

After changing `.env`, clear Laravel's cached configuration:

```bash
php artisan config:clear
```

## Run Migrations

From the repository root, run:

```bash
php artisan migrate --database=module8a
php artisan migrate:status --database=module8a
```

The first command creates Laravel's tables in `orm_practice_db` through the named MySQL connection. The status command confirms which migrations ran successfully.

## Interactive Connection Lab

Start the main application:

```bash
php artisan serve
```

Then open:

```text
http://127.0.0.1:8000/assignments/module8a/database-environment.php
```

The form accepts only `127.0.0.1` or `localhost`, validates all connection fields, uses a three-second timeout, and never redisplays the password. When the password field is blank, Laravel uses `MODULE8A_DB_PASSWORD` from the ignored local `.env`. After connecting, it runs this fixed read-only query:

```sql
SELECT
    DATABASE() AS database_name,
    VERSION() AS mysql_version,
    CURRENT_USER() AS connected_user,
    NOW() AS server_time,
    (
        SELECT COUNT(*)
        FROM information_schema.tables
        WHERE table_schema = DATABASE()
    ) AS table_count;
```

The result shows the active database, MySQL version, authenticated local user, server time, and number of tables created by migrations. The page does not accept custom SQL, so it cannot be used to modify or delete data.

## Troubleshooting

### MySQL is not reachable

Run `docker compose up -d mysql` and confirm `cs85-mysql` is healthy. The application connects to host port `3307`, not the container's internal port `3306`.

### Unknown database

Create `orm_practice_db` before running migrations. Database names must match exactly.

### Access denied

Check `MODULE8A_DB_USERNAME` and `MODULE8A_DB_PASSWORD` in the ignored root `.env`. Leaving the form field blank uses this server-side password; it does not mean that the Docker user has no password.

### Laravel still uses the previous connection

Clear the configuration cache:

```bash
php artisan config:clear
```

### PDO MySQL is missing

Run `php -m` and confirm `pdo_mysql` appears. Switch to a PHP build that includes the extension or enable it before retrying.

## Security Notes

- The real root `.env` file remains excluded from Git.
- `.env.example` contains only safe local defaults and no secret value.
- The dedicated `module8a` user has access to `orm_practice_db` instead of using the MySQL root account.
- The connection form is protected by Laravel CSRF validation and rate limiting.
- The host allowlist prevents the form from connecting to arbitrary remote database servers.
- The diagnostic SQL is fixed in the controller and performs no writes.
- Connection failures are converted into safe troubleshooting messages instead of exposing raw exception details.

## Submission Checklist

- [x] Sanitized `.env.example` created.
- [x] README includes setup instructions and troubleshooting notes.
- [x] Interactive local connection check implemented.
- [x] Module 8 roadmap entry added.
- [x] Feature tests added.
- [x] MySQL running in the healthy `cs85-mysql` Docker container.
- [x] `orm_practice_db` created.
- [x] Laravel migrations completed against the named `module8a` MySQL connection.
- [ ] Successful migration screenshot added to `docs/screenshots`.
- [ ] Project pushed to GitHub.
