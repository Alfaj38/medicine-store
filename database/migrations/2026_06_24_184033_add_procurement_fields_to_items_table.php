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
            $table->foreignId('preferred_supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('alternate_supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->integer('reorder_level')->default(0);
            $table->integer('reorder_qty')->default(0);
            $table->integer('safety_stock')->default(0);
            $table->integer('lead_time_days')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['preferred_supplier_id']);
            $table->dropForeign(['alternate_supplier_id']);
            $table->dropColumn([
                'preferred_supplier_id',
                'alternate_supplier_id',
                'reorder_level',
                'reorder_qty',
                'safety_stock',
                'lead_time_days'
            ]);
        });
    }
};
