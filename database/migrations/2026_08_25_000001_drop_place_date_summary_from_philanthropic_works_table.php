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
        Schema::table('philanthropic_works', function (Blueprint $table) {
            $table->dropColumn(['place', 'date', 'summary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('philanthropic_works', function (Blueprint $table) {
            $table->string('place')->nullable()->after('slug');
            $table->date('date')->nullable()->after('video_url');
            $table->text('summary')->nullable()->after('date');
        });
    }
};
