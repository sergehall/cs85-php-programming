<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Module 2B - Cosmic Calendar</title>
        <style>
            :root {
                --ink: #101828;
                --graphite: #344054;
                --paper: #ffffff;
                --cloud: #f8fafc;
                --line: rgba(16, 24, 40, 0.12);
                --teal: #14b8a6;
                --gold: #f5b841;
                --coral: #f9736b;
                --violet: #8b5cf6;
                --night: #17202a;
            }

            * {
                box-sizing: border-box;
            }

            body {
                min-height: 100vh;
                margin: 0;
                padding: 28px;
                color: var(--ink);
                font-family:
                    Inter,
                    ui-sans-serif,
                    system-ui,
                    -apple-system,
                    BlinkMacSystemFont,
                    'Segoe UI',
                    sans-serif;
                background:
                    radial-gradient(circle at 18% 12%, rgba(20, 184, 166, 0.2), transparent 28%),
                    radial-gradient(circle at 82% 10%, rgba(245, 184, 65, 0.22), transparent 26%),
                    linear-gradient(135deg, #eef7fb 0%, #f8fbf7 48%, #fff5ee 100%);
            }

            a {
                color: inherit;
                text-decoration: none;
            }

            .page-shell {
                width: min(100%, 1080px);
                margin: 0 auto;
            }

            .topbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 18px;
                margin-bottom: 22px;
            }

            .brand {
                display: flex;
                align-items: center;
                gap: 12px;
                font-weight: 900;
            }

            .brand-mark {
                width: 42px;
                height: 42px;
                display: grid;
                place-items: center;
                border-radius: 8px;
                color: #ffffff;
                background: linear-gradient(135deg, var(--ink), #0f766e);
                box-shadow: 0 12px 28px rgba(23, 32, 42, 0.16);
            }

            .nav-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                justify-content: flex-end;
            }

            .nav-button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 40px;
                padding: 0 14px;
                border: 1px solid var(--line);
                border-radius: 8px;
                background: rgba(255, 255, 255, 0.84);
                color: var(--graphite);
                font-size: 0.92rem;
                font-weight: 800;
            }

            .container {
                overflow: hidden;
                border: 1px solid var(--line);
                border-radius: 8px;
                background: rgba(255, 255, 255, 0.94);
                box-shadow: 0 24px 60px rgba(23, 32, 42, 0.14);
            }

            .hero {
                padding: 34px 34px 28px;
                color: var(--paper);
                background: linear-gradient(135deg, rgba(16, 24, 40, 0.96), rgba(15, 118, 110, 0.9)), var(--night);
            }

            .eyebrow {
                margin: 0 0 10px;
                color: var(--gold);
                font-size: 0.78rem;
                font-weight: 800;
                letter-spacing: 0;
                text-transform: uppercase;
            }

            h1 {
                margin: 0;
                font-size: clamp(2.2rem, 7vw, 4.4rem);
                line-height: 1.02;
            }

            .subtitle {
                max-width: 680px;
                margin: 16px 0 0;
                color: rgba(255, 255, 255, 0.82);
                font-size: 1.02rem;
                line-height: 1.6;
            }

            .meta {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 10px;
                padding: 18px 34px;
                border-bottom: 1px solid var(--line);
                background: var(--cloud);
            }

            .meta-box {
                min-width: 0;
                padding: 13px;
                border: 1px solid var(--line);
                border-radius: 8px;
                background: var(--paper);
            }

            .meta-box span {
                display: block;
                color: var(--graphite);
                font-size: 0.74rem;
                font-weight: 800;
                text-transform: uppercase;
            }

            .meta-box strong {
                display: block;
                margin-top: 5px;
                overflow-wrap: anywhere;
                font-size: 1rem;
            }

            .meta-box small {
                display: block;
                margin-top: 5px;
                color: var(--graphite);
                font-size: 0.74rem;
                font-weight: 700;
                line-height: 1.35;
            }

            .calendar-wrap {
                padding: 30px 34px 34px;
            }

            .explanation {
                margin: 0 0 22px;
                padding: 14px 16px;
                border: 1px solid rgba(20, 184, 166, 0.28);
                border-radius: 8px;
                color: var(--graphite);
                background: #f0fdfa;
                font-weight: 700;
                line-height: 1.55;
            }

            .legend {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-bottom: 24px;
            }

            .legend-item {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 9px 11px;
                border: 1px solid var(--line);
                border-radius: 8px;
                color: var(--graphite);
                background: var(--paper);
                font-size: 0.88rem;
                font-weight: 700;
            }

            .legend-swatch {
                width: 16px;
                height: 16px;
                border-radius: 4px;
                background: #e2e8f0;
            }

            .legend-swatch.name {
                background: var(--violet);
            }

            .legend-swatch.month {
                border: 2px solid var(--gold);
                background: #e2e8f0;
            }

            .legend-swatch.both {
                border: 2px solid var(--gold);
                background: var(--coral);
            }

            .calendar-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(58px, 1fr));
                gap: 10px;
            }

            .day-box {
                min-height: 58px;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(16, 24, 40, 0.1);
                border-radius: 8px;
                color: var(--ink);
                background: #eef2f7;
                font-size: 1.05rem;
                font-weight: 800;
            }

            .cosmic-name {
                color: #ffffff;
                background: var(--violet);
                box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.16);
            }

            .cosmic-month {
                border: 2px solid var(--gold);
                background: #fff8e1;
            }

            .cosmic-both {
                color: #ffffff;
                border: 2px solid var(--gold);
                background: var(--coral);
                box-shadow: 0 0 0 3px rgba(249, 115, 107, 0.16);
            }

            .notice {
                margin: 22px 0 0;
                padding: 14px 16px;
                border: 1px solid rgba(245, 184, 65, 0.42);
                border-radius: 8px;
                color: #8a5a00;
                background: #fff8e1;
                font-weight: 700;
            }

            @media (max-width: 760px) {
                body {
                    padding: 18px;
                }

                .topbar {
                    align-items: stretch;
                    flex-direction: column;
                }

                .nav-actions {
                    justify-content: flex-start;
                }

                .hero,
                .meta,
                .calendar-wrap {
                    padding-left: 20px;
                    padding-right: 20px;
                }

                .meta {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media (max-width: 480px) {
                .meta {
                    grid-template-columns: 1fr;
                }

                .calendar-grid {
                    grid-template-columns: repeat(auto-fill, minmax(48px, 1fr));
                }

                .day-box {
                    min-height: 48px;
                }
            }
        </style>
    </head>
    <body>
        <div class="page-shell">
            <nav class="topbar" aria-label="Primary navigation">
                <a class="brand" href="{{ route('home') }}">
                    <span class="brand-mark">85</span>
                    <span>CS85 PHP Programming</span>
                </a>

                <div class="nav-actions">
                    <a class="nav-button" href="{{ route('home') }}">Home</a>
                    <a class="nav-button" href="{{ route('roadmap.module', 'module-2') }}">Module 2</a>
                    <a class="nav-button" href="{{ route('roadmap') }}">Roadmap</a>
                </div>
            </nav>

            <div class="container">
                <header class="hero">
                    <p class="eyebrow">Module 2B - Time Loops</p>
                    <h1>Cosmic Day Number Calendar</h1>
                    <p class="subtitle">
                        This Laravel page lists day numbers from my name length to today's day of year. Conditional
                        logic highlights every number that matches the name, month, or both rules.
                    </p>
                </header>

                <section class="meta" aria-label="Calendar data">
                    <div class="meta-box">
                        <span>First Name</span>
                        <strong>{{ $firstName }}</strong>
                    </div>
                    <div class="meta-box">
                        <span>Name Length</span>
                        <strong>{{ $nameLength }}</strong>
                    </div>
                    <div class="meta-box">
                        <span>Day of Year</span>
                        <strong>{{ $dayOfYear }}</strong>
                        <small>Today counted as a day number in the current year.</small>
                    </div>
                    <div class="meta-box">
                        <span>Current Month</span>
                        <strong>{{ $currentMonth }}</strong>
                        <small>{{ $dateTime }}</small>
                    </div>
                </section>

                <main class="calendar-wrap">
                    <p class="explanation">
                        This is a day-number calendar, not a month grid. The loop starts at {{ $nameLength }} because
                        "{{ $firstName }}" has {{ $nameLength }} letters, then it counts up to day {{ $dayOfYear }} of
                        the year.
                    </p>

                    <section class="legend" aria-label="Cosmic number legend">
                        <div class="legend-item"><span class="legend-swatch name"></span>Divisible by name length</div>
                        <div class="legend-item"><span class="legend-swatch month"></span>Divisible by month</div>
                        <div class="legend-item"><span class="legend-swatch both"></span>Divisible by both</div>
                        <div class="legend-item"><span class="legend-swatch"></span>Regular day number</div>
                    </section>

                    <div class="calendar-grid">
                        @foreach ($days as $day)
                            <div class="{{ $day['cssClass'] }}" title="{{ $day['rule'] }}">
                                {{ $day['number'] }}
                            </div>
                        @endforeach
                    </div>

                    @if ($usedFallback)
                        <p class="notice">
                            The live API was unavailable, so this page used the local America/Los_Angeles date as a
                            fallback.
                        </p>
                    @endif
                </main>
            </div>
        </div>
    </body>
</html>
