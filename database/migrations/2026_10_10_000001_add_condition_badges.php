<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Each condition carries its own product-card badge, so a product shows
        // "BRAND NEW" just by picking its condition.
        Schema::table('conditions', function (Blueprint $table) {
            $table->boolean('badge_active')->default(false)->after('tagline');
            $table->string('badge_text', 30)->nullable()->after('badge_active');
            $table->string('badge_color', 7)->default('#16a34a')->after('badge_text');
        });

        // Per-product opt-out for the condition badge. The hand-written badge
        // already lives in products.badge.
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('hide_condition_badge')->default(false)->after('badge');
        });

        DB::table('conditions')->whereIn('slug', ['intact', 'without-box'])
            ->update(['badge_active' => true, 'badge_text' => 'BRAND NEW', 'badge_color' => '#16a34a']);
        DB::table('conditions')->where('slug', 'pre-owned')
            ->update(['badge_active' => false, 'badge_text' => 'PRE-OWNED', 'badge_color' => '#d97706']);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('hide_condition_badge');
        });

        Schema::table('conditions', function (Blueprint $table) {
            $table->dropColumn(['badge_active', 'badge_text', 'badge_color']);
        });
    }
};
