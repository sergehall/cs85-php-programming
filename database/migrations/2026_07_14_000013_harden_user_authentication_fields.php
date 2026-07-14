<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $normalizedEmails = DB::table('users')
            ->select(['id', 'email'])
            ->get()
            ->groupBy(fn (object $user): string => Str::lower(trim((string) $user->email)));

        $collision = $normalizedEmails->first(fn ($users): bool => $users->count() > 1);

        if ($collision !== null) {
            throw new RuntimeException('Cannot normalize user emails because case-insensitive duplicates exist.');
        }

        DB::transaction(function () use ($normalizedEmails): void {
            foreach ($normalizedEmails as $email => $users) {
                DB::table('users')
                    ->where('id', $users->first()->id)
                    ->update(['email' => $email]);
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('password_login_enabled')->default(true)->after('password');
            $table->unsignedBigInteger('mfa_last_used_time_slice')->nullable()->after('mfa_confirmed_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['password_login_enabled', 'mfa_last_used_time_slice']);
        });
    }
};
