@extends('layouts.app')

{{--
Reflection:
Eloquent simplified how I interacted with the database. It helped me write less
database code and think about each inventory row as an Item object instead of a
raw SQL result. Migrations also made the table structure visible and repeatable,
which is a more organized and scalable workflow than keeping SQL in comments.
--}}

@section('content')
    <section class="overflow-hidden rounded-3xl border border-emerald-200 bg-slate-950 text-white shadow-2xl shadow-emerald-950/15">
        <div class="grid gap-8 p-6 sm:p-8 lg:grid-cols-[1.25fr_.75fr] lg:p-12">
            <div>
                <a class="mb-8 inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-bold no-underline transition hover:bg-white/15" href="{{ route('roadmap.module', 'module-8') }}">
                    ← Return to Module 8 roadmap
                </a>
                <p class="mb-3 text-sm font-black uppercase tracking-[.18em] text-emerald-300">Module 8 · Assignment 8B</p>
                <h1 class="max-w-4xl text-4xl leading-tight font-black tracking-tight sm:text-6xl">Inventory rebuilt with Eloquent</h1>
                <p class="mt-5 max-w-3xl text-lg leading-8 text-slate-300">
                    The personal inventory from Module 4B now flows through a Laravel migration, the
                    <code class="text-emerald-200">Item</code> model, a controller, and this Blade view instead of raw PDO.
                </p>
            </div>

            <aside class="self-end rounded-2xl border border-emerald-300/25 bg-emerald-300/10 p-5" aria-label="Eloquent request summary">
                <p class="text-xs font-black uppercase tracking-[.14em] text-emerald-300">Request flow</p>
                <ol class="mt-4 grid gap-3 text-sm font-bold">
                    <li class="flex items-center gap-3"><span class="grid h-7 w-7 place-items-center rounded-lg bg-emerald-300 text-slate-950">1</span> GET /inventory</li>
                    <li class="flex items-center gap-3"><span class="grid h-7 w-7 place-items-center rounded-lg bg-emerald-300 text-slate-950">2</span> InventoryController</li>
                    <li class="flex items-center gap-3"><span class="grid h-7 w-7 place-items-center rounded-lg bg-emerald-300 text-slate-950">3</span> Item Eloquent model</li>
                    <li class="flex items-center gap-3"><span class="grid h-7 w-7 place-items-center rounded-lg bg-emerald-300 text-slate-950">4</span> Blade response</li>
                </ol>
            </aside>
        </div>
    </section>

    <section class="grid gap-4 lg:grid-cols-3" aria-labelledby="orm-change-title">
        <div class="lg:col-span-3">
            <p class="text-sm font-black uppercase tracking-[.14em] text-emerald-700">PDO → ORM</p>
            <h2 id="orm-change-title" class="mt-2 text-3xl font-black tracking-tight">The same inventory, a clearer workflow</h2>
        </div>

        @foreach ([
            ['Migration', 'The items table is described in PHP and can be recreated consistently.'],
            ['Item model', 'Each row becomes an object with fillable fields and automatic date casting.'],
            ['Eloquent query', 'Search, filters, and sorting are composed without building raw SQL strings.'],
        ] as [$heading, $copy])
            <article class="rounded-2xl border border-stone-300 bg-white p-5 shadow-lg shadow-slate-900/5">
                <p class="text-xs font-black uppercase tracking-[.14em] text-orange-700">{{ $loop->iteration < 10 ? '0'.$loop->iteration : $loop->iteration }}</p>
                <h3 class="mt-3 text-xl font-black">{{ $heading }}</h3>
                <p class="mt-2 leading-7 text-slate-600">{{ $copy }}</p>
            </article>
        @endforeach
    </section>

    <section class="rounded-3xl border border-stone-300 bg-white p-5 shadow-xl shadow-slate-900/5 sm:p-7" aria-labelledby="filters-title">
        <div class="flex flex-col gap-2 border-b border-stone-200 pb-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-[.14em] text-orange-700">Interactive Eloquent query</p>
                <h2 id="filters-title" class="mt-2 text-3xl font-black tracking-tight">Filter the collection</h2>
            </div>
            <code class="text-sm font-bold text-slate-500">Item::query()</code>
        </div>

        <form class="mt-6 grid min-w-0 gap-5" method="GET" action="{{ route('inventory.index') }}">
            <div class="grid min-w-0 gap-4 md:grid-cols-2 xl:grid-cols-4">
                <label class="grid min-w-0 gap-2 text-sm font-black uppercase tracking-wide text-slate-600">
                    Search item or category
                    <input class="min-w-0 w-full rounded-xl border border-stone-300 px-4 py-3 font-normal tracking-normal normal-case" type="search" name="search" value="{{ $filters['search'] }}" placeholder="keyboard, tools, kitchen...">
                </label>

                <label class="grid min-w-0 gap-2 text-sm font-black uppercase tracking-wide text-slate-600">
                    Category
                    <select class="min-w-0 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 font-normal tracking-normal normal-case" name="category">
                        <option value="">All categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category }}" @selected($filters['category'] === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="grid min-w-0 gap-2 text-sm font-black uppercase tracking-wide text-slate-600">
                    Minimum quantity
                    <input class="min-w-0 w-full rounded-xl border border-stone-300 px-4 py-3 font-normal tracking-normal normal-case" type="number" name="min_quantity" min="0" value="{{ $filters['min_quantity'] }}" placeholder="0">
                </label>

                <label class="grid min-w-0 gap-2 text-sm font-black uppercase tracking-wide text-slate-600">
                    Display order
                    <select class="min-w-0 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 font-normal tracking-normal normal-case" name="sort">
                        @foreach ($sorts as $key => $sort)
                            <option value="{{ $key }}" @selected($filters['sort'] === $key)>{{ $sort['label'] }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div class="flex flex-wrap gap-3">
                <button class="min-h-12 rounded-xl bg-emerald-700 px-5 py-3 font-black text-white transition hover:-translate-y-0.5 hover:bg-emerald-800" type="submit">Apply Eloquent filters</button>
                <a class="inline-flex min-h-12 items-center rounded-xl border border-stone-300 bg-white px-5 py-3 font-black no-underline transition hover:border-emerald-700 hover:text-emerald-800" href="{{ route('inventory.index') }}">Reset</a>
            </div>
        </form>
    </section>

    <section class="rounded-3xl border border-stone-300 bg-white p-5 shadow-xl shadow-slate-900/5 sm:p-7" aria-labelledby="inventory-title">
        <div class="flex flex-col gap-2 border-b border-stone-200 pb-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-[.14em] text-emerald-700">MySQL data through Eloquent</p>
                <h2 id="inventory-title" class="mt-2 text-3xl font-black tracking-tight">Inventory items</h2>
            </div>
            <span class="rounded-full bg-emerald-100 px-4 py-2 text-sm font-black text-emerald-900">{{ $items->count() }} {{ $items->count() === 1 ? 'item' : 'items' }}</span>
        </div>

        @if ($items->isEmpty())
            <div class="mt-6 rounded-2xl border border-dashed border-stone-300 bg-stone-50 p-8 text-center">
                <h3 class="text-xl font-black">No inventory records matched</h3>
                <p class="mt-2 text-slate-600">Adjust the filters or reset the collection to see every Item model.</p>
            </div>
        @else
            <div class="mt-6 overflow-x-auto rounded-2xl border border-stone-300">
                <table class="w-full min-w-3xl border-collapse text-left">
                    <thead class="bg-slate-950 text-white">
                        <tr>
                            <th class="px-5 py-4 text-xs font-black uppercase tracking-[.12em]" scope="col">Item</th>
                            <th class="px-5 py-4 text-xs font-black uppercase tracking-[.12em]" scope="col">Category</th>
                            <th class="px-5 py-4 text-xs font-black uppercase tracking-[.12em]" scope="col">Quantity</th>
                            <th class="px-5 py-4 text-xs font-black uppercase tracking-[.12em]" scope="col">Purchase date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200">
                        @foreach ($items as $item)
                            <tr class="transition hover:bg-emerald-50/70">
                                <td class="px-5 py-4 font-black">{{ $item->item_name }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $item->category ?? 'Uncategorized' }}</td>
                                <td class="px-5 py-4"><span class="inline-grid min-w-10 place-items-center rounded-lg bg-sky-100 px-2 py-1 font-mono font-black text-sky-900">{{ $item->quantity }}</span></td>
                                <td class="px-5 py-4 text-slate-600">{{ $item->purchase_date?->format('M j, Y') ?? 'Not recorded' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <section class="grid gap-4 lg:grid-cols-[1.15fr_.85fr]" aria-labelledby="reflection-title">
        <article class="rounded-3xl border border-emerald-200 bg-emerald-50 p-6 sm:p-8">
            <p class="text-sm font-black uppercase tracking-[.14em] text-emerald-800">Reflection</p>
            <h2 id="reflection-title" class="mt-2 text-3xl font-black tracking-tight">Thinking in objects instead of rows</h2>
            <p class="mt-4 leading-8 text-slate-700">
                Eloquent simplified how I interacted with the database because each inventory record became an
                <code>Item</code> object with readable properties. I wrote less connection and query code, while the
                controller remained focused on preparing data for the Blade view.
            </p>
            <p class="mt-3 leading-8 text-slate-700">
                Migrations also changed the workflow by keeping the table structure in Git. Compared with the raw PDO
                version from Module 4B, this approach is easier to reproduce, test, and extend as the inventory grows.
            </p>
        </article>

        <aside class="rounded-3xl border border-stone-300 bg-white p-6 sm:p-8" aria-label="Assignment evidence">
            <p class="text-sm font-black uppercase tracking-[.14em] text-orange-700">Assignment evidence</p>
            <h2 class="mt-2 text-2xl font-black">Required Laravel pieces</h2>
            <ul class="mt-5 grid gap-3 text-slate-700">
                <li><strong class="text-slate-950">Migration:</strong> creates the <code>items</code> table</li>
                <li><strong class="text-slate-950">Model:</strong> <code>App\Models\Item</code></li>
                <li><strong class="text-slate-950">Controller:</strong> retrieves Eloquent records</li>
                <li><strong class="text-slate-950">Blade:</strong> loops with <code>@@foreach</code></li>
                <li><strong class="text-slate-950">Test URL:</strong> <code>/inventory</code></li>
            </ul>
        </aside>
    </section>
@endsection
