{{--
    Master layout shared by all four pages.
    Child templates provide the document title and main content through
    @section, while this file owns navigation, styles, and page structure.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A four-page personal Laravel website demonstrating basic routing, controllers, Blade layouts, and named navigation.">
    <title>@yield('title', 'Siarhei · Personal Route Lab')</title>
    <style>
        :root {
            color-scheme: light;
            --canvas: #f4f1ea;
            --surface: rgba(255, 255, 255, .84);
            --surface-solid: #fffefa;
            --ink: #17202a;
            --muted: #68717d;
            --line: #d9d4c9;
            --indigo: #343270;
            --indigo-deep: #242250;
            --aqua: #53c8bd;
            --coral: #ef684e;
            --gold: #d5a33f;
            --shadow: 0 1.5rem 4rem rgba(31, 37, 49, .12);
        }

        *, *::before, *::after { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            min-height: 100vh;
            margin: 0;
            background:
                radial-gradient(circle at 8% 8%, rgba(83, 200, 189, .18), transparent 23rem),
                radial-gradient(circle at 94% 5%, rgba(239, 104, 78, .15), transparent 25rem),
                var(--canvas);
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            line-height: 1.65;
        }

        a { color: inherit; }

        a:focus-visible,
        button:focus-visible {
            outline: .2rem solid var(--aqua);
            outline-offset: .22rem;
        }

        .skip-link {
            position: fixed;
            top: .75rem;
            left: .75rem;
            z-index: 20;
            transform: translateY(-180%);
            border-radius: 999px;
            background: var(--ink);
            color: white;
            padding: .7rem 1rem;
            font-weight: 800;
            transition: transform .2s ease;
        }

        .skip-link:focus { transform: translateY(0); }

        .site-shell {
            width: min(100% - 1.5rem, 76rem);
            margin-inline: auto;
            padding-block: .75rem 2rem;
        }

        .site-nav {
            position: sticky;
            top: .75rem;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            border: 1px solid rgba(255, 255, 255, .72);
            border-radius: 1.25rem;
            background: rgba(23, 32, 42, .94);
            box-shadow: 0 .8rem 2.3rem rgba(23, 32, 42, .2);
            color: white;
            padding: .7rem .8rem .7rem 1rem;
            backdrop-filter: blur(1rem);
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: .65rem;
            text-decoration: none;
            font-weight: 900;
            letter-spacing: -.025em;
            white-space: nowrap;
        }

        .brand-mark {
            display: grid;
            width: 2.2rem;
            aspect-ratio: 1;
            place-items: center;
            border-radius: .7rem;
            background: linear-gradient(135deg, var(--aqua), #a8eee6);
            color: var(--indigo-deep);
            font-size: .76rem;
            letter-spacing: .04em;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: .35rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .nav-link {
            display: inline-flex;
            min-height: 2.75rem;
            align-items: center;
            border-radius: .85rem;
            padding: .6rem .9rem;
            color: #dce2e7;
            font-size: .91rem;
            font-weight: 800;
            text-decoration: none;
            transition: background-color .2s ease, color .2s ease, transform .2s ease;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, .1);
            color: white;
            transform: translateY(-1px);
        }

        .nav-link.active {
            background: linear-gradient(135deg, var(--coral), #f08a60);
            color: white;
        }

        .course-return {
            display: inline-flex;
            margin-top: 1rem;
            align-items: center;
            gap: .45rem;
            color: var(--indigo);
            font-size: .88rem;
            font-weight: 850;
            text-decoration-thickness: .1rem;
            text-underline-offset: .2rem;
        }

        main { padding-top: clamp(2rem, 5vw, 4.5rem); }

        .page-grid {
            display: grid;
            gap: 1rem;
        }

        .eyebrow {
            margin: 0 0 .7rem;
            color: var(--coral);
            font-size: .76rem;
            font-weight: 950;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        h1, h2, h3 {
            margin-top: 0;
            font-family: Georgia, "Times New Roman", serif;
            line-height: 1.08;
            letter-spacing: -.035em;
        }

        h1 {
            max-width: 14ch;
            margin-bottom: 1rem;
            font-size: clamp(2.7rem, 8vw, 6.8rem);
        }

        h2 { font-size: clamp(1.65rem, 4vw, 2.7rem); }
        h3 { font-size: 1.35rem; }

        .lede {
            max-width: 47rem;
            margin: 0;
            color: var(--muted);
            font-size: clamp(1.05rem, 2.1vw, 1.28rem);
        }

        .hero {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 2rem;
            background: var(--surface);
            box-shadow: var(--shadow);
            padding: clamp(1.4rem, 5vw, 4rem);
        }

        .hero::after {
            position: absolute;
            right: -6rem;
            bottom: -8rem;
            width: 20rem;
            aspect-ratio: 1;
            border: 4rem solid rgba(83, 200, 189, .14);
            border-radius: 50%;
            content: "";
            pointer-events: none;
        }

        .hero-copy { position: relative; z-index: 1; }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: .7rem;
            margin-top: 1.55rem;
        }

        .button {
            display: inline-flex;
            min-height: 3rem;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--indigo);
            border-radius: 999px;
            background: var(--indigo);
            color: white;
            padding: .7rem 1.2rem;
            font-weight: 900;
            text-decoration: none;
            transition: transform .2s ease, box-shadow .2s ease, background-color .2s ease;
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 .7rem 1.5rem rgba(52, 50, 112, .22);
        }

        .button-secondary {
            background: transparent;
            color: var(--indigo);
        }

        .panel-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 15rem), 1fr));
            gap: 1rem;
        }

        .panel,
        .hobby-card,
        .profile-card,
        .detail-card {
            border: 1px solid var(--line);
            border-radius: 1.5rem;
            background: var(--surface-solid);
            box-shadow: 0 .8rem 2rem rgba(31, 37, 49, .07);
            padding: clamp(1.15rem, 3vw, 1.7rem);
        }

        .panel-kicker,
        .card-number {
            display: inline-flex;
            margin-bottom: 1.1rem;
            border-radius: 999px;
            background: rgba(83, 200, 189, .18);
            color: var(--indigo-deep);
            padding: .35rem .62rem;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: .75rem;
            font-weight: 900;
        }

        .panel p,
        .hobby-card p,
        .detail-card p { color: var(--muted); }

        .route-code {
            display: block;
            overflow-wrap: anywhere;
            border-radius: .85rem;
            background: #17202a;
            color: #c7f7f0;
            padding: .78rem .9rem;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: .86rem;
        }

        .section-header {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.4rem;
        }

        .section-header h2 { margin-bottom: 0; }

        .profile-layout {
            display: grid;
            gap: 1rem;
        }

        .profile-list {
            display: grid;
            gap: .8rem;
            margin: 0;
        }

        .profile-list div {
            display: grid;
            gap: .1rem;
            border-bottom: 1px solid var(--line);
            padding-bottom: .75rem;
        }

        .profile-list div:last-child { border-bottom: 0; padding-bottom: 0; }
        .profile-list dt { color: var(--muted); font-size: .76rem; font-weight: 900; letter-spacing: .1em; text-transform: uppercase; }
        .profile-list dd { margin: 0; font-size: 1.05rem; font-weight: 850; }

        .project-showcase {
            margin-top: clamp(1rem, 4vw, 2.5rem);
            border-top: 1px solid var(--line);
            padding-top: clamp(2rem, 5vw, 4rem);
        }

        .project-grid {
            display: grid;
            gap: 1rem;
        }

        .project-card {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 1.5rem;
            background: var(--surface-solid);
            box-shadow: 0 .8rem 2rem rgba(31, 37, 49, .07);
            padding: clamp(1.2rem, 3vw, 1.8rem);
        }

        .project-card::before {
            position: absolute;
            inset: 0 auto 0 0;
            width: .3rem;
            background: var(--aqua);
            content: "";
        }

        .project-card-lens::before { background: var(--indigo); }
        .project-card-photo::before { background: var(--coral); }

        .project-type {
            margin: 0 0 .75rem;
            color: var(--muted);
            font-size: .72rem;
            font-weight: 950;
            letter-spacing: .11em;
            text-transform: uppercase;
        }

        .project-card h3 { margin-bottom: .75rem; font-size: clamp(1.35rem, 3vw, 1.8rem); }

        .project-card h3 a {
            color: var(--indigo);
            text-decoration-thickness: .09rem;
            text-underline-offset: .22rem;
        }

        .project-card p:last-child { margin-bottom: 0; }

        .tech-list {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
            margin: 1rem 0;
            padding: 0;
            list-style: none;
        }

        .tech-list li {
            border: 1px solid rgba(52, 50, 112, .22);
            border-radius: 999px;
            background: rgba(52, 50, 112, .07);
            color: var(--indigo-deep);
            padding: .3rem .68rem;
            font-size: .76rem;
            font-weight: 900;
        }

        .connect-card {
            display: grid;
            gap: 1.25rem;
            margin-top: 1rem;
            align-items: center;
            border-radius: 1.5rem;
            background:
                radial-gradient(circle at 90% 10%, rgba(83, 200, 189, .22), transparent 13rem),
                var(--ink);
            box-shadow: var(--shadow);
            color: white;
            padding: clamp(1.35rem, 4vw, 2.3rem);
        }

        .connect-card h2 { margin-bottom: .55rem; }
        .connect-card p:not(.eyebrow) { max-width: 50rem; margin-bottom: 0; color: #cbd3da; }
        .connect-card .button { border-color: var(--aqua); background: var(--aqua); color: var(--indigo-deep); }

        .hobby-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 19rem), 1fr));
            gap: 1rem;
        }

        .hobby-card {
            display: flex;
            min-height: 21rem;
            flex-direction: column;
            transition: border-color .2s ease, transform .2s ease, box-shadow .2s ease;
        }

        .hobby-card:hover {
            transform: translateY(-.3rem);
            border-color: rgba(52, 50, 112, .45);
            box-shadow: 0 1.3rem 2.6rem rgba(31, 37, 49, .12);
        }

        .hobby-card .eyebrow { color: var(--indigo); }
        .hobby-card h2 { margin-bottom: .7rem; font-size: 2rem; }
        .hobby-card .card-link { margin-top: auto; }

        .card-link {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            color: var(--indigo);
            font-weight: 900;
            text-decoration-thickness: .1rem;
            text-underline-offset: .25rem;
        }

        .detail-wrap {
            display: grid;
            gap: 1rem;
        }

        .detail-card h2 { margin-top: 1.8rem; margin-bottom: .6rem; }
        .detail-card h2:first-of-type { margin-top: 0; }

        .route-facts {
            display: grid;
            gap: .75rem;
            align-content: start;
        }

        .fact {
            border-left: .25rem solid var(--aqua);
            border-radius: 0 1rem 1rem 0;
            background: rgba(255, 255, 255, .72);
            padding: .85rem 1rem;
        }

        .fact span { display: block; color: var(--muted); font-size: .72rem; font-weight: 900; letter-spacing: .09em; text-transform: uppercase; }
        .fact strong { display: block; margin-top: .2rem; overflow-wrap: anywhere; }

        .site-footer {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: .8rem;
            margin-top: 2rem;
            border-top: 1px solid var(--line);
            padding: 1.4rem .2rem 0;
            color: var(--muted);
            font-size: .83rem;
        }

        @media (min-width: 48rem) {
            .site-shell { width: min(100% - 3rem, 76rem); padding-top: 1.25rem; }
            .profile-layout { grid-template-columns: minmax(0, 1.35fr) minmax(17rem, .65fr); }
            .detail-wrap { grid-template-columns: minmax(0, 1.45fr) minmax(16rem, .55fr); }
            .project-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .project-card-lens { grid-row: span 2; }
            .connect-card { grid-template-columns: minmax(0, 1fr) auto; }
        }

        @media (max-width: 39.99rem) {
            .site-nav { align-items: flex-start; flex-direction: column; }
            .nav-links { width: 100%; }
            .nav-links li { flex: 1; }
            .nav-link { justify-content: center; padding-inline: .45rem; }
            .section-header { align-items: flex-start; flex-direction: column; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>
<body>
    <a class="skip-link" href="#main-content">Skip to main content</a>

    <div class="site-shell">
        <nav class="site-nav" aria-label="Primary navigation">
            <a class="brand" href="{{ route($routePrefix.'home') }}">
                <span class="brand-mark" aria-hidden="true">7B</span>
                <span>Route Lab</span>
            </a>

            <ul class="nav-links">
                <li>
                    <a
                        class="nav-link {{ request()->routeIs($routePrefix.'home') ? 'active' : '' }}"
                        href="{{ route($routePrefix.'home') }}"
                        @if (request()->routeIs($routePrefix.'home')) aria-current="page" @endif
                    >Home</a>
                </li>
                <li>
                    <a
                        class="nav-link {{ request()->routeIs($routePrefix.'about') ? 'active' : '' }}"
                        href="{{ route($routePrefix.'about') }}"
                        @if (request()->routeIs($routePrefix.'about')) aria-current="page" @endif
                    >About</a>
                </li>
                <li>
                    <a
                        class="nav-link {{ request()->routeIs($routePrefix.'hobbies.*') ? 'active' : '' }}"
                        href="{{ route($routePrefix.'hobbies.index') }}"
                        @if (request()->routeIs($routePrefix.'hobbies.*')) aria-current="page" @endif
                    >Hobbies</a>
                </li>
            </ul>
        </nav>

        @if ($embedded)
            <a class="course-return" href="{{ route('roadmap.module', 'module-7') }}">← Return to the Module 7 roadmap</a>
        @endif

        <main id="main-content" tabindex="-1">
            @yield('content')
        </main>

        <footer class="site-footer">
            <span>CS 85 · Module 7 Assignment 7B</span>
            <span>Closure routes · Controller routes · Blade</span>
        </footer>
    </div>
</body>
</html>
