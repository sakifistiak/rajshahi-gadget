<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Split the "Last updated" date off the show_title toggle so each can be hidden on its own.
     */
    public function up(): void
    {
        Schema::table('custom_pages', function (Blueprint $table) {
            $table->boolean('show_updated_at')->default(true)->after('show_title');
        });

        // Until now show_title hid both, so pages that hid the title also hid the date: keep them that way.
        DB::table('custom_pages')->where('show_title', false)->update(['show_updated_at' => false]);
    }

    public function down(): void
    {
        Schema::table('custom_pages', function (Blueprint $table) {
            $table->dropColumn('show_updated_at');
        });
    }
};
