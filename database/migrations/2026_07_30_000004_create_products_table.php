<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('condition_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('price');
            $table->unsignedInteger('compare_at_price')->nullable();
            $table->decimal('rating', 3, 1)->default(0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->string('badge')->nullable();
            $table->text('description');
            $table->boolean('in_stock')->default(true);
            $table->string('warranty')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('products'); }
};
