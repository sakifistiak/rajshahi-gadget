<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('philanthropic_works', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('place');
            $table->string('image')->nullable();
            $table->text('summary');
            $table->date('date');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('philanthropic_works'); }
};
