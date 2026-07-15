@php
    $record = $contact ?? null;
    $fieldPrefix = $prefix ?? 'contact';
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <label class="grid gap-2 text-sm font-black text-slate-700" for="{{ $fieldPrefix }}-first-name">
        First name
        <input class="min-h-12 rounded-xl border border-stone-300 bg-white px-4 py-3 font-normal" id="{{ $fieldPrefix }}-first-name" name="first_name" type="text" maxlength="100" value="{{ old('first_name', $record?->first_name) }}" required>
    </label>

    <label class="grid gap-2 text-sm font-black text-slate-700" for="{{ $fieldPrefix }}-last-name">
        Last name
        <input class="min-h-12 rounded-xl border border-stone-300 bg-white px-4 py-3 font-normal" id="{{ $fieldPrefix }}-last-name" name="last_name" type="text" maxlength="100" value="{{ old('last_name', $record?->last_name) }}" required>
    </label>

    <label class="grid gap-2 text-sm font-black text-slate-700" for="{{ $fieldPrefix }}-email">
        Email address
        <input class="min-h-12 rounded-xl border border-stone-300 bg-white px-4 py-3 font-normal" id="{{ $fieldPrefix }}-email" name="email" type="email" maxlength="255" value="{{ old('email', $record?->email) }}" required>
    </label>

    <label class="grid gap-2 text-sm font-black text-slate-700" for="{{ $fieldPrefix }}-phone">
        Phone
        <input class="min-h-12 rounded-xl border border-stone-300 bg-white px-4 py-3 font-normal" id="{{ $fieldPrefix }}-phone" name="phone" type="tel" maxlength="32" value="{{ old('phone', $record?->phone) }}" placeholder="+1-310-555-0100">
    </label>

    <label class="grid gap-2 text-sm font-black text-slate-700" for="{{ $fieldPrefix }}-company">
        Company
        <input class="min-h-12 rounded-xl border border-stone-300 bg-white px-4 py-3 font-normal" id="{{ $fieldPrefix }}-company" name="company" type="text" maxlength="150" value="{{ old('company', $record?->company) }}">
    </label>

    <label class="grid gap-2 text-sm font-black text-slate-700" for="{{ $fieldPrefix }}-group">
        Contact group
        <select class="min-h-12 rounded-xl border border-stone-300 bg-white px-4 py-3 font-normal" id="{{ $fieldPrefix }}-group" name="contact_group_id">
            <option value="">No group</option>
            @foreach ($groups as $group)
                <option value="{{ $group->getKey() }}" @selected((string) old('contact_group_id', $record?->contact_group_id) === (string) $group->getKey())>{{ $group->name }}</option>
            @endforeach
        </select>
    </label>

    <label class="grid gap-2 text-sm font-black text-slate-700" for="{{ $fieldPrefix }}-role">
        Sample role
        <select class="min-h-12 rounded-xl border border-stone-300 bg-white px-4 py-3 font-normal" id="{{ $fieldPrefix }}-role" name="role" required>
            @foreach ($roles as $roleValue => $roleLabel)
                <option value="{{ $roleValue }}" @selected(old('role', $record?->role ?? 'user') === $roleValue)>{{ $roleLabel }}</option>
            @endforeach
        </select>
        <span class="text-xs leading-5 font-normal text-slate-500">Training data only; this does not grant application permissions.</span>
    </label>

    <label class="flex min-h-12 items-center gap-3 self-start rounded-xl border border-stone-300 bg-white px-4 py-3 text-sm font-black text-slate-700 sm:mt-7">
        <input class="h-5 w-5 rounded border-stone-300 text-amber-700" name="is_active" type="checkbox" value="1" @checked((bool) old('is_active', $record?->is_active ?? true))>
        Active contact
    </label>

    <label class="grid gap-2 text-sm font-black text-slate-700 sm:col-span-2" for="{{ $fieldPrefix }}-notes">
        Notes
        <textarea class="min-h-28 rounded-xl border border-stone-300 bg-white px-4 py-3 font-normal leading-6" id="{{ $fieldPrefix }}-notes" name="notes" maxlength="1000">{{ old('notes', $record?->notes) }}</textarea>
    </label>
</div>
