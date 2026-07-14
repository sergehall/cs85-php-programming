<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="An instructor spotlight greeting created with a Laravel route parameter.">
    <meta name="theme-color" content="#111827">
    <title>Hello, {{ $displayName }}!</title>
    <style>
        :root {
            color-scheme: dark;
            --ink: #f8fafc;
            --muted: #cbd5e1;
            --night: #0b1120;
            --panel: rgba(15, 23, 42, .76);
            --coral: #fb735d;
            --gold: #f6c453;
            --teal: #4bd9c2;
            --line: rgba(255, 255, 255, .16);
        }

        * { box-sizing: border-box; }

        body {
            background:
                radial-gradient(circle at 18% 18%, rgba(251, 115, 93, .2), transparent 26rem),
                radial-gradient(circle at 82% 72%, rgba(75, 217, 194, .16), transparent 30rem),
                linear-gradient(145deg, #080d18 0%, #111827 48%, #151024 100%);
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            margin: 0;
            min-height: 100vh;
            overflow-x: hidden;
        }

        a:focus-visible { outline: .2rem solid var(--gold); outline-offset: .25rem; }

        .scene {
            display: grid;
            min-height: 100vh;
            overflow: hidden;
            padding: clamp(1rem, 4vw, 3rem);
            place-items: center;
            position: relative;
            isolation: isolate;
        }

        .orb {
            border-radius: 999px;
            filter: blur(.25rem);
            opacity: .8;
            pointer-events: none;
            position: absolute;
            z-index: -1;
        }

        .orb-one {
            animation: orbit-one 10s ease-in-out infinite alternate;
            background: linear-gradient(135deg, var(--coral), #9f3df0);
            height: clamp(13rem, 32vw, 27rem);
            left: -10rem;
            top: -8rem;
            width: clamp(13rem, 32vw, 27rem);
        }

        .orb-two {
            animation: orbit-two 12s ease-in-out infinite alternate;
            background: linear-gradient(135deg, var(--teal), #2563eb);
            bottom: -12rem;
            height: clamp(17rem, 38vw, 32rem);
            right: -13rem;
            width: clamp(17rem, 38vw, 32rem);
        }

        .presentation {
            animation: reveal .9s cubic-bezier(.2, .8, .2, 1) both;
            backdrop-filter: blur(1.5rem);
            background: linear-gradient(145deg, rgba(30, 41, 59, .82), rgba(15, 23, 42, .68));
            border: 1px solid var(--line);
            border-radius: clamp(1.5rem, 4vw, 3rem);
            box-shadow: 0 2.5rem 8rem rgba(0, 0, 0, .45);
            display: grid;
            gap: clamp(1.5rem, 4vw, 3rem);
            max-width: 68rem;
            overflow: hidden;
            padding: clamp(1.25rem, 5vw, 4.5rem);
            position: relative;
            width: 100%;
        }

        .presentation::after {
            animation: sheen 7s ease-in-out infinite;
            background: linear-gradient(110deg, transparent 35%, rgba(255, 255, 255, .08) 50%, transparent 65%);
            content: "";
            inset: 0;
            pointer-events: none;
            position: absolute;
            transform: translateX(-120%);
        }

        .topline {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
            justify-content: space-between;
            position: relative;
            z-index: 1;
        }

        .back-link,
        .route-pill {
            align-items: center;
            border: 1px solid var(--line);
            border-radius: 999px;
            display: inline-flex;
            font-size: .82rem;
            font-weight: 850;
            gap: .5rem;
            min-height: 2.7rem;
            padding: .55rem .85rem;
        }

        .back-link {
            color: var(--ink);
            text-decoration: none;
            transition: background-color .2s ease, border-color .2s ease, transform .2s ease;
        }

        .back-link:hover { background: rgba(255, 255, 255, .1); border-color: rgba(255, 255, 255, .35); transform: translateY(-.12rem); }
        .method { background: var(--teal); border-radius: 999px; color: #052e2b; padding: .2rem .45rem; }
        .route-pill code { color: var(--muted); }

        .spotlight {
            align-items: center;
            display: grid;
            gap: clamp(1.5rem, 4vw, 3rem);
            position: relative;
            z-index: 1;
        }

        .monogram {
            animation: float 4s ease-in-out infinite;
            background: linear-gradient(135deg, var(--coral), var(--gold));
            border: 1px solid rgba(255, 255, 255, .35);
            border-radius: 2rem;
            box-shadow: 0 1.5rem 4rem rgba(251, 115, 93, .28);
            color: #1a1020;
            display: grid;
            font-size: clamp(2.4rem, 9vw, 5.8rem);
            font-weight: 950;
            height: clamp(9rem, 24vw, 15rem);
            letter-spacing: -.08em;
            place-items: center;
            transform: rotate(-3deg);
            width: clamp(9rem, 24vw, 15rem);
        }

        .kicker {
            color: var(--gold);
            font-size: .76rem;
            font-weight: 950;
            letter-spacing: .16em;
            margin: 0 0 .6rem;
            text-transform: uppercase;
        }

        h1 {
            font-size: clamp(3rem, 10vw, 7.5rem);
            letter-spacing: -.07em;
            line-height: .82;
            margin: 0;
        }

        h1 span {
            background: linear-gradient(90deg, #fff, #ffd9a0 45%, #93f3e4);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .greeting {
            color: var(--teal);
            font-family: "SFMono-Regular", Consolas, "Liberation Mono", monospace;
            font-size: clamp(1rem, 3vw, 1.45rem);
            font-weight: 800;
            margin: 1.25rem 0 .65rem;
        }

        .tribute {
            color: var(--muted);
            font-size: clamp(1rem, 2vw, 1.18rem);
            margin: 0;
            max-width: 43rem;
        }

        .details {
            display: grid;
            gap: .75rem;
            position: relative;
            z-index: 1;
        }

        .detail {
            background: rgba(255, 255, 255, .055);
            border: 1px solid var(--line);
            border-radius: 1rem;
            padding: 1rem;
        }

        .detail span { color: var(--muted); display: block; font-size: .72rem; font-weight: 850; letter-spacing: .1em; text-transform: uppercase; }
        .detail strong { display: block; font-size: 1rem; margin-top: .2rem; }

        @media (min-width: 48rem) {
            .spotlight { grid-template-columns: auto minmax(0, 1fr); }
            .details { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        @keyframes reveal {
            from { opacity: 0; transform: translateY(2rem) scale(.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes float {
            0%, 100% { transform: rotate(-3deg) translateY(0); }
            50% { transform: rotate(2deg) translateY(-.7rem); }
        }

        @keyframes orbit-one {
            to { transform: translate(6rem, 4rem) scale(1.12); }
        }

        @keyframes orbit-two {
            to { transform: translate(-6rem, -3rem) scale(.9); }
        }

        @keyframes sheen {
            0%, 45% { transform: translateX(-120%); }
            75%, 100% { transform: translateX(120%); }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>
<body>
    <main class="scene">
        <span class="orb orb-one" aria-hidden="true"></span>
        <span class="orb orb-two" aria-hidden="true"></span>

        <section class="presentation" aria-labelledby="instructor-name">
            <div class="topline">
                <a class="back-link" href="{{ $assignmentBase ?: '/' }}">← Back to Hello Route</a>
                <div class="route-pill" aria-label="Laravel route">
                    <span class="method">GET</span>
                    <code>/greet/vicky-seno</code>
                </div>
            </div>

            <div class="spotlight">
                <div class="monogram" aria-hidden="true">VS</div>
                <div>
                    <p class="kicker">Instructor Spotlight</p>
                    <h1 id="instructor-name">Vicky <span>Seno</span></h1>
                    <p class="greeting">Hello, {{ $displayName }}!</p>
                    <p class="tribute">Thank you for guiding our Laravel journey and turning each new route into a practical step forward.</p>
                </div>
            </div>

            <div class="details" aria-label="Route presentation details">
                <div class="detail">
                    <span>Framework</span>
                    <strong>Laravel 13</strong>
                </div>
                <div class="detail">
                    <span>Route parameter</span>
                    <strong>vicky-seno</strong>
                </div>
                <div class="detail">
                    <span>Formatted response</span>
                    <strong>{{ $displayName }}</strong>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
