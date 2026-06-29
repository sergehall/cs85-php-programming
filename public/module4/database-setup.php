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
$mysqlPassword = '';
$notes = '';
$evidence = [];
$connectionStatus = 'Not checked yet.';
$connectionDetails = '';

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
    $mysqlPassword = (string) ($_POST['mysql_password'] ?? '');
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

    if ($errors === []) {
        if (!extension_loaded('pdo_mysql')) {
            $connectionStatus = 'PDO MySQL extension is not enabled.';
            $connectionDetails = 'Enable pdo_mysql in PHP before using this page as a live connection check.';
        } else {
            try {
                $dsn = "mysql:host={$mysqlHost};port={$mysqlPort};charset=utf8mb4";
                $pdo = new PDO($dsn, $mysqlUser, $mysqlPassword, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 3,
                ]);
                $version = $pdo->query('select version()')->fetchColumn();
                $connectionStatus = 'Connected to MySQL successfully.';
                $connectionDetails = 'MySQL server version: ' . (is_string($version) ? $version : 'unknown');
            } catch (PDOException $exception) {
                $connectionStatus = 'Could not connect to MySQL.';
                $connectionDetails = $exception->getMessage();
            }
        }
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
        :root {
            color-scheme: light;
            --ink: #111827;
            --muted: #5b6575;
            --panel: #ffffff;
            --line: #d8dee8;
            --accent: #b45309;
            --accent-dark: #78350f;
            --soft: #fffbeb;
            --danger-bg: #fee2e2;
            --danger-line: #fca5a5;
            --danger-text: #991b1b;
            --success-bg: #dcfce7;
            --success-line: #86efac;
            --success-text: #166534;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background:
                radial-gradient(circle at 12% 0%, rgba(245, 158, 11, 0.2), transparent 28rem),
                radial-gradient(circle at 86% 4%, rgba(20, 184, 166, 0.15), transparent 30rem),
                linear-gradient(135deg, #f8fafc 0%, #fff7ed 52%, #fffbeb 100%);
            color: var(--ink);
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.5;
            margin: 0;
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        main {
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid var(--line);
            border-radius: 14px;
            box-shadow: 0 24px 70px rgba(17, 24, 39, 0.14);
            margin: 0 auto;
            max-width: 64rem;
            overflow: hidden;
        }

        .hero {
            background: linear-gradient(135deg, #111827 0%, #78350f 62%, #0f766e 100%);
            color: #ffffff;
            display: grid;
            gap: 1rem;
            padding: 1.5rem;
        }

        .back-link {
            align-items: center;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.28);
            border-radius: 999px;
            color: #ffffff;
            display: inline-flex;
            font-size: 0.9rem;
            font-weight: 700;
            justify-self: start;
            padding: 0.55rem 0.85rem;
            text-decoration: none;
        }

        .kicker {
            color: #fed7aa;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            margin: 0;
            text-transform: uppercase;
        }

        h1 {
            font-size: clamp(2rem, 6vw, 3.7rem);
            line-height: 0.98;
            margin: 0;
            max-width: 46rem;
        }

        .intro {
            color: #ffedd5;
            margin: 0;
            max-width: 46rem;
        }

        .content {
            display: grid;
            gap: 1.25rem;
            padding: 1.5rem;
        }

        .goal-grid {
            display: grid;
            gap: 0.75rem;
        }

        @media (min-width: 760px) {
            .goal-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .goal-card {
            background: var(--soft);
            border: 1px solid #fcd34d;
            border-radius: 10px;
            color: var(--accent-dark);
            font-weight: 700;
            margin: 0;
            padding: 0.9rem 1rem;
        }

        form {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 12px;
            display: grid;
            gap: 1rem;
            padding: 1.25rem;
        }

        fieldset {
            border: 1px solid var(--line);
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
            border-radius: 8px;
            color: var(--ink);
            font: inherit;
            padding: 0.7rem;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(180, 83, 9, 0.16);
            outline: none;
        }

        textarea {
            min-height: 8rem;
            resize: vertical;
        }

        .alert {
            border-radius: 8px;
            font-weight: 700;
            padding: 0.85rem 1rem;
        }

        .alert.error {
            background: var(--danger-bg);
            border: 1px solid var(--danger-line);
            color: var(--danger-text);
        }

        .alert.success {
            background: var(--success-bg);
            border: 1px solid var(--success-line);
            color: var(--success-text);
        }

        .button {
            background: var(--accent);
            border: 1px solid var(--accent);
            border-radius: 8px;
            color: #ffffff;
            cursor: pointer;
            font-weight: 800;
            padding: 0.75rem 1rem;
        }

        .checkbox-list label {
            align-items: center;
            display: flex;
            gap: 0.65rem;
        }

        .checkbox-list input {
            height: 1rem;
            width: 1rem;
        }
    </style>
</head>
<body>
<main>
    <header class="hero">
        <a class="back-link" href="/roadmap/module-4">Back to Module 4</a>
        <p class="kicker">Module 4 Assignment 4A</p>
        <h1>Database Setup</h1>
        <p class="intro">
            Demonstrate a working local PHP, MySQL, and phpMyAdmin environment with a setup checklist,
            screenshot evidence summary, and optional live MySQL connection check.
        </p>
    </header>

    <section class="content">
        <div class="goal-grid">
            <p class="goal-card">Confirm PHP is running locally.</p>
            <p class="goal-card">Confirm MySQL login access.</p>
            <p class="goal-card">Confirm phpMyAdmin is reachable.</p>
        </div>

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
                <p>Connection check: <?php echo h($connectionStatus); ?></p>
                <?php if ($connectionDetails !== '') { ?>
                    <p>Connection details: <?php echo h($connectionDetails); ?></p>
                <?php } ?>
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
                    <input type="password" id="mysql_password" name="mysql_password" autocomplete="current-password">
                </label>
            </fieldset>

            <fieldset class="checkbox-list">
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

            <button class="button" type="submit" name="submit" value="Generate Setup Report">Generate Setup Report</button>
        </form>
    </section>
</main>
</body>
</html>
