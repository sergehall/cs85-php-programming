@php
    $courseTrackActive = request()->routeIs('ai.form', 'ai.generate');
@endphp

<nav class="grid gap-4 rounded-2xl border border-orange-200 bg-white p-4 shadow-xl shadow-orange-950/5 md:grid-cols-[minmax(0,1fr)_auto] md:items-center md:p-5" aria-label="Module 12 implementation tracks">
    <div>
        <p class="text-xs font-bold uppercase tracking-normal text-orange-700">Module 12 workspace</p>
        <p class="mt-1 text-sm leading-6 text-slate-600">Verify the exact OpenAI assignment, then inspect its production-grade final-project evolution.</p>
    </div>

    <div class="grid gap-2 sm:grid-cols-2">
        <a
            class="grid min-h-16 content-center rounded-xl border px-4 py-3 no-underline transition {{ $courseTrackActive ? 'border-orange-700 bg-slate-950 text-white shadow-lg shadow-slate-900/15' : 'border-stone-300 bg-stone-50 text-slate-800 hover:border-orange-600 hover:bg-white' }}"
            href="{{ route('ai.form') }}"
            @if ($courseTrackActive) aria-current="page" @endif
        >
            <strong class="text-sm">Assignment 12A</strong>
            <span class="mt-1 text-xs {{ $courseTrackActive ? 'text-slate-300' : 'text-slate-500' }}">OpenAI gpt-4o-mini content generator</span>
        </a>
        <a
            class="grid min-h-16 content-center rounded-xl border px-4 py-3 no-underline transition {{ $courseTrackActive ? 'border-stone-300 bg-stone-50 text-slate-800 hover:border-orange-600 hover:bg-white' : 'border-orange-700 bg-slate-950 text-white shadow-lg shadow-slate-900/15' }}"
            href="{{ route('roadmap.module', 'module-12') }}"
            @unless ($courseTrackActive) aria-current="page" @endunless
        >
            <strong class="text-sm">Final Project</strong>
            <span class="mt-1 text-xs {{ $courseTrackActive ? 'text-slate-500' : 'text-slate-300' }}">Hybrid LM Studio + OpenAI workspace</span>
        </a>
    </div>
</nav>
