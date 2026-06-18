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
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->decimal('mrp', 10, 2)->default(0)->after('unit_price');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->decimal('mrp', 10, 2)->default(0)->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn('mrp');
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropColumn('mrp');
        });
    }
};
