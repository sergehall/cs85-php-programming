@extends('layouts.app', ['title' => 'AI Study Studio - Cabinet - CS85'])

@section('content')
    @include('partials.cabinet-nav')

    @php
        $displayedModeKey = $activeConversation?->mode->value ?? 'general';
        $displayedMode = $modes[$displayedModeKey];
        $starterPrompts = [
            'general' => [
                'Explain Laravel service containers with a small example.',
                'Quiz me on PHP object-oriented programming.',
                'Create a 30-minute study plan for Eloquent relationships.',
            ],
            'coding' => [
                'Review this Laravel code for correctness and security.',
                'Help me debug an error step by step.',
                'Show me how to test a Laravel controller with PHPUnit.',
            ],
            'architecture' => [
                'Compare a modular monolith with microservices for my project.',
                'Review my Laravel application boundaries.',
                'Turn this feature idea into a production-ready implementation plan.',
            ],
        ];
    @endphp

    @if (session('status'))
        <section class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-900" role="status">
            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-emerald-700 text-white" aria-hidden="true">✓</span>
            {{ session('status') }}
        </section>
    @endif

    <section
        class="grid min-h-[42rem] items-start gap-4 lg:grid-cols-[18rem_minmax(0,1fr)]"
        data-ai-chat
        data-prompt-limit="{{ $promptLimit }}"
    >
        <aside class="grid min-w-0 content-start gap-4 overflow-hidden rounded-3xl border border-stone-200 bg-white shadow-sm shadow-slate-900/5 lg:sticky lg:top-4">
            <div class="grid gap-3 bg-slate-950 p-5 text-white">
                <div class="flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center rounded-2xl bg-teal-400 text-xl text-slate-950" aria-hidden="true">✦</span>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-teal-300">Local AI workspace</p>
                        <h1 class="mt-1 text-xl font-bold">AI Study Studio</h1>
                    </div>
                </div>
                <p class="text-sm leading-6 text-slate-300"><span class="font-bold text-white">Learning assistant.</span> Learn, build, and review code with private conversations stored by Laravel.</p>
                <div class="flex flex-wrap gap-2 text-xs font-bold">
                    <span class="rounded-full border border-slate-700 bg-slate-900 px-3 py-1.5 text-teal-200">Private history</span>
                    <span class="rounded-full border border-slate-700 bg-slate-900 px-3 py-1.5 text-slate-300">Streaming replies</span>
                </div>
            </div>

            <form class="grid min-w-0 gap-3 px-4" method="POST" action="{{ route('cabinet.ai.conversations.store') }}">
                @csrf
                <label class="grid min-w-0 gap-2 text-sm font-bold text-slate-800">
                    New conversation
                    <select class="block w-full min-w-0 rounded-xl border border-stone-300 bg-stone-50 px-3 py-3 font-normal text-slate-950 outline-none transition focus:border-teal-700 focus:bg-white" name="mode" required data-ai-mode-select>
                        @foreach ($modes as $mode => $configuration)
                            <option value="{{ $mode }}" data-description="{{ $configuration['description'] }}" @selected($displayedModeKey === $mode)>{{ $configuration['label'] }}</option>
                        @endforeach
                    </select>
                </label>
                <p class="text-xs leading-5 text-slate-500" data-ai-mode-description>{{ $displayedMode['description'] }}</p>
                <button class="group flex w-full items-center justify-center gap-2 rounded-xl bg-teal-700 px-4 py-3 text-sm font-bold text-white transition hover:bg-slate-950 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700" type="submit">
                    <span aria-hidden="true">＋</span>
                    Start conversation
                </button>
            </form>

            <div class="grid min-w-0 gap-3 border-t border-stone-200 px-4 pt-4" aria-label="AI conversation history">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Recent chats</p>
                    <span class="rounded-full bg-stone-100 px-2.5 py-1 text-xs font-bold text-slate-600">{{ $conversations->count() }}</span>
                </div>

                @if ($conversations->isNotEmpty())
                    <label class="relative block">
                        <span class="sr-only">Search conversations</span>
                        <span class="pointer-events-none absolute inset-y-0 left-3 grid place-items-center text-slate-400" aria-hidden="true">⌕</span>
                        <input
                            class="w-full rounded-xl border border-stone-300 bg-stone-50 py-2.5 pr-3 pl-9 text-sm text-slate-950 outline-none transition placeholder:text-slate-400 focus:border-teal-700 focus:bg-white"
                            type="search"
                            placeholder="Search conversations"
                            autocomplete="off"
                            data-ai-conversation-search
                        >
                    </label>
                @endif

                <div class="grid max-h-72 gap-2 overflow-y-auto pr-1" data-ai-conversation-list>
                    @forelse ($conversations as $conversation)
                        <a
                            class="group grid min-w-0 grid-cols-[minmax(0,1fr)_auto] items-center gap-2 rounded-xl border px-3 py-3 no-underline transition {{ $activeConversation?->is($conversation) ? 'border-teal-700 bg-teal-50 shadow-sm' : 'border-stone-200 bg-white hover:border-teal-600 hover:bg-stone-50' }}"
                            href="{{ route('cabinet.ai.conversations.show', $conversation->public_uuid) }}"
                            data-ai-conversation-item
                            data-conversation-title="{{ Str::lower($conversation->title) }}"
                            @if ($activeConversation?->is($conversation)) aria-current="page" @endif
                        >
                            <span class="grid min-w-0 gap-1">
                                <span class="truncate text-sm font-bold text-slate-950">{{ $conversation->title }}</span>
                                <span class="truncate text-xs text-slate-500">{{ $modes[$conversation->mode->value]['label'] ?? $conversation->mode->value }} · {{ $conversation->messages_count }} messages</span>
                            </span>
                            <span class="text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-teal-700" aria-hidden="true">›</span>
                        </a>
                    @empty
                        <div class="rounded-2xl border border-dashed border-stone-300 bg-stone-50 p-4 text-center">
                            <p class="text-sm font-bold text-slate-800">Your first chat starts here</p>
                            <p class="mt-1 text-xs leading-5 text-slate-500">Choose a mode above to begin.</p>
                        </div>
                    @endforelse
                    <p class="hidden rounded-xl border border-dashed border-stone-300 p-3 text-center text-sm text-slate-500" data-ai-conversation-search-empty>No matching conversations.</p>
                </div>
            </div>

            <div class="mx-4 mb-4 rounded-2xl border border-teal-100 bg-teal-50 p-4">
                <p class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.1em] text-teal-900"><span aria-hidden="true">●</span> Local-first</p>
                <p class="mt-2 text-xs leading-5 text-teal-900/75">Prompts are sent through the Laravel backend to your configured LM Studio runtime. Provider keys never reach the browser.</p>
            </div>
        </aside>

        <div class="grid min-w-0 content-start gap-4">
            @if ($activeConversation)
                <section class="relative grid min-h-[42rem] min-w-0 max-w-full grid-rows-[auto_minmax(20rem,1fr)_auto] overflow-hidden rounded-3xl border border-stone-200 bg-white shadow-lg shadow-slate-900/5">
                    <header class="flex min-w-0 max-w-full flex-col gap-4 overflow-hidden border-b border-stone-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                        <div class="flex min-w-0 flex-1 items-center gap-3">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-teal-50 text-lg text-teal-800" aria-hidden="true">✦</span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-teal-800">{{ $modes[$activeConversation->mode->value]['label'] }}</p>
                                    <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[0.68rem] font-bold text-emerald-800">Active</span>
                                    <span class="rounded-full bg-teal-50 px-2 py-0.5 text-[0.68rem] font-bold text-teal-800">{{ $modes[$activeConversation->mode->value]['model_name'] }}</span>
                                    <span class="rounded-full bg-stone-100 px-2 py-0.5 text-[0.68rem] font-bold text-slate-500">{{ $activeConversation->messages->count() }} messages</span>
                                </div>
                                <h2 class="mt-1 block max-w-full truncate text-xl font-bold text-slate-950" data-ai-conversation-title title="{{ $activeConversation->title }}">{{ $activeConversation->title }}</h2>
                            </div>
                        </div>
                        <form class="shrink-0" method="POST" action="{{ route('cabinet.ai.conversations.destroy', $activeConversation->public_uuid) }}" data-confirm="Delete this conversation and its messages? This cannot be undone.">
                            @csrf
                            @method('DELETE')
                            <button class="rounded-xl border border-stone-300 bg-white px-3 py-2 text-sm font-bold text-slate-600 transition hover:border-orange-300 hover:bg-orange-50 hover:text-orange-800" type="submit" aria-label="Delete conversation">
                                Delete
                            </button>
                        </form>
                    </header>

                    <div class="relative min-h-0 min-w-0 overflow-x-hidden bg-[linear-gradient(180deg,#fafaf9_0%,#ffffff_18%)]">
                        <section
                            class="grid h-[min(62vh,42rem)] min-h-80 min-w-0 max-w-full content-start gap-6 overflow-x-hidden overflow-y-auto px-4 py-6 [scrollbar-color:#a8a29e_transparent] [scrollbar-width:thin] sm:px-6"
                            aria-label="Conversation messages"
                            aria-live="polite"
                            aria-relevant="additions text"
                            tabindex="0"
                            data-ai-messages
                        >
                            @forelse ($activeConversation->messages as $message)
                                <article class="flex min-w-0 max-w-full gap-3 {{ $message->role === 'user' ? 'flex-row-reverse' : '' }}" data-ai-message-role="{{ $message->role }}">
                                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl text-xs font-bold {{ $message->role === 'user' ? 'bg-slate-950 text-white' : 'bg-teal-100 text-teal-900' }}" aria-hidden="true">
                                        {{ $message->role === 'user' ? 'You' : 'AI' }}
                                    </span>
                                    <div class="group grid min-w-0 max-w-[min(100%_-_3rem,48rem)] gap-2 {{ $message->role === 'user' ? 'justify-items-end' : 'justify-items-start' }}">
                                        <div class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                            <span>{{ $message->role === 'user' ? 'You' : 'Assistant' }}</span>
                                            <time datetime="{{ $message->created_at?->toIso8601String() }}">{{ $message->created_at?->format('g:i A') }}</time>
                                        </div>
                                        <pre class="min-w-0 max-w-full whitespace-pre-wrap break-words [overflow-wrap:anywhere] rounded-2xl px-4 py-3 font-sans text-sm leading-7 {{ $message->role === 'user' ? 'rounded-tr-sm bg-slate-950 text-white' : 'rounded-tl-sm border border-stone-200 bg-white text-slate-800 shadow-sm' }}" data-ai-message-content>{{ $message->content }}</pre>
                                        @if ($message->role === 'assistant')
                                            <button class="rounded-lg px-2 py-1 text-xs font-bold text-slate-400 opacity-100 transition hover:bg-stone-100 hover:text-teal-800 sm:opacity-0 sm:group-hover:opacity-100 sm:focus:opacity-100" type="button" data-ai-copy>Copy response</button>
                                        @endif
                                    </div>
                                </article>
                            @empty
                                <div class="grid min-h-72 place-items-center text-center" data-ai-empty-state>
                                    <div class="max-w-2xl">
                                        <span class="mx-auto grid h-16 w-16 place-items-center rounded-3xl bg-teal-50 text-2xl text-teal-800" aria-hidden="true">✦</span>
                                        <p class="mt-5 text-xl font-bold text-slate-950">What would you like to learn?</p>
                                        <p class="mx-auto mt-2 max-w-lg text-sm leading-6 text-slate-600">Start with your own question or use one of these ideas tailored to {{ $displayedMode['label'] }}.</p>
                                        <div class="mt-5 grid gap-2 sm:grid-cols-3">
                                            @foreach ($starterPrompts[$displayedModeKey] as $prompt)
                                                <button class="rounded-2xl border border-stone-200 bg-white p-3 text-left text-sm font-bold leading-5 text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-teal-600 hover:text-teal-900 hover:shadow-md" type="button" data-ai-prompt="{{ $prompt }}">
                                                    {{ $prompt }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </section>

                        <button class="absolute right-5 bottom-4 hidden items-center gap-2 rounded-full border border-stone-300 bg-white px-3 py-2 text-xs font-bold text-slate-700 shadow-lg transition hover:border-teal-600 hover:text-teal-800" type="button" data-ai-scroll-latest>
                            Latest message <span aria-hidden="true">↓</span>
                        </button>
                    </div>

                    <form
                        class="grid gap-3 border-t border-stone-200 bg-white p-4 sm:p-5"
                        method="POST"
                        action="{{ route('cabinet.ai.conversations.messages.stream', $activeConversation->public_uuid) }}"
                        data-ai-message-form
                    >
                        @csrf
                        <div class="flex gap-2 overflow-x-auto pb-1" aria-label="Suggested prompts">
                            @foreach ($starterPrompts[$displayedModeKey] as $prompt)
                                <button class="shrink-0 rounded-full border border-stone-300 bg-stone-50 px-3 py-2 text-xs font-bold text-slate-600 transition hover:border-teal-600 hover:bg-teal-50 hover:text-teal-900" type="button" data-ai-prompt="{{ $prompt }}">
                                    {{ Str::limit($prompt, 42) }}
                                </button>
                            @endforeach
                        </div>

                        <div class="rounded-2xl border border-stone-300 bg-stone-50 p-2 transition focus-within:border-teal-700 focus-within:bg-white focus-within:ring-4 focus-within:ring-teal-700/10">
                            <label class="sr-only" for="ai-message">Message</label>
                            <textarea
                                class="block min-h-24 w-full resize-none bg-transparent px-2 py-2 text-sm leading-6 text-slate-950 outline-none placeholder:text-slate-400"
                                id="ai-message"
                                name="message"
                                maxlength="{{ $promptLimit }}"
                                placeholder="Ask a question, paste code, or request a review…"
                                required
                                data-ai-textarea
                            ></textarea>
                            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-stone-200 px-2 pt-2">
                                <div class="flex min-w-0 items-center gap-2 text-xs font-bold text-slate-500" role="status" aria-live="polite">
                                    <span class="h-2 w-2 shrink-0 rounded-full bg-emerald-500" data-ai-status-dot></span>
                                    <span class="truncate" data-ai-status>Ready · responses stay plain text for safety</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="hidden text-xs text-slate-400 sm:inline"><kbd class="rounded border border-stone-300 bg-white px-1.5 py-0.5 font-sans">Enter</kbd> send</span>
                                    <span class="min-w-14 text-right text-xs font-bold text-slate-400" data-ai-character-count>0 / {{ number_format($promptLimit) }}</span>
                                    <button class="hidden rounded-xl border border-stone-300 bg-white px-3 py-2 text-sm font-bold text-slate-700 transition hover:border-orange-300 hover:text-orange-800" type="button" data-ai-stop>Stop</button>
                                    <button class="flex items-center gap-2 rounded-xl bg-teal-700 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-950 disabled:cursor-not-allowed disabled:opacity-50" type="submit" data-ai-submit>
                                        <span data-ai-submit-label>Send</span>
                                        <span aria-hidden="true">↑</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <p class="text-center text-[0.7rem] leading-5 text-slate-400">Local AI can make mistakes. Review generated code, commands, and architecture decisions before using them.</p>
                    </form>
                </section>

                <template data-ai-user-template>
                    <article class="flex min-w-0 max-w-full flex-row-reverse gap-3" data-ai-message-role="user">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-slate-950 text-xs font-bold text-white" aria-hidden="true">You</span>
                        <div class="group grid min-w-0 max-w-[min(100%_-_3rem,48rem)] justify-items-end gap-2">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-500"><span>You</span><span>Now</span></div>
                            <pre class="min-w-0 max-w-full whitespace-pre-wrap break-words [overflow-wrap:anywhere] rounded-2xl rounded-tr-sm bg-slate-950 px-4 py-3 font-sans text-sm leading-7 text-white" data-ai-message-content></pre>
                        </div>
                    </article>
                </template>
                <template data-ai-assistant-template>
                    <article class="flex min-w-0 max-w-full gap-3" data-ai-message-role="assistant" data-ai-streaming>
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-teal-100 text-xs font-bold text-teal-900" aria-hidden="true">AI</span>
                        <div class="group grid min-w-0 max-w-[min(100%_-_3rem,48rem)] justify-items-start gap-2">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-500"><span>Assistant</span><span data-ai-message-state>Thinking</span></div>
                            <pre class="min-h-12 min-w-0 max-w-full whitespace-pre-wrap break-words [overflow-wrap:anywhere] rounded-2xl rounded-tl-sm border border-stone-200 bg-white px-4 py-3 font-sans text-sm leading-7 text-slate-800 shadow-sm" data-ai-message-content></pre>
                            <button class="hidden rounded-lg px-2 py-1 text-xs font-bold text-slate-400 transition hover:bg-stone-100 hover:text-teal-800" type="button" data-ai-copy>Copy response</button>
                        </div>
                    </article>
                </template>
            @else
                <section class="overflow-hidden rounded-3xl border border-stone-200 bg-white shadow-lg shadow-slate-900/5">
                    <div class="grid gap-8 bg-[radial-gradient(circle_at_top_right,#ccfbf1_0%,transparent_34%),linear-gradient(135deg,#ffffff_20%,#f5f5f4_100%)] p-6 sm:p-9 lg:grid-cols-[minmax(0,1fr)_18rem] lg:items-center">
                        <div class="max-w-2xl">
                            <span class="inline-flex items-center gap-2 rounded-full border border-teal-200 bg-white/80 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.12em] text-teal-800"><span aria-hidden="true">✦</span> Your private learning copilot</span>
                            <h2 class="mt-5 text-3xl font-bold leading-tight text-slate-950 sm:text-4xl">Move from a question to working code with a focused AI partner.</h2>
                            <p class="mt-4 max-w-xl text-base leading-7 text-slate-600">Choose the specialist that fits your task. Each conversation keeps its own mode, model, and Laravel-owned history.</p>
                        </div>
                        <div class="grid gap-3 rounded-3xl border border-white/80 bg-white/75 p-4 shadow-xl shadow-teal-900/10 backdrop-blur">
                            <div class="flex items-center gap-3 rounded-2xl bg-slate-950 p-3 text-white"><span class="grid h-9 w-9 place-items-center rounded-xl bg-teal-400 text-slate-950">1</span><span class="text-sm font-bold">Choose a mode</span></div>
                            <div class="flex items-center gap-3 rounded-2xl border border-stone-200 bg-white p-3"><span class="grid h-9 w-9 place-items-center rounded-xl bg-orange-100 text-orange-800">2</span><span class="text-sm font-bold text-slate-800">Ask a focused question</span></div>
                            <div class="flex items-center gap-3 rounded-2xl border border-stone-200 bg-white p-3"><span class="grid h-9 w-9 place-items-center rounded-xl bg-teal-100 text-teal-800">3</span><span class="text-sm font-bold text-slate-800">Iterate with context</span></div>
                        </div>
                    </div>

                    <div class="grid gap-4 border-t border-stone-200 p-5 sm:p-6 md:grid-cols-3">
                        @foreach ($modes as $mode => $configuration)
                            <form class="grid content-start gap-4 rounded-2xl border border-stone-200 p-5 transition hover:-translate-y-0.5 hover:border-teal-600 hover:shadow-lg hover:shadow-slate-900/5" method="POST" action="{{ route('cabinet.ai.conversations.store') }}">
                                @csrf
                                <input type="hidden" name="mode" value="{{ $mode }}">
                                <div class="flex items-start justify-between gap-3">
                                    <span class="grid h-11 w-11 place-items-center rounded-2xl {{ $mode === 'coding' ? 'bg-slate-950 text-teal-300' : ($mode === 'architecture' ? 'bg-orange-100 text-orange-800' : 'bg-teal-100 text-teal-900') }}" aria-hidden="true">{{ $mode === 'coding' ? '</>' : ($mode === 'architecture' ? '◇' : '✦') }}</span>
                                    <span class="rounded-full bg-stone-100 px-2.5 py-1 text-[0.68rem] font-bold text-slate-500">{{ $configuration['model_profile'] }}</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-slate-950">{{ $configuration['label'] }}</h3>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $configuration['recommended_for'] }}</p>
                                </div>
                                <div class="mt-auto border-t border-stone-200 pt-4">
                                    <p class="truncate font-mono text-xs font-bold text-slate-400">{{ $configuration['model_name'] }}</p>
                                    <button class="mt-3 flex w-full items-center justify-between rounded-xl bg-stone-100 px-4 py-3 text-sm font-bold text-slate-800 transition hover:bg-teal-700 hover:text-white" type="submit">
                                        Start with this mode <span aria-hidden="true">→</span>
                                    </button>
                                </div>
                            </form>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </section>
@endsection
