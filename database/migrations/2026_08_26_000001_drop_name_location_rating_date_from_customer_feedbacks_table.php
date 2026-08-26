<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customer_feedbacks', function (Blueprint $table) {
            $table->dropColumn(['name', 'location', 'rating', 'date']);
            $table->text('message')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_feedbacks', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('location')->nullable();
            $table->decimal('rating', 2, 1)->default(5.0);
            $table->date('date')->nullable();
        });
    }
};
