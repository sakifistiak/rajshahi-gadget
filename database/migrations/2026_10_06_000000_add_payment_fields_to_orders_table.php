<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // unpaid (COD and new online orders), paid, failed, cancelled
            $table->string('payment_status', 20)->default('unpaid')->after('payment_method');
            $table->string('payment_val_id')->nullable()->after('payment_status');
            $table->string('payment_bank_tran_id')->nullable()->after('payment_val_id');
            $table->string('payment_card_type', 60)->nullable()->after('payment_bank_tran_id');
            $table->timestamp('paid_at')->nullable()->after('payment_card_type');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'payment_val_id', 'payment_bank_tran_id', 'payment_card_type', 'paid_at']);
        });
    }
};
