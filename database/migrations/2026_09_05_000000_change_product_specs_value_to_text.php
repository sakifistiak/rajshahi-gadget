<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Schema's change() instead of a raw MySQL "ALTER TABLE ... MODIFY", so the migration also runs on
    // the SQLite database the test suite uses. On MySQL it produces the same NOT NULL column.
    public function up(): void
    {
        Schema::table('product_specs', function (Blueprint $table) {
            $table->text('value')->change();
        });
    }

    public function down(): void
    {
        Schema::table('product_specs', function (Blueprint $table) {
            $table->string('value')->change();
        });
    }
};
