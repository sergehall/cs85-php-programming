<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'CS85 PHP Programming' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 text-slate-950 antialiased">
    <div class="min-h-screen">
        <header class="mx-auto flex w-[min(1120px,calc(100%_-_2rem))] flex-col gap-4 py-6 md:flex-row md:items-center md:gap-6">
            <a class="grid min-w-42 gap-0.5 no-underline" href="{{ route('home') }}" aria-label="CS85 PHP Programming home">
                <span class="text-lg font-bold text-slate-950">CS85</span>
                <span class="text-xs font-bold uppercase tracking-normal text-slate-500">PHP Programming</span>
            </a>

            <nav class="flex flex-1 flex-wrap gap-2" aria-label="Main navigation">
                @foreach (config('navigation.public') as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        class="rounded-lg border px-3 py-2 text-sm font-bold no-underline transition {{ request()->routeIs($item['route']) ? 'border-stone-300 bg-white text-slate-950' : 'border-transparent text-slate-500 hover:border-stone-300 hover:bg-white hover:text-slate-950' }}"
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <a class="rounded-lg border border-transparent px-3 py-2 text-sm font-bold text-teal-800 no-underline transition hover:border-stone-300 hover:bg-white hover:text-slate-950" href="{{ route('cabinet.dashboard') }}">Cabinet</a>
        </header>

        <main class="mx-auto grid w-[min(1120px,calc(100%_-_2rem))] gap-7 py-9 md:pb-12">
            @yield('content')
        </main>

        <footer class="mx-auto flex w-[min(1120px,calc(100%_-_2rem))] flex-col gap-3 border-t border-stone-300 py-6 text-xs font-bold text-slate-500 md:flex-row md:justify-between">
            <span>Santa Monica College - Summer 2026</span>
            <span>Built with Laravel, MySQL readiness, and AI extension points.</span>
        </footer>
    </div>
</body>
</html>
