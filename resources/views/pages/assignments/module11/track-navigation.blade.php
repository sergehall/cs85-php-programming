@php
    $courseTrackActive = request()->routeIs('assignments.module11a.weather');
@endphp

<nav class="grid gap-4 rounded-2xl border border-teal-200 bg-white p-4 shadow-xl shadow-teal-950/5 md:grid-cols-[minmax(0,1fr)_auto] md:items-center md:p-5" aria-label="Module 11 implementation tracks">
    <div>
        <p class="text-xs font-bold uppercase tracking-normal text-teal-700">Module 11 workspace</p>
        <p class="mt-1 text-sm leading-6 text-slate-600">Compare the exact static-JSON assignment with the production-oriented live API extension.</p>
    </div>

    <div class="grid gap-2 sm:grid-cols-2">
        <a
            class="grid min-h-16 content-center rounded-xl border px-4 py-3 no-underline transition {{ $courseTrackActive ? 'border-teal-700 bg-slate-950 text-white shadow-lg shadow-slate-900/15' : 'border-stone-300 bg-stone-50 text-slate-800 hover:border-teal-600 hover:bg-white' }}"
            href="{{ route('assignments.module11a.weather') }}"
            @if ($courseTrackActive) aria-current="page" @endif
        >
            <strong class="text-sm">Assignment 11A</strong>
            <span class="mt-1 text-xs {{ $courseTrackActive ? 'text-slate-300' : 'text-slate-500' }}">Static JSON, controller, Blade table</span>
        </a>
        <a
            class="grid min-h-16 content-center rounded-xl border px-4 py-3 no-underline transition {{ $courseTrackActive ? 'border-stone-300 bg-stone-50 text-slate-800 hover:border-teal-600 hover:bg-white' : 'border-teal-700 bg-slate-950 text-white shadow-lg shadow-slate-900/15' }}"
            href="{{ route('roadmap.module', 'module-11') }}"
            @unless ($courseTrackActive) aria-current="page" @endunless
        >
            <strong class="text-sm">Advanced implementation</strong>
            <span class="mt-1 text-xs {{ $courseTrackActive ? 'text-slate-500' : 'text-slate-300' }}">Live API, DTO, cache, fallback, JSON</span>
        </a>
    </div>
</nav>
