<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_method')->default('home_delivery')->after('payment_method');
            $table->foreignId('store_location_id')->nullable()->after('delivery_method')->constrained()->nullOnDelete();
            $table->text('address')->nullable()->change();
            $table->string('district', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('store_location_id');
            $table->dropColumn('delivery_method');
        });
    }
};
