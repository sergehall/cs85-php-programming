@php
    $activeTrack = $activeTrack ?? 'advanced';
@endphp

<nav class="grid gap-4 rounded-2xl border border-violet-200 bg-white p-4 shadow-xl shadow-violet-950/5 md:grid-cols-[minmax(0,1fr)_auto] md:items-center md:p-5" aria-label="Module 10 implementation tracks">
    <div class="grid gap-1">
        <p class="text-xs font-bold uppercase tracking-normal text-violet-700">Module 10 workspace</p>
        <p class="text-sm leading-6 text-slate-600">Compare the exact coursework surface with the production-oriented extension.</p>
    </div>

    <div class="grid gap-2 sm:grid-cols-2">
        <a
            class="grid min-h-16 content-center rounded-xl border px-4 py-3 no-underline transition {{ $activeTrack === 'assignment' ? 'border-violet-700 bg-violet-700 text-white shadow-lg shadow-violet-950/15' : 'border-stone-300 bg-stone-50 text-slate-700 hover:border-violet-500 hover:bg-violet-50' }}"
            href="{{ route('assignments.module10a.overview') }}"
            @if ($activeTrack === 'assignment') aria-current="page" @endif
        >
            <strong class="text-sm">Assignment 10A</strong>
            <span class="text-xs {{ $activeTrack === 'assignment' ? 'text-violet-100' : 'text-slate-500' }}">Dashboard, secret page, short answers</span>
        </a>

        <a
            class="grid min-h-16 content-center rounded-xl border px-4 py-3 no-underline transition {{ $activeTrack === 'advanced' ? 'border-slate-950 bg-slate-950 text-white shadow-lg shadow-slate-950/15' : 'border-stone-300 bg-stone-50 text-slate-700 hover:border-slate-600 hover:bg-slate-100' }}"
            href="{{ route('roadmap.module', 'module-10') }}"
            @if ($activeTrack === 'advanced') aria-current="page" @endif
        >
            <strong class="text-sm">Advanced implementation</strong>
            <span class="text-xs {{ $activeTrack === 'advanced' ? 'text-slate-300' : 'text-slate-500' }}">MFA, OAuth, roles, sessions, audit</span>
        </a>
    </div>
</nav>
