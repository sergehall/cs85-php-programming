@extends('layouts.app', ['title' => 'Profile - Cabinet - CS85'])

@section('content')
    @include('partials.cabinet-nav')

    <section class="max-w-4xl py-3">
        <p class="mb-3 text-xs font-bold uppercase tracking-normal text-teal-800">{{ $section['eyebrow'] }}</p>
        <h1 class="mb-4 max-w-4xl text-4xl font-bold leading-none text-slate-950 md:text-6xl">{{ $section['title'] }}</h1>
        <p class="text-lg leading-8 text-slate-600">{{ $section['description'] }}</p>
    </section>

    @if (session('status'))
        <section class="rounded-lg border border-emerald-300 bg-emerald-50 p-4 text-sm font-bold text-emerald-800" role="status">
            {{ session('status') }}
        </section>
    @endif

    <section class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_320px]">
        <form class="grid gap-5 rounded-lg border border-stone-300 bg-white p-6" method="POST" action="{{ route('cabinet.profile.update') }}">
            @csrf
            @method('PUT')

            <div>
                <h2 class="text-xl font-bold text-slate-950">Editable profile fields</h2>
                <p class="mt-2 leading-7 text-slate-600">
                    Keep your course identity and portfolio links ready for future coursework and final project pages.
                </p>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <label class="grid gap-2 font-bold text-slate-700" for="first_name">
                    First name
                    <input
                        class="rounded-lg border border-stone-300 px-3 py-2 font-normal text-slate-950"
                        id="first_name"
                        name="first_name"
                        type="text"
                        value="{{ old('first_name', $user->first_name ?: Str::before($user->name, ' ')) }}"
                        required
                    >
                    @error('first_name')
                        <span class="text-sm text-red-700">{{ $message }}</span>
                    @enderror
                </label>

                <label class="grid gap-2 font-bold text-slate-700" for="last_name">
                    Last name
                    <input
                        class="rounded-lg border border-stone-300 px-3 py-2 font-normal text-slate-950"
                        id="last_name"
                        name="last_name"
                        type="text"
                        value="{{ old('last_name', $user->last_name ?: Str::after($user->name, ' ')) }}"
                        required
                    >
                    @error('last_name')
                        <span class="text-sm text-red-700">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <label class="grid gap-2 font-bold text-slate-700" for="github_profile_url">
                GitHub profile link
                <input
                    class="rounded-lg border border-stone-300 px-3 py-2 font-normal text-slate-950"
                    id="github_profile_url"
                    name="github_profile_url"
                    type="url"
                    value="{{ old('github_profile_url', $user->github_profile_url) }}"
                    placeholder="https://github.com/username"
                >
                @error('github_profile_url')
                    <span class="text-sm text-red-700">{{ $message }}</span>
                @enderror
            </label>

            <label class="grid gap-2 font-bold text-slate-700" for="linkedin_profile_url">
                LinkedIn profile link
                <input
                    class="rounded-lg border border-stone-300 px-3 py-2 font-normal text-slate-950"
                    id="linkedin_profile_url"
                    name="linkedin_profile_url"
                    type="url"
                    value="{{ old('linkedin_profile_url', $user->linkedin_profile_url) }}"
                    placeholder="https://www.linkedin.com/in/username"
                >
                @error('linkedin_profile_url')
                    <span class="text-sm text-red-700">{{ $message }}</span>
                @enderror
            </label>

            <label class="grid gap-2 font-bold text-slate-700" for="bio">
                Short bio
                <textarea
                    class="min-h-32 rounded-lg border border-stone-300 px-3 py-2 font-normal text-slate-950"
                    id="bio"
                    name="bio"
                    placeholder="A short course and portfolio bio."
                >{{ old('bio', $user->bio) }}</textarea>
                @error('bio')
                    <span class="text-sm text-red-700">{{ $message }}</span>
                @enderror
            </label>

            <label class="grid gap-2 font-bold text-slate-700" for="technical_skills">
                Technical skills
                <textarea
                    class="min-h-28 rounded-lg border border-stone-300 px-3 py-2 font-normal text-slate-950"
                    id="technical_skills"
                    name="technical_skills"
                    placeholder="PHP, Laravel, MySQL, Docker, JavaScript..."
                >{{ old('technical_skills', $user->technical_skills) }}</textarea>
                @error('technical_skills')
                    <span class="text-sm text-red-700">{{ $message }}</span>
                @enderror
            </label>

            <div class="flex justify-end border-t border-stone-200 pt-5">
                <button class="rounded-lg bg-teal-800 px-4 py-2 font-bold text-white transition hover:bg-teal-900" type="submit">
                    Save profile
                </button>
            </div>
        </form>

        <aside class="grid content-start gap-4 rounded-lg border border-stone-300 bg-white p-6">
            <h2 class="text-xl font-bold text-slate-950">Account summary</h2>
            @php($emptyProfileValue = 'Not provided yet')
            @php($profileInitials = collect(explode(' ', trim($user->name)))->filter()->map(fn (string $name): string => Str::upper(Str::substr($name, 0, 1)))->take(2)->implode('') ?: 'U')

            <div class="grid gap-3 rounded-lg border border-stone-200 bg-stone-50 p-4">
                <div class="flex items-center gap-3">
                    @if ($user->github_avatar_url)
                        <img class="h-16 w-16 rounded-lg object-cover" src="{{ $user->github_avatar_url }}" alt="" referrerpolicy="no-referrer">
                    @else
                        <span class="grid h-16 w-16 place-items-center rounded-lg bg-slate-950 text-lg font-bold text-white">{{ $profileInitials }}</span>
                    @endif
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-normal text-slate-500">Profile photo</p>
                        <p class="mt-1 font-bold text-slate-950">{{ $user->github_avatar_url ? 'Synced from GitHub' : 'Connect GitHub to sync photo' }}</p>
                    </div>
                </div>
                <p class="text-sm leading-6 text-slate-600">
                    After you connect GitHub in Security, your GitHub profile photo appears here automatically.
                </p>
            </div>

            <dl class="grid gap-3 text-sm">
                <div class="border-t border-stone-200 pt-3">
                    <dt class="font-bold text-slate-500">Account UUID</dt>
                    <dd class="mt-1 break-words font-bold text-slate-950">{{ $user->public_uuid }}</dd>
                </div>
                <div class="border-t border-stone-200 pt-3">
                    <dt class="font-bold text-slate-500">First name</dt>
                    <dd class="mt-1 break-words font-bold text-slate-950">{{ $user->first_name ?: $emptyProfileValue }}</dd>
                </div>
                <div class="border-t border-stone-200 pt-3">
                    <dt class="font-bold text-slate-500">Last name</dt>
                    <dd class="mt-1 break-words font-bold text-slate-950">{{ $user->last_name ?: $emptyProfileValue }}</dd>
                </div>
                <div class="border-t border-stone-200 pt-3">
                    <dt class="font-bold text-slate-500">Email</dt>
                    <dd class="mt-1 break-words font-bold text-slate-950">{{ $user->email }}</dd>
                </div>
                <div class="border-t border-stone-200 pt-3">
                    <dt class="font-bold text-slate-500">GitHub profile</dt>
                    <dd class="mt-1 break-words font-bold text-slate-950">
                        @if ($user->github_profile_url)
                            <a class="text-teal-800 underline decoration-teal-700/40 underline-offset-4 hover:text-teal-900" href="{{ $user->github_profile_url }}" rel="noreferrer" target="_blank">
                                {{ $user->github_profile_url }}
                            </a>
                        @else
                            {{ $emptyProfileValue }}
                        @endif
                    </dd>
                </div>
                <div class="border-t border-stone-200 pt-3">
                    <dt class="font-bold text-slate-500">LinkedIn profile</dt>
                    <dd class="mt-1 break-words font-bold text-slate-950">
                        @if ($user->linkedin_profile_url)
                            <a class="text-teal-800 underline decoration-teal-700/40 underline-offset-4 hover:text-teal-900" href="{{ $user->linkedin_profile_url }}" rel="noreferrer" target="_blank">
                                {{ $user->linkedin_profile_url }}
                            </a>
                        @else
                            {{ $emptyProfileValue }}
                        @endif
                    </dd>
                </div>
                <div class="border-t border-stone-200 pt-3">
                    <dt class="font-bold text-slate-500">Short bio</dt>
                    <dd class="mt-1 whitespace-pre-line break-words font-bold text-slate-950">{{ $user->bio ?: $emptyProfileValue }}</dd>
                </div>
                <div class="border-t border-stone-200 pt-3">
                    <dt class="font-bold text-slate-500">Technical skills</dt>
                    <dd class="mt-1 whitespace-pre-line break-words font-bold text-slate-950">{{ $user->technical_skills ?: $emptyProfileValue }}</dd>
                </div>
                <div class="border-t border-stone-200 pt-3">
                    <dt class="font-bold text-slate-500">Role</dt>
                    <dd class="mt-1 font-bold text-teal-800">{{ config("navigation.roles.{$user->role}.label", Str::headline($user->role)) }}</dd>
                </div>
                <div class="border-t border-stone-200 pt-3">
                    <dt class="font-bold text-slate-500">Profile source</dt>
                    <dd class="mt-1 font-bold text-orange-700">{{ $user->github_id ? 'GitHub connected' : 'Password account' }}</dd>
                </div>
            </dl>
        </aside>
    </section>
@endsection
