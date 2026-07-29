<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('excerpt');
            $table->json('content');
            $table->string('category_tag');
            $table->string('featured_image')->nullable();
            $table->unsignedTinyInteger('read_minutes')->default(5);
            $table->string('author_name');
            $table->string('author_role')->nullable();
            $table->date('published_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('blog_posts'); }
};
