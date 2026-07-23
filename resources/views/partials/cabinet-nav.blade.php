<nav class="grid min-w-0 max-w-full gap-3 overflow-hidden rounded-lg border border-stone-300 bg-white p-3" aria-label="Cabinet navigation">
    <div class="flex min-w-0 flex-col gap-3 md:flex-row md:items-center md:gap-4">
        <span class="text-xs font-bold uppercase tracking-normal text-teal-800">User cabinet</span>
        <div class="flex min-w-0 flex-nowrap gap-2 overflow-x-auto pb-1 md:flex-wrap md:overflow-visible md:pb-0">
            @foreach (config('cabinet.navigation.user') as $item)
                <a
                    href="{{ route($item['route']) }}"
                    class="shrink-0 rounded-lg border px-3 py-2 text-sm font-bold no-underline transition {{ request()->routeIs($item['route']) ? 'border-stone-300 bg-stone-100 text-slate-950' : 'border-transparent text-slate-500 hover:border-stone-300 hover:bg-stone-100 hover:text-slate-950' }}"
                    @if (request()->routeIs($item['route'])) aria-current="page" @endif
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </div>

    @if (auth()->user()?->isAdmin())
        <div class="flex min-w-0 flex-col gap-3 border-t border-stone-300 pt-3 md:flex-row md:items-center md:gap-4">
            <span class="text-xs font-bold uppercase tracking-normal text-orange-700">Admin rules</span>
            <div class="flex min-w-0 flex-nowrap gap-2 overflow-x-auto pb-1 md:flex-wrap md:overflow-visible md:pb-0">
                @foreach (config('cabinet.navigation.admin') as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        class="shrink-0 rounded-lg border px-3 py-2 text-sm font-bold no-underline transition {{ request()->routeIs($item['route']) ? 'border-stone-300 bg-stone-100 text-slate-950' : 'border-transparent text-slate-500 hover:border-stone-300 hover:bg-stone-100 hover:text-slate-950' }}"
                        @if (request()->routeIs($item['route'])) aria-current="page" @endif
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</nav>
