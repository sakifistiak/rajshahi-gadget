<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Months the customer chose on the SSLCommerz EMI page (EMI orders only).
            $table->unsignedTinyInteger('payment_emi_instalment')->nullable()->after('payment_card_type');
            // Set when SSLCommerz reports risk_level = 1: hold delivery until the customer is verified.
            $table->string('payment_risk_title')->nullable()->after('payment_emi_instalment');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_emi_instalment', 'payment_risk_title']);
        });
    }
};
