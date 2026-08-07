<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('phone', 30);
            $table->string('email')->nullable();
            $table->text('address');
            $table->string('district', 100);
            $table->text('note')->nullable();
            $table->string('payment_method', 30);
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('shipping_fee')->default(0);
            $table->unsignedBigInteger('total');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->string('product_name');
            $table->string('product_slug');
            $table->unsignedBigInteger('unit_price');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('line_total');
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('order_items'); Schema::dropIfExists('orders'); }
};
