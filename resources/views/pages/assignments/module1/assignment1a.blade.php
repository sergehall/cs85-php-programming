<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Assignment 1A - Laravel Hello World</title>
        <style>
            :root {
                --color-ink: #17202a;
                --color-graphite: #2d3748;
                --color-sky: #0ea5e9;
                --color-teal: #14b8a6;
                --color-gold: #f5b841;
                --color-coral: #f9736b;
                --color-cloud: #f7fafc;
                --color-white: #ffffff;
                --line: rgba(45, 55, 72, 0.12);
            }

            * {
                box-sizing: border-box;
            }

            body {
                min-height: 100vh;
                margin: 0;
                display: grid;
                place-items: center;
                padding: 32px;
                color: var(--color-ink);
                font-family:
                    Inter,
                    ui-sans-serif,
                    system-ui,
                    -apple-system,
                    BlinkMacSystemFont,
                    'Segoe UI',
                    sans-serif;
                background:
                    radial-gradient(circle at 18% 18%, rgba(20, 184, 166, 0.18), transparent 28%),
                    radial-gradient(circle at 82% 14%, rgba(245, 184, 65, 0.2), transparent 26%),
                    linear-gradient(135deg, #eef7fb 0%, #f8fbf7 48%, #fff5ee 100%);
            }

            main {
                width: min(100%, 760px);
                overflow: hidden;
                border: 1px solid var(--line);
                border-radius: 8px;
                background: rgba(255, 255, 255, 0.92);
                box-shadow: 0 24px 60px rgba(23, 32, 42, 0.14);
            }

            .assignment-header {
                padding: 30px 34px 26px;
                color: var(--color-white);
                background: linear-gradient(135deg, var(--color-ink), #0f766e);
            }

            .nav-links {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-bottom: 22px;
            }

            .top-link {
                display: inline-flex;
                align-items: center;
                min-height: 38px;
                padding: 0 12px;
                border: 1px solid rgba(255, 255, 255, 0.22);
                border-radius: 8px;
                color: var(--color-white);
                font-size: 0.9rem;
                font-weight: 800;
                text-decoration: none;
            }

            .eyebrow {
                margin: 0 0 12px;
                color: var(--color-gold);
                font-size: 0.78rem;
                font-weight: 800;
                letter-spacing: 0;
                text-transform: uppercase;
            }

            .message {
                margin: 0;
                color: rgba(255, 255, 255, 0.82);
                font-size: clamp(1.15rem, 2.5vw, 1.45rem);
                line-height: 1.55;
            }

            .message strong {
                display: block;
                color: var(--color-white);
                font-size: clamp(2.25rem, 6vw, 4.2rem);
                font-weight: 800;
                line-height: 1.05;
            }

            .message span {
                display: block;
                margin-top: 18px;
            }

            .palette {
                display: grid;
                grid-template-columns: repeat(5, minmax(0, 1fr));
                gap: 12px;
                padding: 28px 34px 34px;
                background: var(--color-white);
            }

            .swatch {
                min-height: 92px;
                overflow: hidden;
                border: 1px solid rgba(23, 32, 42, 0.12);
                border-radius: 8px;
                background: var(--swatch-color);
            }

            .swatch span {
                display: block;
                min-height: 36px;
                margin-top: 56px;
                padding: 8px;
                color: var(--color-ink);
                font-size: 0.78rem;
                font-weight: 700;
                background: rgba(255, 255, 255, 0.86);
            }

            @media (max-width: 620px) {
                body {
                    padding: 20px;
                }

                .assignment-header,
                .palette {
                    padding-left: 24px;
                    padding-right: 24px;
                }

                .palette {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }
        </style>
    </head>
    <body>
        <main aria-label="Assignment 1A Laravel welcome screen">
            <section class="assignment-header">
                <nav class="nav-links" aria-label="Assignment navigation">
                    <a class="top-link" href="{{ route('roadmap.module', 'module-1') }}">Back to Module 1</a>
                    <a class="top-link" href="{{ route('roadmap') }}">Roadmap</a>
                </nav>
                <p class="eyebrow">Module 1 - Assignment 1A</p>
                <p class="message">
                    <strong>Hello World from Laravel Herd!</strong>
                    <span>My first Laravel route is now rendering a custom Blade page.</span>
                </p>
            </section>

            <section class="palette" aria-label="Brand color palette">
                <div class="swatch" style="--swatch-color: var(--color-ink)">
                    <span>Ink</span>
                </div>
                <div class="swatch" style="--swatch-color: var(--color-sky)">
                    <span>Sky</span>
                </div>
                <div class="swatch" style="--swatch-color: var(--color-teal)">
                    <span>Teal</span>
                </div>
                <div class="swatch" style="--swatch-color: var(--color-gold)">
                    <span>Gold</span>
                </div>
                <div class="swatch" style="--swatch-color: var(--color-coral)">
                    <span>Coral</span>
                </div>
            </section>
        </main>
    </body>
</html>
