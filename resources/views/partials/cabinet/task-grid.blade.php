<section class="rounded-lg border border-stone-300 bg-white p-6">
    <h2 class="text-xl font-bold text-slate-950">{{ $title }}</h2>
    <div class="mt-5 grid gap-3 md:grid-cols-3">
        @foreach ($tasks as $task)
            <article class="rounded-lg border border-stone-200 bg-stone-50 p-4">
                @if (is_array($task))
                    <p class="font-bold text-slate-950">{{ $task['label'] }}</p>
                    <p class="mt-2 text-sm font-bold uppercase tracking-normal text-orange-700">{{ $task['status'] }}</p>
                @else
                    <p class="font-bold text-slate-950">{{ $task }}</p>
                @endif
            </article>
        @endforeach
    </div>
</section>
