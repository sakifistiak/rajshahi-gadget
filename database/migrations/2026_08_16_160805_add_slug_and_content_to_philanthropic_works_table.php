<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('philanthropic_works', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
            $table->longText('content')->nullable()->after('summary');
        });

        // Backfill slugs for any rows seeded before this column existed.
        $slugCounts = [];
        foreach (DB::table('philanthropic_works')->whereNull('slug')->orderBy('id')->get() as $row) {
            $base = Str::slug($row->title);
            $slug = $base;
            $n = $slugCounts[$base] ?? 0;
            if ($n > 0) {
                $slug = $base . '-' . ($n + 1);
            }
            $slugCounts[$base] = $n + 1;
            DB::table('philanthropic_works')->where('id', $row->id)->update(['slug' => $slug]);
        }

        Schema::table('philanthropic_works', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('philanthropic_works', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'content']);
        });
    }
};
