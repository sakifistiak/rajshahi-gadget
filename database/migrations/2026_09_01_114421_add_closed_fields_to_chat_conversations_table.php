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
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->timestamp('closed_at')->nullable()->after('last_message_at');
            $table->enum('closed_by', ['customer', 'agent', 'auto'])->nullable()->after('closed_at');
            $table->index('closed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->dropIndex(['closed_at']);
            $table->dropColumn(['closed_at', 'closed_by']);
        });
    }
};
