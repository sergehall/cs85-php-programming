@extends('layouts.app', ['title' => 'AI Assistant - Cabinet - CS85'])

@section('content')
    @include('partials.cabinet-nav')

    @if (session('status'))
        <section class="rounded-lg border border-emerald-300 bg-emerald-50 p-4 text-sm font-bold text-emerald-800" role="status">
            {{ session('status') }}
        </section>
    @endif

    <section class="grid items-start gap-5 lg:grid-cols-[280px_minmax(0,1fr)]" data-ai-chat>
        <aside class="grid min-w-0 content-start gap-4 rounded-lg border border-stone-300 bg-white p-4">
            <div class="min-w-0">
                <p class="text-xs font-bold uppercase tracking-normal text-orange-700">Local AI</p>
                <h1 class="mt-2 text-2xl font-bold text-slate-950">Learning assistant</h1>
                <p class="mt-2 break-words text-sm leading-6 text-slate-600">Conversations stay in this Laravel application and use models loaded in LM Studio.</p>
            </div>

            <form class="grid min-w-0 gap-3 border-t border-stone-200 pt-4" method="POST" action="{{ route('cabinet.ai.conversations.store') }}">
                @csrf
                <label class="grid min-w-0 gap-2 text-sm font-bold text-slate-700">
                    New conversation mode
                    <select class="block w-full min-w-0 max-w-full rounded-lg border border-stone-300 bg-white px-3 py-3 font-normal text-slate-950" name="mode" required>
                        @foreach ($modes as $mode => $configuration)
                            <option value="{{ $mode }}">{{ $configuration['label'] }} — {{ $configuration['model_name'] }}</option>
                        @endforeach
                    </select>
                </label>
                <p class="break-words text-xs leading-5 text-slate-500">A mode selects one specialized local model for the entire conversation.</p>
                <button class="w-full rounded-lg bg-teal-800 px-4 py-3 text-sm font-bold text-white transition hover:bg-slate-950" type="submit">
                    Start conversation
                </button>
            </form>

            <div class="grid min-w-0 gap-2 border-t border-stone-200 pt-4" aria-label="AI conversation history">
                <p class="text-xs font-bold uppercase tracking-normal text-slate-500">Recent conversations</p>
                @forelse ($conversations as $conversation)
                    <a
                        class="grid min-w-0 gap-1 rounded-lg border px-3 py-3 no-underline transition {{ $activeConversation?->is($conversation) ? 'border-teal-700 bg-teal-50' : 'border-stone-200 bg-stone-50 hover:border-teal-700' }}"
                        href="{{ route('cabinet.ai.conversations.show', $conversation->public_uuid) }}"
                    >
                        <span class="truncate text-sm font-bold text-slate-950">{{ $conversation->title }}</span>
                        <span class="truncate text-xs font-bold text-slate-500">{{ $modes[$conversation->mode->value]['label'] ?? $conversation->mode->value }} · {{ $conversation->messages_count }} messages</span>
                    </a>
                @empty
                    <p class="rounded-lg border border-dashed border-stone-300 p-3 text-sm leading-6 text-slate-500">No conversations yet.</p>
                @endforelse
            </div>
        </aside>

        <div class="grid min-w-0 content-start gap-4">
            @php
                $displayedMode = $modes[$activeConversation?->mode->value ?? 'general'];
            @endphp
            <details class="group rounded-lg border border-stone-300 bg-white" data-ai-model-guide>
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 rounded-lg p-4 transition hover:bg-stone-50 [&::-webkit-details-marker]:hidden" data-ai-model-guide-summary>
                    <div class="min-w-0">
                        <p class="truncate text-xs font-bold uppercase tracking-normal text-teal-800">{{ $displayedMode['label'] }}</p>
                        <p class="mt-1 truncate text-lg font-bold text-slate-950">{{ $displayedMode['model_name'] }}</p>
                    </div>
                    <div class="flex shrink-0 items-center gap-2">
                        <span class="rounded-full px-2.5 py-1 text-[0.68rem] font-bold uppercase tracking-normal {{ $activeConversation ? 'bg-teal-800 text-white' : 'bg-stone-100 text-slate-600' }}">
                            {{ $activeConversation ? 'Active' : 'Default' }}
                        </span>
                        <span class="hidden text-xs font-bold text-slate-500 sm:inline">Model details</span>
                        <span class="grid h-8 w-8 place-items-center rounded-full border border-stone-300 text-sm font-bold text-slate-600 transition group-open:rotate-180" aria-hidden="true">⌄</span>
                    </div>
                </summary>

                <div class="grid gap-4 border-t border-stone-200 p-5">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-normal text-orange-700">Three modes · three local models</p>
                        <h2 class="mt-2 text-2xl font-bold text-slate-950">Choose the right AI model</h2>
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Each mode routes the conversation to a different model loaded by LM Studio. The selected model stays fixed for the conversation.</p>
                    </div>

                    <div class="grid gap-3 md:grid-cols-3">
                        @foreach ($modes as $mode => $configuration)
                            @php
                                $isActiveMode = $activeConversation?->mode->value === $mode;
                            @endphp
                            <article class="grid content-start gap-3 rounded-lg border p-4 {{ $isActiveMode ? 'border-teal-700 bg-teal-50 shadow-sm' : 'border-stone-200 bg-stone-50' }}">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold uppercase tracking-normal {{ $isActiveMode ? 'text-teal-800' : 'text-slate-500' }}">{{ $configuration['label'] }}</p>
                                        <h3 class="mt-1 text-lg font-bold text-slate-950">{{ $configuration['model_name'] }}</h3>
                                    </div>
                                    @if ($isActiveMode)
                                        <span class="shrink-0 rounded-full bg-teal-800 px-2.5 py-1 text-[0.68rem] font-bold uppercase tracking-normal text-white">Active</span>
                                    @endif
                                </div>

                                <p class="text-sm leading-6 text-slate-600">{{ $configuration['recommended_for'] }}</p>

                                <dl class="grid gap-2 border-t border-stone-200 pt-3 text-xs">
                                    <div class="grid gap-1">
                                        <dt class="font-bold uppercase tracking-normal text-slate-500">LM Studio model ID</dt>
                                        <dd class="break-all font-mono font-bold text-slate-800">{{ $configuration['model'] }}</dd>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <dt class="font-bold uppercase tracking-normal text-slate-500">Local build</dt>
                                        <dd class="rounded-md border border-stone-300 bg-white px-2 py-1 font-bold text-slate-700">{{ $configuration['model_profile'] }}</dd>
                                    </div>
                                </dl>
                            </article>
                        @endforeach
                    </div>
                </div>
            </details>

            @if ($activeConversation)
                <header class="flex flex-col gap-4 rounded-lg border border-stone-300 bg-white p-5 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-normal text-teal-800">{{ $modes[$activeConversation->mode->value]['label'] }}</p>
                        <h2 class="mt-2 truncate text-2xl font-bold text-slate-950" data-ai-conversation-title>{{ $activeConversation->title }}</h2>
                        <p class="mt-2 text-sm font-bold text-slate-700">{{ $modes[$activeConversation->mode->value]['model_name'] }}</p>
                        <p class="mt-1 break-all font-mono text-xs font-bold text-slate-500">{{ $activeConversation->model }}</p>
                    </div>
                    <form method="POST" action="{{ route('cabinet.ai.conversations.destroy', $activeConversation->public_uuid) }}">
                        @csrf
                        @method('DELETE')
                        <button class="rounded-lg border border-orange-300 bg-white px-4 py-2 text-sm font-bold text-orange-700 transition hover:bg-orange-50" type="submit">
                            Delete
                        </button>
                    </form>
                </header>

                <section class="grid min-h-96 content-start gap-4 rounded-lg border border-stone-300 bg-white p-5" aria-live="polite" data-ai-messages>
                    @forelse ($activeConversation->messages as $message)
                        <article class="grid gap-2 {{ $message->role === 'user' ? 'justify-items-end' : 'justify-items-start' }}" data-ai-message-role="{{ $message->role }}">
                            <span class="text-xs font-bold uppercase tracking-normal {{ $message->role === 'user' ? 'text-orange-700' : 'text-teal-800' }}">
                                {{ $message->role === 'user' ? 'You' : 'Assistant' }}
                            </span>
                            <pre class="max-w-[min(100%,48rem)] whitespace-pre-wrap break-words rounded-lg px-4 py-3 font-sans text-sm leading-7 {{ $message->role === 'user' ? 'bg-slate-950 text-white' : 'border border-stone-200 bg-stone-50 text-slate-800' }}">{{ $message->content }}</pre>
                        </article>
                    @empty
                        <div class="grid min-h-72 place-items-center text-center" data-ai-empty-state>
                            <div class="max-w-lg">
                                <p class="text-lg font-bold text-slate-950">Start a private local conversation</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Ask a programming question, paste code for review, request a quiz, or discuss a Laravel architecture decision.</p>
                            </div>
                        </div>
                    @endforelse
                </section>

                <form
                    class="grid gap-3 rounded-lg border border-stone-300 bg-white p-4"
                    method="POST"
                    action="{{ route('cabinet.ai.conversations.messages.stream', $activeConversation->public_uuid) }}"
                    data-ai-message-form
                >
                    @csrf
                    <label class="grid gap-2 text-sm font-bold text-slate-700">
                        Message
                        <textarea class="min-h-32 resize-y rounded-lg border border-stone-300 px-3 py-3 font-normal leading-6 text-slate-950 outline-none transition focus:border-teal-700" name="message" maxlength="{{ $promptLimit }}" placeholder="Ask the local AI assistant…" required></textarea>
                    </label>
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <p class="text-xs font-bold text-slate-500" data-ai-status>Plain text output · {{ number_format($promptLimit) }} character limit</p>
                        <div class="flex gap-2">
                            <button class="hidden rounded-lg border border-stone-300 px-4 py-3 text-sm font-bold text-slate-700" type="button" data-ai-stop>Stop</button>
                            <button class="rounded-lg bg-teal-800 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-950 disabled:cursor-not-allowed disabled:opacity-50" type="submit" data-ai-submit>
                                Send
                            </button>
                        </div>
                    </div>
                </form>

                <template data-ai-user-template>
                    <article class="grid justify-items-end gap-2" data-ai-message-role="user">
                        <span class="text-xs font-bold uppercase tracking-normal text-orange-700">You</span>
                        <pre class="max-w-[min(100%,48rem)] whitespace-pre-wrap break-words rounded-lg bg-slate-950 px-4 py-3 font-sans text-sm leading-7 text-white" data-ai-message-content></pre>
                    </article>
                </template>
                <template data-ai-assistant-template>
                    <article class="grid justify-items-start gap-2" data-ai-message-role="assistant">
                        <span class="text-xs font-bold uppercase tracking-normal text-teal-800">Assistant</span>
                        <pre class="max-w-[min(100%,48rem)] whitespace-pre-wrap break-words rounded-lg border border-stone-200 bg-stone-50 px-4 py-3 font-sans text-sm leading-7 text-slate-800" data-ai-message-content></pre>
                    </article>
                </template>
            @else
                <section class="grid min-h-[32rem] place-items-center rounded-lg border border-stone-300 bg-white p-8 text-center">
                    <div class="max-w-xl">
                        <p class="text-xs font-bold uppercase tracking-normal text-teal-800">LM Studio powered</p>
                        <h2 class="mt-3 text-3xl font-bold text-slate-950">Choose a mode and start a conversation.</h2>
                        <p class="mt-4 leading-7 text-slate-600">Use General Tutor for learning, Coding Assistant for implementation, or Architecture Advisor for system design. Start LM Studio and load the selected model before sending your first message.</p>
                    </div>
                </section>
            @endif
        </div>
    </section>
@endsection
