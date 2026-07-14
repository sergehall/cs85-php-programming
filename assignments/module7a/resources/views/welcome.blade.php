@php
    $isEmbedded = $embedded ?? false;
    $assignmentBase = $isEmbedded ? '/assignments/module7a' : '';
    $roadmapUrl = $isEmbedded ? '/roadmap/module-7' : 'http://127.0.0.1:8000/roadmap/module-7';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="CS85 Module 7 Assignment 7A: Hello Route, a Laravel routing fundamentals project.">
    <meta name="theme-color" content="#17212b">
    <title>Module 7A: Hello Route</title>
    <style>
        :root {
            color-scheme: light;
            --canvas: #f7f4ef;
            --ink: #17212b;
            --muted: #66717d;
            --panel: #fff;
            --line: #d9d4cc;
            --coral: #e4573d;
            --coral-dark: #a83222;
            --teal: #0f766e;
            --teal-soft: #dff5f1;
            --code: #111b24;
            --radius: 1.25rem;
            --shadow: 0 1.5rem 4rem rgba(23, 33, 43, .12);
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            background: radial-gradient(circle at top right, rgba(228, 87, 61, .13), transparent 30rem), linear-gradient(180deg, #fff 0, var(--canvas) 32rem);
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            line-height: 1.6;
            margin: 0;
        }

        a { color: inherit; }
        a:focus-visible, button:focus-visible, input:focus-visible { outline: .2rem solid #f5a08f; outline-offset: .2rem; }
        .shell { margin: 0 auto; max-width: 74rem; padding: 1rem; }
        .topbar { align-items: center; display: flex; flex-wrap: wrap; gap: .75rem; justify-content: space-between; padding-block: .5rem 1.5rem; }
        .brand { align-items: center; display: inline-flex; font-weight: 850; gap: .65rem; text-decoration: none; }
        .brand-mark { background: var(--coral); border-radius: .7rem; color: #fff; display: grid; height: 2.5rem; place-items: center; width: 2.5rem; }
        .back-link, .button { align-items: center; border-radius: 999px; display: inline-flex; font-weight: 800; justify-content: center; min-height: 2.75rem; padding: .65rem 1rem; text-decoration: none; }
        .back-link { border: 1px solid var(--line); color: var(--muted); }
        .hero { background: var(--ink); border-radius: calc(var(--radius) + .5rem); box-shadow: var(--shadow); color: #fff; overflow: hidden; padding: clamp(1.5rem, 5vw, 4rem); position: relative; }
        .hero::after { background: var(--coral); border-radius: 999px; content: ""; filter: blur(.15rem); height: 15rem; opacity: .85; position: absolute; right: -5rem; top: -6rem; width: 15rem; }
        .hero-content { max-width: 50rem; position: relative; z-index: 1; }
        .eyebrow, .section-label { color: var(--coral-dark); font-size: .75rem; font-weight: 900; letter-spacing: .1em; margin: 0 0 .5rem; text-transform: uppercase; }
        .hero .eyebrow { color: #ffbaa9; }
        h1 { font-size: clamp(2.7rem, 9vw, 6.5rem); letter-spacing: -.065em; line-height: .88; margin: 0; max-width: 9ch; }
        .hero-copy { color: #d5dde4; font-size: clamp(1rem, 2vw, 1.22rem); margin: 1.5rem 0 0; max-width: 46rem; }
        .badges { display: flex; flex-wrap: wrap; gap: .6rem; margin-top: 1.5rem; }
        .badge { background: rgba(255, 255, 255, .1); border: 1px solid rgba(255, 255, 255, .2); border-radius: 999px; color: #f7fafc; font-size: .82rem; font-weight: 750; padding: .4rem .7rem; }
        .layout { display: grid; gap: 1rem; grid-template-columns: minmax(0, 1fr); margin-top: 1rem; }
        .panel { background: var(--panel); border: 1px solid var(--line); border-radius: var(--radius); min-width: 0; padding: clamp(1.15rem, 3vw, 2rem); }
        .panel-accent { background: var(--teal-soft); border-color: #9dd9d1; }
        h2, h3, p { margin-top: 0; }
        h2 { font-size: clamp(1.55rem, 4vw, 2.2rem); letter-spacing: -.035em; line-height: 1.1; margin-bottom: .75rem; }
        h3 { font-size: 1.05rem; margin-bottom: .35rem; }
        .muted { color: var(--muted); }
        .route-grid, .fact-grid, .knowledge-grid { display: grid; gap: 1rem; }
        .route-card { background: #fff; border: 1px solid var(--line); border-radius: 1rem; display: grid; gap: 1rem; min-width: 0; padding: 1.1rem; }
        .route-actions { align-items: center; display: flex; flex-wrap: wrap; gap: .5rem; min-height: 3.2rem; }
        .method { background: var(--teal-soft); border-radius: .5rem; color: #075b55; font-size: .72rem; font-weight: 950; padding: .25rem .45rem; }
        .endpoint { align-items: center; display: flex; flex-wrap: wrap; gap: .55rem; }
        code, pre { font-family: "SFMono-Regular", Consolas, "Liberation Mono", monospace; }
        .endpoint code { font-size: 1rem; font-weight: 850; }
        .response, .terminal { background: var(--code); border-radius: .8rem; color: #c6f6e9; margin: 0; overflow-x: auto; padding: .85rem; }
        .button { background: var(--coral); border: 0; color: #fff; cursor: pointer; min-height: 3.2rem; width: fit-content; }
        .button:hover { background: var(--coral-dark); }
        .button-instructor {
            background: linear-gradient(112deg, #111827 0%, #312e81 24%, #5b5fd6 48%, #3730a3 70%, #0f172a 100%);
            background-size: 190% 190%;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .3), 0 .7rem 1.6rem rgba(49, 46, 129, .28);
            text-shadow: 0 1px 2px rgba(15, 23, 42, .55);
            transition: background-position .4s ease, box-shadow .25s ease, transform .25s ease;
        }
        .button-instructor:hover {
            background-position: 100% 50%;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .38), 0 .95rem 2.1rem rgba(55, 48, 163, .36);
            transform: translateY(-.15rem);
        }
        .greet-form { display: grid; gap: .4rem; margin-top: 1rem; }
        .greet-controls { display: grid; gap: .75rem; }
        .greet-controls input, .greet-controls .button { height: 3.2rem; min-height: 3.2rem; }
        .greet-controls .button { white-space: nowrap; width: 100%; }
        .form-help { color: var(--muted); font-size: .82rem; font-weight: 750; margin: 0; }
        label { color: var(--muted); display: grid; font-size: .85rem; font-weight: 850; gap: .35rem; }
        input { border: 1px solid #aaa39a; border-radius: .75rem; color: var(--ink); font: inherit; min-height: 2.8rem; padding: .65rem .75rem; width: 100%; }
        .terminal { color: #d9e4ea; padding: 1rem; }
        .terminal strong { color: #ffbaa9; }
        .fact { border-left: .25rem solid var(--coral); padding-left: .85rem; }
        .fact p, .answer p { margin-bottom: 0; }
        .checklist { list-style: none; margin: 0; padding: 0; }
        .checklist li { align-items: start; border-top: 1px solid var(--line); display: grid; gap: .75rem; grid-template-columns: 1.5rem 1fr; padding: .85rem 0; }
        .checklist li::before { background: var(--teal); border-radius: 999px; color: #fff; content: "✓"; display: grid; font-size: .75rem; font-weight: 950; height: 1.35rem; place-items: center; width: 1.35rem; }
        footer { color: var(--muted); font-size: .85rem; padding: 1.5rem 0 1rem; text-align: center; }

        @media (min-width: 44rem) {
            .route-grid, .fact-grid, .knowledge-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .route-card { grid-template-rows: auto minmax(3.5rem, 1fr) auto auto; }
            .greet-controls { align-items: stretch; grid-template-columns: minmax(0, 1fr) auto; }
            .greet-controls .button { width: auto; }
        }

        @media (min-width: 64rem) {
            .layout { grid-template-columns: minmax(0, 1.25fr) minmax(19rem, .75fr); }
            .wide { grid-column: 1 / -1; }
        }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            * { transition: none !important; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <a class="brand" href="{{ $assignmentBase ?: '/' }}" aria-label="Module 7A assignment home">
                <span class="brand-mark" aria-hidden="true">7A</span>
                <span>CS85 · Laravel Fundamentals</span>
            </a>
            <a class="back-link" href="{{ $roadmapUrl }}">← Back to Module 7</a>
        </header>

        <main>
            <section class="hero" aria-labelledby="assignment-title">
                <div class="hero-content">
                    <p class="eyebrow">Module 7 Assignment 7A</p>
                    <h1 id="assignment-title">Hello Route</h1>
                    <p class="hero-copy">A standalone Laravel application that demonstrates how URLs connect to route closures, how path parameters carry input, and how Git documents a working project.</p>
                    <div class="badges" aria-label="Project status">
                        <span class="badge">Laravel 13</span>
                        <span class="badge">PHP 8.5</span>
                        <span class="badge">2 required routes</span>
                        <span class="badge">Feature tested</span>
                    </div>
                </div>
            </section>

            <div class="layout">
                <section class="panel wide" aria-labelledby="objective-title">
                    <p class="section-label">Objective</p>
                    <h2 id="objective-title">Learn routing by seeing it work</h2>
                    <p class="muted">Laravel is a modern PHP web framework. A route connects an HTTP method and URL to code that produces a response; web routes live in <code>routes/web.php</code>.</p>
                    <div class="route-grid">
                        <article class="route-card">
                            <div class="endpoint"><span class="method">GET</span><code>/hello</code></div>
                            <p class="muted">A static route returns the required greeting with no input.</p>
                            <pre class="response">Hello from Laravel!</pre>
                            <div class="route-actions">
                                <a class="button" href="{{ $assignmentBase }}/hello">Open /hello</a>
                            </div>
                        </article>

                        <article class="route-card">
                            <div class="endpoint"><span class="method">GET</span><code>/greet/{name}</code></div>
                            <p class="muted">The dynamic segment becomes <code>$name</code>. Hyphens separate name parts and Laravel formats them for display.</p>
                            <pre class="response">Hello, Serge Hall!</pre>
                            <div class="route-actions">
                                <a class="button" href="{{ $assignmentBase }}/greet/alex">Try Alex</a>
                                <a class="button button-instructor" href="{{ $assignmentBase }}/greet/vicky-seno">Vicky Seno</a>
                                <a class="button" href="{{ $assignmentBase }}/greet/serge-hall">Try Serge Hall</a>
                            </div>
                        </article>
                    </div>

                    <form class="greet-form" data-greet-form data-base-path="{{ $assignmentBase }}">
                        <label for="visitor-name">Test another name</label>
                        <div class="greet-controls">
                            <input id="visitor-name" name="name" value="serge-hall" placeholder="serge-hall" pattern="[A-Za-z]+(?:-[A-Za-z]+)*" maxlength="80" autocomplete="name" required aria-describedby="name-help" title="Use English letters and a hyphen between name parts, for example: serge-hall">
                            <button class="button" type="submit">Build greeting route</button>
                        </div>
                        <p class="form-help" id="name-help">Use a hyphen between first and last name, for example: <code>serge-hall</code>. Spaces are not allowed.</p>
                    </form>
                </section>

                <section class="panel panel-accent" aria-labelledby="environment-title">
                    <p class="section-label">Part 1</p>
                    <h2 id="environment-title">Environment verified</h2>
                    <pre class="terminal"><strong>$ php --version</strong>
PHP 8.5.8

<strong>$ composer --version</strong>
Composer 2.10.2

<strong>$ laravel --version</strong>
Laravel Installer 5.25.1</pre>
                    <p class="muted">All installed versions exceed the assignment minimums of PHP 8.2 and Composer 2.x.</p>
                </section>

                <section class="panel" aria-labelledby="installation-title">
                    <p class="section-label">Part 1 · Project setup</p>
                    <h2 id="installation-title">Official installer completed</h2>
                    <pre class="terminal"><strong>$ laravel new assignments/module7a</strong>
Installing laravel/laravel (v13.8.0)
Created project in assignments/module7a
Application key set successfully.
Preparing database.
Application ready in [assignments/module7a].</pre>
                    <p class="muted">The generated folder includes <code>artisan</code>, application bootstrap files, routes, Blade views, configuration, migrations, and PHPUnit tests.</p>
                </section>

                <section class="panel wide" aria-labelledby="structure-title">
                    <p class="section-label">Part 2</p>
                    <h2 id="structure-title">Laravel file structure</h2>
                    <div class="fact-grid">
                        <div class="fact"><h3>routes/web.php</h3><p class="muted">Defines browser routes and their responses.</p></div>
                        <div class="fact"><h3>resources/views/</h3><p class="muted">Stores Blade presentation templates.</p></div>
                        <div class="fact"><h3>app/Http/Controllers/</h3><p class="muted">Organizes request-handling classes as apps grow.</p></div>
                        <div class="fact"><h3>.env</h3><p class="muted">Holds local environment values and is excluded from Git.</p></div>
                    </div>
                </section>

                <section class="panel wide" aria-labelledby="knowledge-title">
                    <p class="section-label">Part 4 · Required knowledge check</p>
                    <h2 id="knowledge-title">What this assignment demonstrates</h2>
                    <div class="knowledge-grid">
                        <article class="answer">
                            <h3>What is Laravel and why is it useful?</h3>
                            <p class="muted">Laravel is a PHP framework that gives developers a clear structure for building web applications. Instead of creating every common feature from the beginning, I can use its built-in tools for routing, forms, databases, validation, and security. This helps me keep my code organized and spend more time working on the actual purpose of the application.</p>
                        </article>
                        <article class="answer">
                            <h3>What does /greet/{name} do?</h3>
                            <p class="muted">The <code>/greet/{name}</code> route reads the value placed after <code>/greet/</code> in the URL. Laravel passes that value into the route function as <code>$name</code>, and the function uses it to build a personal greeting. In my version, a hyphen separates first and last names, so <code>/greet/serge-hall</code> returns <code>Hello, Serge Hall!</code>.</p>
                        </article>
                        <article class="answer">
                            <h3>Why use Git and GitHub?</h3>
                            <p class="muted">Git saves a history of the changes I make while developing a project. If I make a mistake, I can compare versions, understand what changed, and return to an earlier working version when necessary. GitHub stores the repository online, provides a backup, and makes it easy to share my work with an instructor or collaborate with other developers.</p>
                        </article>
                        <article class="answer">
                            <h3>How is this project organized?</h3>
                            <p class="muted">The <code>assignments/module7a</code> folder is a complete standalone Laravel application with its own Artisan command, routes, Blade views, configuration, and tests. The main CS85 Laravel project also connects this assignment to the Module 7 roadmap. This app-inside-an-app structure lets me meet the assignment requirements while keeping all of my course projects together in one organized repository.</p>
                        </article>
                    </div>
                </section>

                <section class="panel wide" aria-labelledby="checklist-title">
                    <p class="section-label">Submission readiness</p>
                    <h2 id="checklist-title">Required evidence checklist</h2>
                    <ul class="checklist">
                        <li><span>PHP and Composer version evidence is documented.</span></li>
                        <li><span>The Laravel installer created a complete project folder.</span></li>
                        <li><span><code>/hello</code>, <code>/greet/alex</code>, and <code>/greet/samantha</code> return the required output.</span></li>
                        <li><span>The README contains setup steps and all three knowledge-check answers.</span></li>
                        <li><span>Automated feature tests verify static, dynamic, and invalid routes.</span></li>
                    </ul>
                </section>
            </div>
        </main>

        <footer>Built by Siarhei Hancharou for CS85 PHP Programming · Module 7A</footer>
    </div>

    <script>
        const greetForm = document.querySelector('[data-greet-form]');

        greetForm?.addEventListener('submit', (event) => {
            event.preventDefault();

            if (!greetForm.reportValidity()) {
                return;
            }

            const input = greetForm.elements.namedItem('name');
            const basePath = greetForm.dataset.basePath ?? '';

            if (input instanceof HTMLInputElement) {
                window.location.assign(`${basePath}/greet/${encodeURIComponent(input.value)}`);
            }
        });
    </script>
</body>
</html>
