<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('contacts', 'name')) {
            Schema::table('contacts', function (Blueprint $table): void {
                $table->string('name')->nullable()->after('contact_group_id');
            });
        }

        DB::table('contacts')
            ->select(['id', 'first_name', 'last_name', 'phone'])
            ->orderBy('id')
            ->chunkById(100, function ($contacts): void {
                foreach ($contacts as $contact) {
                    DB::table('contacts')
                        ->where('id', $contact->id)
                        ->update([
                            'name' => trim($contact->first_name.' '.$contact->last_name),
                            'phone' => $contact->phone ?? '',
                        ]);
                }
            });

        Schema::table('contacts', function (Blueprint $table): void {
            $table->string('name')->nullable(false)->change();
            $table->string('phone', 32)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table): void {
            $table->string('phone', 32)->nullable()->change();
            $table->dropColumn('name');
        });
    }
};
