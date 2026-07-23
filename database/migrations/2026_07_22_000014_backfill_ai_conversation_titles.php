<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('ai_conversations')
            ->where('title', 'New conversation')
            ->orderBy('id')
            ->chunkById(100, function ($conversations): void {
                foreach ($conversations as $conversation) {
                    $prompt = DB::table('ai_messages')
                        ->where('ai_conversation_id', $conversation->id)
                        ->where('role', 'user')
                        ->orderBy('created_at')
                        ->orderBy('id')
                        ->value('content');

                    if (! is_string($prompt) || trim($prompt) === '') {
                        continue;
                    }

                    $normalized = preg_replace('/\s+/', ' ', trim($prompt)) ?? 'New conversation';

                    DB::table('ai_conversations')
                        ->where('id', $conversation->id)
                        ->update(['title' => Str::limit($normalized, 80, '…')]);
                }
            });
    }

    public function down(): void
    {
        // Data backfills are intentionally not reversed.
    }
};
