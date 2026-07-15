<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->select(['id', 'public_uuid'])
            ->orderBy('id')
            ->get()
            ->each(function (object $user): void {
                if (is_string($user->public_uuid) && Str::isUuid($user->public_uuid)) {
                    return;
                }

                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['public_uuid' => (string) Str::uuid()]);
            });

        Schema::table('users', function (Blueprint $table): void {
            $table->uuid('public_uuid')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->uuid('public_uuid')->nullable()->change();
        });
    }
};
