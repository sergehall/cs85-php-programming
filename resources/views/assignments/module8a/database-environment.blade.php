@extends('layouts.app')

@section('content')
    <section class="overflow-hidden rounded-3xl border border-sky-200 bg-slate-950 text-white shadow-2xl shadow-sky-950/15">
        <div class="grid gap-8 p-6 sm:p-8 lg:grid-cols-[1.25fr_.75fr] lg:p-12">
            <div>
                <a class="mb-8 inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-bold no-underline transition hover:bg-white/15" href="{{ route('roadmap.module', 'module-8') }}">
                    ← Return to Module 8 roadmap
                </a>
                <p class="mb-3 text-sm font-black uppercase tracking-[.18em] text-sky-300">Module 8 · Assignment 8A</p>
                <h1 class="max-w-4xl text-4xl leading-tight font-black tracking-tight sm:text-6xl">Laravel with a database environment</h1>
                <p class="mt-5 max-w-3xl text-lg leading-8 text-slate-300">
                    An interactive MySQL readiness lab built inside the main CS85 Laravel project. It demonstrates the
                    environment values Laravel needs, tests a local connection, and displays a real read-only query result.
                </p>
            </div>

            <aside class="self-end rounded-2xl border border-sky-300/25 bg-sky-300/10 p-5" aria-label="Environment summary">
                <p class="text-xs font-black uppercase tracking-[.14em] text-sky-300">Current toolchain</p>
                <dl class="mt-4 grid gap-3 text-sm">
                    <div class="flex justify-between gap-4 border-b border-white/10 pb-3"><dt class="text-slate-400">PHP</dt><dd class="font-bold">{{ PHP_VERSION }}</dd></div>
                    <div class="flex justify-between gap-4 border-b border-white/10 pb-3"><dt class="text-slate-400">Laravel</dt><dd class="font-bold">{{ app()->version() }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-slate-400">PDO MySQL</dt><dd class="font-bold text-emerald-300">{{ extension_loaded('pdo_mysql') ? 'Available' : 'Missing' }}</dd></div>
                </dl>
            </aside>
        </div>
    </section>

    <section class="grid gap-4 lg:grid-cols-3" aria-labelledby="workflow-title">
        <div class="lg:col-span-3">
            <p class="text-sm font-black uppercase tracking-[.14em] text-sky-700">Assignment workflow</p>
            <h2 id="workflow-title" class="mt-2 text-3xl font-black tracking-tight">From environment to migration</h2>
        </div>

        @foreach ([
            ['01', 'Start MySQL', 'Start the cs85-mysql Docker container and confirm it is healthy on local port 3307.'],
            ['02', 'Configure Laravel', 'Use the sanitized environment template and keep the real password out of Git.'],
            ['03', 'Run migrations', 'Create orm_practice_db, run php artisan migrate, and capture the successful output.'],
        ] as [$number, $heading, $copy])
            <article class="rounded-2xl border border-stone-300 bg-white p-5 shadow-lg shadow-slate-900/5">
                <span class="inline-grid h-10 w-10 place-items-center rounded-xl bg-sky-100 font-black text-sky-800">{{ $number }}</span>
                <h3 class="mt-5 text-xl font-black">{{ $heading }}</h3>
                <p class="mt-2 leading-7 text-slate-600">{{ $copy }}</p>
            </article>
        @endforeach
    </section>

    <section class="grid gap-4 xl:grid-cols-[.88fr_1.12fr]" aria-labelledby="connection-title">
        <article class="rounded-3xl border border-stone-300 bg-white p-6 shadow-lg shadow-slate-900/5 sm:p-8">
            <p class="text-sm font-black uppercase tracking-[.14em] text-orange-700">Interactive connection check</p>
            <h2 id="connection-title" class="mt-2 text-3xl font-black tracking-tight">Test local MySQL</h2>
            <p class="mt-3 leading-7 text-slate-600">
                These values are used for this request only. The password is never redisplayed, written to a file, or stored in the session.
            </p>

            <form class="mt-6 grid gap-5" method="POST" action="{{ route('assignments.module8a.database.test') }}">
                @csrf

                <label class="grid gap-2 font-bold">
                    Local host
                    <select class="rounded-xl border border-stone-300 bg-white px-4 py-3" name="host">
                        @foreach (['127.0.0.1', 'localhost'] as $host)
                            <option value="{{ $host }}" @selected(old('host', $defaults['host']) === $host)>{{ $host }}</option>
                        @endforeach
                    </select>
                    @error('host') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                </label>

                <div class="grid min-w-0 gap-5">
                    <label class="grid gap-2 font-bold">
                        Port
                        <input class="min-w-0 w-full rounded-xl border border-stone-300 px-4 py-3" name="port" inputmode="numeric" placeholder="3307" value="{{ old('port', $defaults['port']) }}" required>
                        @error('port') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-2 font-bold">
                        Username
                        <input class="min-w-0 w-full rounded-xl border border-stone-300 px-4 py-3" name="username" autocomplete="username" placeholder="module8a" value="{{ old('username', $defaults['username']) }}" required>
                        @error('username') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                    </label>
                </div>

                <label class="grid gap-2 font-bold">
                    Database
                    <input class="rounded-xl border border-stone-300 px-4 py-3 font-mono" name="database" placeholder="orm_practice_db" value="{{ old('database', $defaults['database']) }}" required>
                    @error('database') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                </label>

                <label class="grid gap-2 font-bold">
                    Password <span class="text-sm font-normal text-slate-500">Leave blank to use the protected Module 8A value from the local <code>.env</code></span>
                    <input class="rounded-xl border border-stone-300 px-4 py-3" type="password" name="password" autocomplete="current-password" placeholder="Uses local .env when blank">
                    @error('password') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                </label>

                <button class="min-h-12 rounded-xl bg-sky-700 px-5 py-3 font-black text-white transition hover:-translate-y-0.5 hover:bg-sky-800 focus:outline-3 focus:outline-offset-2 focus:outline-sky-600" type="submit">
                    Run connection query
                </button>
            </form>
        </article>

        <div class="grid content-start gap-4">
            <article class="overflow-hidden rounded-3xl border border-slate-700 bg-slate-950 text-slate-100 shadow-xl shadow-slate-950/10">
                <header class="flex items-center justify-between gap-3 border-b border-slate-700 px-5 py-4">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[.14em] text-sky-300">Read-only SQL</p>
                        <h2 class="mt-1 text-xl font-black">Connection diagnostics query</h2>
                    </div>
                    <span class="rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-black text-emerald-300">SELECT only</span>
                </header>
                <pre class="overflow-x-auto p-5 text-sm leading-7 text-sky-100"><code>{{ $connectionQuery }}</code></pre>
            </article>

            @if ($connectionResult)
                <article class="rounded-3xl border p-6 {{ $connectionResult['connected'] ? 'border-emerald-300 bg-emerald-50' : 'border-red-300 bg-red-50' }}" aria-live="polite">
                    <p class="text-sm font-black uppercase tracking-[.14em] {{ $connectionResult['connected'] ? 'text-emerald-800' : 'text-red-800' }}">
                        {{ $connectionResult['connected'] ? 'Connection successful' : 'Connection needs attention' }}
                    </p>
                    <h2 class="mt-2 text-2xl font-black">{{ $connectionResult['message'] }}</h2>

                    @if ($connectionResult['connected'] && $connectionResult['details'])
                        <dl class="mt-5 grid gap-3 sm:grid-cols-2">
                            @foreach ([
                                'database_name' => 'Database',
                                'mysql_version' => 'MySQL version',
                                'connected_user' => 'Connected user',
                                'server_time' => 'Server time',
                                'table_count' => 'Tables after migration',
                            ] as $key => $label)
                                <div class="rounded-xl border border-emerald-200 bg-white/70 p-4">
                                    <dt class="text-xs font-black uppercase tracking-[.1em] text-emerald-800">{{ $label }}</dt>
                                    <dd class="mt-1 break-words font-mono font-bold">{{ $connectionResult['details'][$key] ?? 'Unavailable' }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    @else
                        <p class="mt-3 text-slate-700">The submitted password was not stored. Check the Docker service or form values and safely try again.</p>
                    @endif
                </article>
            @else
                <article class="rounded-3xl border border-stone-300 bg-stone-100 p-6">
                    <p class="text-sm font-black uppercase tracking-[.14em] text-slate-500">Waiting for a request</p>
                    <h2 class="mt-2 text-2xl font-black">The query result will appear here</h2>
                    <p class="mt-3 leading-7 text-slate-600">Start <code>cs85-mysql</code> and use the form to confirm Laravel can reach <code>orm_practice_db</code> through port <code>3307</code>.</p>
                </article>
            @endif
        </div>
    </section>

    <section class="grid gap-4 lg:grid-cols-2" aria-labelledby="environment-title">
        <article class="rounded-3xl border border-stone-300 bg-white p-6 sm:p-8">
            <p class="text-sm font-black uppercase tracking-[.14em] text-teal-700">Sanitized configuration</p>
            <h2 id="environment-title" class="mt-2 text-3xl font-black">Module-specific <code>.env</code> values</h2>
            <pre class="mt-5 overflow-x-auto rounded-2xl bg-slate-950 p-5 text-sm leading-7 text-emerald-200"><code>MODULE8A_DB_HOST=127.0.0.1
MODULE8A_DB_PORT=3307
MODULE8A_DB_DATABASE=orm_practice_db
MODULE8A_DB_USERNAME=module8a
MODULE8A_DB_PASSWORD=your_local_password</code></pre>
        </article>

        <article class="rounded-3xl border border-stone-300 bg-white p-6 sm:p-8">
            <p class="text-sm font-black uppercase tracking-[.14em] text-orange-700">Migration evidence</p>
            <h2 class="mt-2 text-3xl font-black">Run from the project root</h2>
            <pre class="mt-5 overflow-x-auto rounded-2xl bg-slate-950 p-5 text-sm leading-7 text-sky-200"><code>php artisan config:clear
php artisan migrate --database=module8a
php artisan migrate:status --database=module8a</code></pre>
            <p class="mt-5 leading-7 text-slate-600">
                Capture the successful migration output and save it in the Module 8A documentation folder. The committed
                <code>.env.example</code> must remain free of real passwords and machine-specific secrets.
            </p>
        </article>
    </section>
@endsection
