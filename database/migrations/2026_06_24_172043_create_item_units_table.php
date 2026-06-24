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
        Schema::create('item_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->string('unit_name'); // e.g. Box, Strip, Tablet
            $table->integer('factor')->default(1); // multiplier to get to base unit
            $table->boolean('is_base_unit')->default(false);
            $table->boolean('is_purchase_unit')->default(false);
            $table->boolean('is_sales_unit')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_units');
    }
};
