@if ($errors->any())
    <div class="rounded-xl border border-red-300 bg-red-50 p-4 text-red-900" role="alert">
        <p class="font-black">Please correct the following fields:</p>
        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<label class="grid gap-2 font-bold text-slate-800" for="name">
    Name
    <input
        class="min-h-12 rounded-xl border border-stone-300 bg-white px-4 py-3 font-normal"
        id="name"
        name="name"
        type="text"
        maxlength="255"
        value="{{ old('name', $contact?->name) }}"
        autocomplete="name"
        required
    >
</label>

<label class="grid gap-2 font-bold text-slate-800" for="email">
    Email
    <input
        class="min-h-12 rounded-xl border border-stone-300 bg-white px-4 py-3 font-normal"
        id="email"
        name="email"
        type="email"
        maxlength="255"
        value="{{ old('email', $contact?->email) }}"
        autocomplete="email"
        required
    >
</label>

<label class="grid gap-2 font-bold text-slate-800" for="phone">
    Phone
    <input
        class="min-h-12 rounded-xl border border-stone-300 bg-white px-4 py-3 font-normal"
        id="phone"
        name="phone"
        type="tel"
        maxlength="32"
        value="{{ old('phone', $contact?->phone) }}"
        autocomplete="tel"
        required
    >
</label>
