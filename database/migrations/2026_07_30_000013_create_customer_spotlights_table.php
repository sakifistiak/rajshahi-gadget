<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('customer_spotlights', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->string('product');
            $table->string('image')->nullable();
            $table->text('quote');
            $table->date('date');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('customer_spotlights'); }
};
