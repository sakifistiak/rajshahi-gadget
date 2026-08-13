<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('filter_attributes', function (Blueprint $table) {
            // Comma-separated Specification labels (e.g. "RAM, Memory") this
            // attribute auto-reads its value from, so admins keep entering
            // free-text specs as usual and the shop filter derives itself.
            $table->string('match_labels', 255)->nullable()->after('key');
        });
    }

    public function down(): void
    {
        Schema::table('filter_attributes', function (Blueprint $table) {
            $table->dropColumn('match_labels');
        });
    }
};
