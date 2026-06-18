<section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4" aria-label="{{ $label }}">
    @foreach ($items as $item)
        <article class="rounded-lg border border-stone-300 bg-white p-5">
            <p class="text-xs font-bold uppercase tracking-normal text-slate-500">{{ $item['label'] }}</p>
            <strong class="mt-2 block text-lg text-slate-950">{{ $item['value'] }}</strong>
            @isset($item['detail'])
                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $item['detail'] }}</p>
            @endisset
        </article>
    @endforeach
</section>
