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
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn(['buy_price', 'sell_price', 'vat_percent']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->decimal('buy_price', 10, 2)->default(0)->after('company_name');
            $table->decimal('sell_price', 10, 2)->default(0)->after('buy_price');
            $table->decimal('vat_percent', 5, 2)->default(0)->after('sell_price');
        });
    }
};
