<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @php
        $seoTitle = $title ?? config('seo.title');
        $seoDescription = $description ?? config('seo.description');
        $seoImage = asset(config('seo.image'));
        $canonicalUrl = url()->current();
        $shouldIndex = request()->routeIs('home', 'roadmap', 'stack', 'contact');
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="keywords" content="{{ config('seo.keywords') }}">
    <meta name="author" content="Serge Hall">
    <meta name="robots" content="{{ $shouldIndex ? 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' : 'noindex, nofollow' }}">
    <meta name="theme-color" content="{{ config('seo.theme_color') }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" href="{{ asset('favicon-16x16.png') }}" sizes="16x16" type="image/png">
    <link rel="icon" href="{{ asset('favicon-32x32.png') }}" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('seo.name') }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoImage }}">
    <title>{{ $seoTitle }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 text-slate-950 antialiased">
    <div class="min-h-screen">
        <header class="mx-auto flex w-[min(1120px,calc(100%_-_2rem))] flex-col gap-4 py-6 md:flex-row md:items-center md:gap-6">
            <a class="flex min-w-42 items-center gap-3 no-underline" href="{{ route('home') }}" aria-label="CS85 PHP Programming home">
                <img class="h-11 w-11 rounded-lg object-cover" src="{{ asset('assets/brand/cs85-logo-192.png') }}" width="44" height="44" alt="CS85 PHP Programming logo">
                <span class="grid gap-0.5">
                    <span class="text-lg font-bold text-slate-950">CS85</span>
                    <span class="text-xs font-bold uppercase tracking-normal text-slate-500">PHP Programming</span>
                </span>
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

            <div class="flex flex-wrap items-center gap-2">
                @auth
                    <a class="rounded-lg border border-transparent px-3 py-2 text-sm font-bold text-teal-800 no-underline transition hover:border-stone-300 hover:bg-white hover:text-slate-950" href="{{ route('cabinet.dashboard') }}">Cabinet</a>
                    <span class="hidden max-w-40 truncate text-sm font-bold text-slate-500 md:block">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-bold text-slate-600 transition hover:border-orange-700 hover:text-orange-700" type="submit">Logout</button>
                    </form>
                @else
                    <a class="rounded-lg border border-transparent px-3 py-2 text-sm font-bold text-teal-800 no-underline transition hover:border-stone-300 hover:bg-white hover:text-slate-950" href="{{ route('login') }}">Login</a>
                    <a class="rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-bold text-slate-950 no-underline transition hover:border-teal-700 hover:text-teal-800" href="{{ route('register') }}">Create one</a>
                @endauth
            </div>
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
