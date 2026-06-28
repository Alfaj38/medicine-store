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
        Schema::table('sales', function (Blueprint $table) {
            $table->index('sale_date');
            $table->index(['company_id', 'branch_id']); // Composite Index for Multi-tenant
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->index('purchase_date');
            $table->index(['company_id', 'branch_id']); // Composite Index
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['sale_date']);
            $table->dropIndex(['company_id', 'branch_id']);
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex(['purchase_date']);
            $table->dropIndex(['company_id', 'branch_id']);
        });
    }
};
