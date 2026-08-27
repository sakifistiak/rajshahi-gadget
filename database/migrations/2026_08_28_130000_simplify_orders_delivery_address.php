<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Checkout no longer collects the full Division / District / Upazila / Postal
     * Code breakdown — just a free-text address plus a two-way "delivery area"
     * (Inside Dhaka / Outside Dhaka) that drives the courier fee.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_area', 20)->nullable()->after('address');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['district', 'division', 'upazila', 'union_area', 'postal_code']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('district', 100)->nullable()->after('address');
            $table->string('division', 100)->nullable()->after('district');
            $table->string('upazila', 100)->nullable()->after('division');
            $table->string('union_area', 150)->nullable()->after('upazila');
            $table->string('postal_code', 20)->nullable()->after('union_area');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('delivery_area');
        });
    }
};
