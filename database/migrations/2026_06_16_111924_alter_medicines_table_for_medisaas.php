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
        Schema::create('medicine_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. Box, Strip, Piece
            $table->string('short_name'); // e.g. bx, str, pc
            $table->integer('base_unit_multiplier')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('medicines', function (Blueprint $table) {
            $table->string('barcode')->nullable()->after('id')->unique();
            $table->foreignId('category_id')->nullable()->after('barcode')->constrained('medicine_categories')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->after('strength')->constrained('units')->nullOnDelete();
            $table->decimal('buy_price', 10, 2)->default(0)->after('company_name');
            $table->decimal('sell_price', 10, 2)->default(0)->after('buy_price');
            $table->decimal('vat_percent', 5, 2)->default(0)->after('sell_price');
            $table->integer('reorder_level')->default(10)->after('vat_percent');
        });
    }

    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['unit_id']);
            $table->dropColumn(['barcode', 'category_id', 'unit_id', 'buy_price', 'sell_price', 'vat_percent', 'reorder_level']);
        });

        Schema::dropIfExists('units');
        Schema::dropIfExists('medicine_categories');
    }
};
