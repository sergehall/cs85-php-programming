<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_requests', function (Blueprint $table): void {
            $table->foreignId('user_message_id')
                ->nullable()
                ->after('ai_conversation_id')
                ->constrained('ai_messages')
                ->nullOnDelete();
        });

        DB::table('ai_requests')
            ->whereNull('user_message_id')
            ->orderBy('id')
            ->chunkById(100, function ($requests): void {
                foreach ($requests as $request) {
                    $messageId = DB::table('ai_messages')
                        ->where('ai_conversation_id', $request->ai_conversation_id)
                        ->where('role', 'user')
                        ->where('created_at', '<=', $request->created_at)
                        ->orderByDesc('created_at')
                        ->orderByDesc('id')
                        ->value('id');

                    if ($messageId !== null) {
                        DB::table('ai_requests')
                            ->where('id', $request->id)
                            ->update(['user_message_id' => $messageId]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('ai_requests', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('user_message_id');
        });
    }
};
