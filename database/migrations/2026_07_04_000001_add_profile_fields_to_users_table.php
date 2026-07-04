<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 80)->nullable();
            $table->string('last_name', 80)->nullable();
            $table->string('github_profile_url')->nullable();
            $table->string('linkedin_profile_url')->nullable();
            $table->text('bio')->nullable();
            $table->text('technical_skills')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'github_profile_url',
                'linkedin_profile_url',
                'bio',
                'technical_skills',
            ]);
        });
    }
};
