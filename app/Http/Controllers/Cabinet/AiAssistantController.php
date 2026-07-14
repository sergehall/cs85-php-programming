<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAiConversationRequest;
use App\Http\Requests\SubmitAiMessageRequest;
use App\Models\AiConversation;
use App\Models\User;
use App\Services\AI\AiConversationService;
use App\Services\AI\Enums\AiMode;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AiAssistantController extends Controller
{
    public function index(Request $request): View
    {
        return $this->workspace($this->authenticatedUser($request));
    }

    public function store(CreateAiConversationRequest $request, AiConversationService $service): RedirectResponse
    {
        $mode = AiMode::from($request->validated('mode'));
        $conversation = $service->createConversation($this->authenticatedUser($request), $mode);

        return redirect()->route('cabinet.ai.conversations.show', $conversation->public_uuid);
    }

    public function show(Request $request, string $conversation): View
    {
        $user = $this->authenticatedUser($request);

        return $this->workspace($user, $this->ownedConversation($user, $conversation));
    }

    public function destroy(Request $request, string $conversation): RedirectResponse
    {
        $user = $this->authenticatedUser($request);
        $this->ownedConversation($user, $conversation)->delete();

        return redirect()
            ->route('cabinet.ai')
            ->with('status', 'AI conversation deleted.');
    }

    public function stream(
        SubmitAiMessageRequest $request,
        string $conversation,
        AiConversationService $service,
    ): StreamedResponse {
        $user = $this->authenticatedUser($request);
        $ownedConversation = $this->ownedConversation($user, $conversation);
        $message = $request->validated('message');

        return response()->stream(function () use ($service, $ownedConversation, $message): void {
            foreach ($service->streamReply($ownedConversation, $message) as $event) {
                $eventName = is_string($event['type'] ?? null) ? $event['type'] : 'message';
                echo "event: {$eventName}\n";
                echo 'data: '.json_encode($event, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n\n";

                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-transform',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    private function workspace(User $user, ?AiConversation $activeConversation = null): View
    {
        $conversations = $user->aiConversations()
            ->withCount('messages')
            ->latest('updated_at')
            ->limit(30)
            ->get();

        $activeConversation?->load('messages');

        return view('cabinet.ai.index', [
            'activeConversation' => $activeConversation,
            'conversations' => $conversations,
            'modes' => config('ai.modes'),
            'promptLimit' => (int) config('ai.limits.prompt_characters'),
        ]);
    }

    private function authenticatedUser(Request $request): User
    {
        $user = $request->user();

        if (! $user instanceof User) {
            abort(403);
        }

        return $user;
    }

    private function ownedConversation(User $user, string $publicUuid): AiConversation
    {
        return $user->aiConversations()
            ->where('public_uuid', $publicUuid)
            ->firstOrFail();
    }
}
