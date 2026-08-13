<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filter_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('key', 60);
            $table->string('label', 60);
            $table->string('unit', 20)->nullable();
            // 'range': numeric min/max shop filter (e.g. RAM, Storage, DPI).
            // 'select': fixed list of text options (e.g. Connection: Wired/Wireless).
            $table->enum('type', ['range', 'select']);
            $table->text('options')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['category_id', 'key']);
        });

        Schema::create('product_filter_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('filter_attribute_id')->constrained()->cascadeOnDelete();
            $table->decimal('numeric_value', 12, 2)->nullable();
            $table->string('text_value', 100)->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'filter_attribute_id'], 'product_filter_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_filter_values');
        Schema::dropIfExists('filter_attributes');
    }
};
