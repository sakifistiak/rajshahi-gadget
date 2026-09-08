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
        // 1. Real-time active sessions table (for live online visitors tracking)
        if (!Schema::hasTable('analytics_active_sessions')) {
            Schema::create('analytics_active_sessions', function (Blueprint $table) {
                $table->id();
                $table->string('session_id', 100)->unique();
                $table->string('ip_address', 45)->nullable();
                $table->text('current_url');
                $table->string('current_title', 255)->nullable();
                $table->string('route_name', 100)->nullable();
                $table->string('viewable_type', 50)->nullable(); // 'product', 'blog_post', etc.
                $table->unsignedBigInteger('viewable_id')->nullable()->index();
                $table->text('referrer')->nullable();
                $table->string('referrer_domain', 100)->nullable()->index();
                $table->string('device_type', 20)->default('desktop')->index(); // mobile, desktop, tablet
                $table->string('browser', 50)->nullable();
                $table->string('platform', 50)->nullable();
                $table->timestamp('first_seen_at')->nullable();
                $table->timestamp('last_active_at')->nullable()->index();
                $table->timestamps();
            });
        }

        // 2. Historical pageviews & reach analytics
        if (!Schema::hasTable('analytics_visits')) {
            Schema::create('analytics_visits', function (Blueprint $table) {
                $table->id();
                $table->string('session_id', 100)->index();
                $table->string('visitor_hash', 64)->index(); // SHA256 of IP + UA for unique visitor count
                $table->string('ip_address', 45)->nullable();
                $table->text('url');
                $table->string('route_name', 100)->nullable()->index();
                $table->string('page_title', 255)->nullable();
                $table->string('viewable_type', 50)->nullable();
                $table->unsignedBigInteger('viewable_id')->nullable()->index();
                $table->text('referrer')->nullable();
                $table->string('referrer_domain', 100)->nullable()->index();
                $table->string('device_type', 20)->default('desktop')->index();
                $table->string('browser', 50)->nullable();
                $table->string('platform', 50)->nullable();
                $table->timestamp('created_at')->nullable()->index();
            });
        }

        // 3. Add views_count to products table if not present
        if (!Schema::hasColumn('products', 'views_count')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('views_count')->default(0)->index()->after('in_stock');
            });
        }

        // 4. Add views_count to blog_posts table if not present
        if (!Schema::hasColumn('blog_posts', 'views_count')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                $table->unsignedBigInteger('views_count')->default(0)->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_active_sessions');
        Schema::dropIfExists('analytics_visits');

        if (Schema::hasColumn('products', 'views_count')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('views_count');
            });
        }

        if (Schema::hasColumn('blog_posts', 'views_count')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                $table->dropColumn('views_count');
            });
        }
    }
};
