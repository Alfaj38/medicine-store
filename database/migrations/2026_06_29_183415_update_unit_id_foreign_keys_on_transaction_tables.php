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
        // purchase_items
        try {
            Schema::table('purchase_items', function (Blueprint $table) {
                $table->dropForeign(['unit_id']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('purchase_items', function (Blueprint $table) {
                $table->foreign('unit_id')->references('id')->on('uoms')->nullOnDelete();
            });
        } catch (\Exception $e) {}

        // sale_items
        try {
            Schema::table('sale_items', function (Blueprint $table) {
                $table->dropForeign(['unit_id']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('sale_items', function (Blueprint $table) {
                $table->foreign('unit_id')->references('id')->on('uoms')->nullOnDelete();
            });
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('purchase_items', function (Blueprint $table) {
                $table->dropForeign(['unit_id']);
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('purchase_items', function (Blueprint $table) {
                $table->foreign('unit_id')->references('id')->on('item_units')->nullOnDelete();
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('sale_items', function (Blueprint $table) {
                $table->dropForeign(['unit_id']);
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('sale_items', function (Blueprint $table) {
                $table->foreign('unit_id')->references('id')->on('item_units')->nullOnDelete();
            });
        } catch (\Exception $e) {}
    }
};
