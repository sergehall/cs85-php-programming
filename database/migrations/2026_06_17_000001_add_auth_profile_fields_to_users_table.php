<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->index()->after('password');
            $table->string('github_id')->nullable()->unique()->after('role');
            $table->string('github_username')->nullable()->after('github_id');
            $table->string('github_avatar_url')->nullable()->after('github_username');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['github_id']);
            $table->dropIndex(['role']);
            $table->dropColumn(['role', 'github_id', 'github_username', 'github_avatar_url']);
        });
    }
};
