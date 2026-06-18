<section class="grid gap-5 lg:grid-cols-3" aria-label="{{ $label }}">
    @foreach ($panels as $panel)
        <article class="grid content-start gap-4 rounded-lg border border-stone-300 bg-white p-6">
            <h2 class="text-xl font-bold text-slate-950">{{ $panel['title'] }}</h2>
            <ul class="grid gap-2 pl-5 leading-7 text-slate-600">
                @foreach ($panel['items'] as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </article>
    @endforeach
</section>
