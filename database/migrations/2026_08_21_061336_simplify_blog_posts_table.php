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
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn(['excerpt', 'category_tag', 'read_minutes', 'author_name', 'author_role', 'is_featured']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->text('excerpt')->nullable()->after('title');
            $table->string('category_tag')->nullable()->after('content');
            $table->unsignedTinyInteger('read_minutes')->default(5)->after('featured_image');
            $table->string('author_name')->nullable()->after('read_minutes');
            $table->string('author_role')->nullable()->after('author_name');
            $table->boolean('is_featured')->default(false)->after('published_at');
        });
    }
};
