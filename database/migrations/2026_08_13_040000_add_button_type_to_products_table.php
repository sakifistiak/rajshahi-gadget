<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // 'auto' follows is_preorder (existing behaviour); 'buy_now' and
            // 'preorder' let an admin force the storefront button label
            // independently of the Pre-Order flag.
            $table->string('button_type', 20)->default('auto')->after('is_preorder');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('button_type');
        });
    }
};
