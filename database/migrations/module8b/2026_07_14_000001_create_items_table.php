<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('module8b')->create('items', function (Blueprint $table): void {
            $table->id();
            $table->string('item_name');
            $table->string('category')->nullable();
            $table->integer('quantity')->default(0);
            $table->date('purchase_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('module8b')->dropIfExists('items');
    }
};
