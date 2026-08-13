<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('division', 100)->nullable()->after('district');
            $table->string('upazila', 100)->nullable()->after('division');
            $table->string('union_area', 150)->nullable()->after('upazila');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['division', 'upazila', 'union_area']);
        });
    }
};
