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
            $table->integer('bonus_quantity')->default(0)->after('quantity');
            $table->decimal('trade_discount_percent', 8, 2)->nullable()->after('mrp');
            $table->string('offer_type')->nullable()->after('trade_discount_percent');
            $table->decimal('offer_value', 10, 2)->nullable()->after('offer_type');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->decimal('trade_price', 10, 2)->default(0)->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropColumn(['bonus_quantity', 'trade_discount_percent', 'offer_type', 'offer_value']);
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn('trade_price');
        });
    }
};
