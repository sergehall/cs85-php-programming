<?php

declare(strict_types=1);

use Cs85\Module6A\Models\PhotographyProject;

if (! function_exists('module6h')) {
    function module6h(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

if (! function_exists('module6Selected')) {
    function module6Selected(string $actual, string $expected): string
    {
        return $actual === $expected ? ' selected' : '';
    }
}

if (! function_exists('module6Checked')) {
    function module6Checked(bool $checked): string
    {
        return $checked ? ' checked' : '';
    }
}

/** @var PhotographyProject $project */
/** @var array<string, string> $errors */
/** @var array<string, mixed> $input */
/** @var array<string, string> $serviceTypes */
/** @var array<string, string> $packages */
/** @var array<string, string> $locationTypes */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Module 6A MVC-Based PHP Application</title>
    <style>
        :root {
            color-scheme: light;
            --ink: #171514;
            --muted: #615c54;
            --line: #ded8cf;
            --panel: #ffffff;
            --accent: #0f766e;
            --accent-dark: #134e4a;
            --warm: #b45309;
            --soft: #f1f7f5;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background:
                linear-gradient(135deg, rgba(18, 17, 16, 0.86), rgba(19, 78, 74, 0.74)),
                url("https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?auto=format&fit=crop&w=1800&q=80") center/cover fixed;
            color: var(--ink);
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.5;
            margin: 0;
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        main {
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 14px;
            box-shadow: 0 28px 80px rgba(0, 0, 0, 0.34);
            margin: 0 auto;
            max-width: 74rem;
            overflow: hidden;
        }

        .hero {
            background: linear-gradient(135deg, rgba(18, 17, 16, 0.96), rgba(19, 78, 74, 0.9), rgba(146, 64, 14, 0.84));
            color: #ffffff;
            display: grid;
            gap: 1rem;
            padding: 1.5rem;
        }

        .back-link,
        .site-link {
            align-items: center;
            border-radius: 999px;
            display: inline-flex;
            font-weight: 800;
            min-height: 2.6rem;
            padding: 0.6rem 0.9rem;
            text-decoration: none;
        }

        .back-link {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.28);
            color: #ffffff;
            justify-self: start;
        }

        .site-link {
            background: #ffffff;
            color: #121110;
            justify-self: start;
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
            font-size: clamp(2.1rem, 6vw, 4rem);
            line-height: 0.98;
            margin: 0;
            max-width: 52rem;
        }

        h2,
        h3 {
            margin: 0;
        }

        .intro {
            color: #d1fae5;
            margin: 0;
            max-width: 50rem;
        }

        .content,
        .workspace,
        .summary-grid,
        form,
        .panel {
            display: grid;
            gap: 1rem;
        }

        .content {
            padding: 1.5rem;
        }

        @media (min-width: 860px) {
            .workspace {
                grid-template-columns: minmax(0, 0.92fr) minmax(0, 1.08fr);
                align-items: start;
            }

            .summary-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .panel,
        .metric {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 1rem;
        }

        .metric {
            background: var(--soft);
        }

        .metric span {
            color: var(--muted);
            display: block;
            font-size: 0.76rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .metric strong {
            color: var(--accent-dark);
            display: block;
            font-size: 2rem;
            margin-top: 0.25rem;
        }

        label {
            color: var(--muted);
            display: grid;
            font-size: 0.9rem;
            font-weight: 800;
            gap: 0.35rem;
        }

        input,
        select,
        textarea {
            border: 1px solid #b8b0a5;
            border-radius: 8px;
            color: var(--ink);
            font: inherit;
            min-height: 2.7rem;
            padding: 0.65rem 0.75rem;
        }

        textarea {
            min-height: 6rem;
            resize: vertical;
        }

        .checkbox-row {
            align-items: center;
            display: flex;
            gap: 0.65rem;
        }

        .checkbox-row input {
            min-height: auto;
        }

        button {
            background: #121110;
            border: 1px solid #121110;
            border-radius: 8px;
            color: #ffffff;
            cursor: pointer;
            font: inherit;
            font-weight: 800;
            min-height: 2.8rem;
            padding: 0.7rem 0.9rem;
        }

        .notice {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 12px;
            color: #9a3412;
            padding: 1rem;
        }

        .result {
            background: #ecfdf5;
            border: 1px solid #86efac;
            border-radius: 12px;
            color: #166534;
            padding: 1rem;
        }

        .detail-list {
            display: grid;
            gap: 0.55rem;
            margin: 0;
        }

        .detail-row {
            border-top: 1px solid var(--line);
            display: flex;
            gap: 1rem;
            justify-content: space-between;
            padding-top: 0.55rem;
        }

        dt {
            color: var(--muted);
            font-weight: 700;
        }

        dd {
            font-weight: 800;
            margin: 0;
            text-align: right;
        }

        code {
            background: #ccfbf1;
            border-radius: 6px;
            color: var(--accent-dark);
            font-weight: 800;
            padding: 0.1rem 0.35rem;
        }
    </style>
</head>
<body>
    <main>
        <section class="hero">
            <a class="back-link" href="/roadmap/module-6">Back to Module 6</a>
            <p class="kicker">Module 6 Assignment 6A - MVC + PSR-4</p>
            <h1>MVC-Based PHP Application</h1>
            <p class="intro">A SERGIOARTG booking planner built with Composer PSR-4 autoloading, a Model for quote rules, a Controller for validation and input flow, and a View for the browser UI.</p>
            <a class="site-link" href="https://sergioartg.com" target="_blank" rel="noopener noreferrer">View SERGIOARTG live site</a>
        </section>

        <section class="content">
            <?php if ($errors !== []): ?>
                <div class="notice">
                    <strong>Validation adjusted the request:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= module6h($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <section class="summary-grid" aria-label="Quote summary">
                <article class="metric">
                    <span>Quote total</span>
                    <strong><?= module6h($project->formattedQuoteTotal()) ?></strong>
                </article>
                <article class="metric">
                    <span>Deposit due</span>
                    <strong><?= module6h($project->formattedDepositDue()) ?></strong>
                </article>
                <article class="metric">
                    <span>Complexity</span>
                    <strong><?= module6h($project->complexityLabel()) ?></strong>
                </article>
            </section>

            <section class="workspace" aria-label="MVC application workspace">
                <form class="panel" method="post">
                    <?php if (function_exists('csrf_token')): ?>
                        <input type="hidden" name="_token" value="<?= module6h((string) csrf_token()) ?>">
                    <?php endif; ?>
                    <h2>Controller Input</h2>
                    <label>
                        Client name
                        <input name="client_name" value="<?= module6h($project->clientName) ?>">
                    </label>
                    <label>
                        Service type
                        <select name="service_type">
                            <?php foreach ($serviceTypes as $value => $label): ?>
                                <option value="<?= module6h($value) ?>"<?= module6Selected($project->serviceType, $value) ?>><?= module6h($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>
                        Package
                        <select name="package">
                            <?php foreach ($packages as $value => $label): ?>
                                <option value="<?= module6h($value) ?>"<?= module6Selected($project->package, $value) ?>><?= module6h($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>
                        Session hours
                        <input name="hours" type="number" min="1" max="8" step="0.5" value="<?= module6h((string) $project->hours) ?>">
                    </label>
                    <label>
                        Edited photos
                        <input name="edited_photos" type="number" min="5" max="80" value="<?= module6h((string) $project->editedPhotos) ?>">
                    </label>
                    <label>
                        Location
                        <select name="location_type">
                            <?php foreach ($locationTypes as $value => $label): ?>
                                <option value="<?= module6h($value) ?>"<?= module6Selected($project->locationType, $value) ?>><?= module6h($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>
                        Deposit paid
                        <input name="deposit_paid" type="number" min="0" max="5000" step="25" value="<?= module6h(number_format($project->depositPaidCents / 100, 0, '.', '')) ?>">
                    </label>
                    <label class="checkbox-row">
                        <input name="rush_delivery" type="checkbox" value="1"<?= module6Checked($project->rushDelivery) ?>>
                        Rush delivery
                    </label>
                    <label>
                        Project note
                        <textarea name="project_note"><?= module6h($project->projectNote) ?></textarea>
                    </label>
                    <button type="submit">Update MVC quote</button>
                </form>

                <section class="panel">
                    <h2>View Output</h2>
                    <div class="result">
                        <h3><?= module6h($project->summary()) ?></h3>
                        <p><?= module6h($project->workflowDecision()) ?></p>
                    </div>
                    <dl class="detail-list">
                        <div class="detail-row">
                            <dt>Model</dt>
                            <dd><code>PhotographyProject</code></dd>
                        </div>
                        <div class="detail-row">
                            <dt>Controller</dt>
                            <dd><code>BookingPlannerController</code></dd>
                        </div>
                        <div class="detail-row">
                            <dt>View</dt>
                            <dd><code>booking-planner.php</code></dd>
                        </div>
                        <div class="detail-row">
                            <dt>Composer namespace</dt>
                            <dd><code>Cs85\Module6A\</code></dd>
                        </div>
                    </dl>
                </section>
            </section>
        </section>
    </main>
</body>
</html>
