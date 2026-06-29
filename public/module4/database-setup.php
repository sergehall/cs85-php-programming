<?php
/*
 * Module 4 Assignment 4A: Database Setup
 * Student: Serge Hall
 *
 * This first version creates the visible assignment workspace. Later commits
 * add request handling, validation, MySQL connection checks, and roadmap access.
 */
$errors = [];
$submitted = $_SERVER['REQUEST_METHOD'] === 'POST';

$environment = '';
$phpMyAdminUrl = '';
$mysqlHost = '127.0.0.1';
$mysqlPort = '3306';
$mysqlUser = 'root';
$notes = '';
$evidence = [];

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

if ($submitted) {
    $environment = trim((string) ($_POST['environment'] ?? ''));
    $phpMyAdminUrl = trim((string) ($_POST['phpmyadmin_url'] ?? ''));
    $mysqlHost = trim((string) ($_POST['mysql_host'] ?? ''));
    $mysqlPort = trim((string) ($_POST['mysql_port'] ?? ''));
    $mysqlUser = trim((string) ($_POST['mysql_user'] ?? ''));
    $notes = trim((string) ($_POST['notes'] ?? ''));
    $evidence = array_values(array_filter((array) ($_POST['evidence'] ?? []), 'is_string'));

    if ($environment === '') {
        $errors['environment'] = 'Choose Laravel Herd or XAMPP.';
    }

    if ($phpMyAdminUrl === '' || filter_var($phpMyAdminUrl, FILTER_VALIDATE_URL) === false) {
        $errors['phpmyadmin_url'] = 'Enter a valid phpMyAdmin URL.';
    }

    if ($mysqlHost === '') {
        $errors['mysql_host'] = 'MySQL host is required.';
    }

    if ($mysqlPort === '' || filter_var($mysqlPort, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]]) === false) {
        $errors['mysql_port'] = 'Enter a valid MySQL port from 1 to 65535.';
    }

    if ($mysqlUser === '') {
        $errors['mysql_user'] = 'MySQL username is required.';
    }

    if (count($evidence) < 3) {
        $errors['evidence'] = 'Check at least three screenshot evidence items.';
    }
}

$isReady = $submitted && $errors === [];
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

        .alert {
            border-radius: 8px;
            font-weight: 700;
            margin: 1rem 0;
            padding: 0.85rem 1rem;
        }

        .alert.error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        .alert.success {
            background: #dcfce7;
            border: 1px solid #86efac;
            color: #166534;
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

    <?php if ($submitted && $errors !== []) { ?>
        <section class="alert error">
            <strong>Fix these setup report fields:</strong>
            <ul>
                <?php foreach ($errors as $error) { ?>
                    <li><?php echo h($error); ?></li>
                <?php } ?>
            </ul>
        </section>
    <?php } ?>

    <?php if ($isReady) { ?>
        <section class="alert success">
            <h2>Setup Report Ready</h2>
            <p>
                Environment: <?php echo h($environment); ?>. phpMyAdmin URL:
                <a href="<?php echo h($phpMyAdminUrl); ?>"><?php echo h($phpMyAdminUrl); ?></a>.
            </p>
            <p>
                MySQL login target: <?php echo h($mysqlUser); ?>@<?php echo h($mysqlHost); ?>:<?php echo h($mysqlPort); ?>.
            </p>
            <p>Screenshot evidence checked: <?php echo h((string) count($evidence)); ?> item(s).</p>
            <?php if ($notes !== '') { ?>
                <p>Canvas notes: <?php echo h($notes); ?></p>
            <?php } ?>
        </section>
    <?php } ?>

    <form action="" method="POST">
        <fieldset>
            <legend>Development Environment</legend>

            <label for="environment">
                Setup option
                <select id="environment" name="environment" required>
                    <option value="">Choose one</option>
                    <option value="Laravel Herd" <?php echo $environment === 'Laravel Herd' ? 'selected' : ''; ?>>Laravel Herd for macOS</option>
                    <option value="XAMPP" <?php echo $environment === 'XAMPP' ? 'selected' : ''; ?>>XAMPP</option>
                </select>
            </label>

            <label for="phpmyadmin_url">
                phpMyAdmin URL
                <input
                    type="url"
                    id="phpmyadmin_url"
                    name="phpmyadmin_url"
                    placeholder="https://phpmyadmin.test"
                    value="<?php echo h($phpMyAdminUrl); ?>"
                    required
                >
            </label>
        </fieldset>

        <fieldset>
            <legend>MySQL Login Check</legend>

            <label for="mysql_host">
                MySQL host
                <input type="text" id="mysql_host" name="mysql_host" value="<?php echo h($mysqlHost); ?>" required>
            </label>

            <label for="mysql_port">
                MySQL port
                <input type="number" id="mysql_port" name="mysql_port" value="<?php echo h($mysqlPort); ?>" min="1" max="65535" required>
            </label>

            <label for="mysql_user">
                MySQL username
                <input type="text" id="mysql_user" name="mysql_user" value="<?php echo h($mysqlUser); ?>" required>
            </label>

            <label for="mysql_password">
                MySQL password
                <input type="password" id="mysql_password" name="mysql_password">
            </label>
        </fieldset>

        <fieldset>
            <legend>Screenshot Checklist</legend>

            <label>
                <input type="checkbox" name="evidence[]" value="PHP service running" <?php echo in_array('PHP service running', $evidence, true) ? 'checked' : ''; ?>>
                PHP service is running
            </label>

            <label>
                <input type="checkbox" name="evidence[]" value="MySQL installed or running" <?php echo in_array('MySQL installed or running', $evidence, true) ? 'checked' : ''; ?>>
                MySQL is installed or running
            </label>

            <label>
                <input type="checkbox" name="evidence[]" value="phpMyAdmin open" <?php echo in_array('phpMyAdmin open', $evidence, true) ? 'checked' : ''; ?>>
                phpMyAdmin is open in the browser
            </label>

            <label>
                <input type="checkbox" name="evidence[]" value="Logged into MySQL" <?php echo in_array('Logged into MySQL', $evidence, true) ? 'checked' : ''; ?>>
                Logged into MySQL with phpMyAdmin or terminal
            </label>
        </fieldset>

        <label for="notes">
            Setup notes for Canvas submission
            <textarea id="notes" name="notes" placeholder="Write what your screenshots prove."><?php echo h($notes); ?></textarea>
        </label>

        <button type="submit" name="submit" value="Generate Setup Report">Generate Setup Report</button>
    </form>
</main>
</body>
</html>
