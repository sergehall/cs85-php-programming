<?php
/*
 * Module 4 Assignment 4A: Database Setup
 * Student: Serge Hall
 *
 * This first version creates the visible assignment workspace. Later commits
 * add request handling, validation, MySQL connection checks, and roadmap access.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Module 4A Database Setup</title>
    <style>
        body {
            background: #f8fafc;
            color: #111827;
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.5;
            margin: 0;
            padding: 2rem 1rem;
        }

        main {
            background: #ffffff;
            border: 1px solid #d8dee8;
            border-radius: 10px;
            margin: 0 auto;
            max-width: 58rem;
            padding: 1.5rem;
        }

        form {
            display: grid;
            gap: 1rem;
        }

        fieldset {
            border: 1px solid #d8dee8;
            border-radius: 8px;
            display: grid;
            gap: 0.75rem;
            padding: 1rem;
        }

        label {
            display: grid;
            font-weight: 700;
            gap: 0.35rem;
        }

        input,
        select,
        textarea {
            border: 1px solid #aab4c4;
            border-radius: 6px;
            font: inherit;
            padding: 0.7rem;
        }

        textarea {
            min-height: 8rem;
            resize: vertical;
        }
    </style>
</head>
<body>
<main>
    <a href="/roadmap/module-4">Back to Module 4</a>
    <h1>Module 4 Assignment 4A: Database Setup</h1>
    <p>
        Use this page to demonstrate that your local PHP, MySQL, and phpMyAdmin environment is ready.
    </p>

    <form action="" method="POST">
        <fieldset>
            <legend>Development Environment</legend>

            <label for="environment">
                Setup option
                <select id="environment" name="environment" required>
                    <option value="">Choose one</option>
                    <option value="Laravel Herd">Laravel Herd for macOS</option>
                    <option value="XAMPP">XAMPP</option>
                </select>
            </label>

            <label for="phpmyadmin_url">
                phpMyAdmin URL
                <input
                    type="url"
                    id="phpmyadmin_url"
                    name="phpmyadmin_url"
                    placeholder="https://phpmyadmin.test"
                    required
                >
            </label>
        </fieldset>

        <fieldset>
            <legend>MySQL Login Check</legend>

            <label for="mysql_host">
                MySQL host
                <input type="text" id="mysql_host" name="mysql_host" value="127.0.0.1" required>
            </label>

            <label for="mysql_port">
                MySQL port
                <input type="number" id="mysql_port" name="mysql_port" value="3306" min="1" max="65535" required>
            </label>

            <label for="mysql_user">
                MySQL username
                <input type="text" id="mysql_user" name="mysql_user" value="root" required>
            </label>

            <label for="mysql_password">
                MySQL password
                <input type="password" id="mysql_password" name="mysql_password">
            </label>
        </fieldset>

        <fieldset>
            <legend>Screenshot Checklist</legend>

            <label>
                <input type="checkbox" name="evidence[]" value="PHP service running">
                PHP service is running
            </label>

            <label>
                <input type="checkbox" name="evidence[]" value="MySQL installed or running">
                MySQL is installed or running
            </label>

            <label>
                <input type="checkbox" name="evidence[]" value="phpMyAdmin open">
                phpMyAdmin is open in the browser
            </label>

            <label>
                <input type="checkbox" name="evidence[]" value="Logged into MySQL">
                Logged into MySQL with phpMyAdmin or terminal
            </label>
        </fieldset>

        <label for="notes">
            Setup notes for Canvas submission
            <textarea id="notes" name="notes" placeholder="Write what your screenshots prove."></textarea>
        </label>

        <button type="submit" name="submit" value="Generate Setup Report">Generate Setup Report</button>
    </form>
</main>
</body>
</html>
