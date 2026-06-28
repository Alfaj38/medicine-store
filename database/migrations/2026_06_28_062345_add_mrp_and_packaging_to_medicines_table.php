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
            $table->decimal('mrp', 10, 2)->default(0)->after('company_name');
            $table->foreignId('secondary_unit_id')->nullable()->after('unit_id')->constrained('units')->nullOnDelete();
            $table->integer('conversion_factor')->default(1)->after('secondary_unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropForeign(['secondary_unit_id']);
            $table->dropColumn(['mrp', 'secondary_unit_id', 'conversion_factor']);
        });
    }
};
