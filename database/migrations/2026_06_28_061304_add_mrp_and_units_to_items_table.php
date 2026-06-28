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
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('mrp', 10, 2)->nullable()->after('tax_category_id');
            $table->decimal('trade_price', 10, 2)->nullable()->after('mrp');
            $table->foreignId('secondary_unit_id')->nullable()->after('uom_id')->constrained('uoms')->nullOnDelete();
            $table->integer('conversion_factor')->default(1)->after('secondary_unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['secondary_unit_id']);
            $table->dropColumn(['mrp', 'trade_price', 'secondary_unit_id', 'conversion_factor']);
        });
    }
};
