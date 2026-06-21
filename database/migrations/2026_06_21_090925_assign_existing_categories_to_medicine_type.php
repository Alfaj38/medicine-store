<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $medicineType = DB::table('item_types')->where('name', 'Medicine')->first();
        if ($medicineType) {
            DB::table('item_categories')->whereNull('item_type_id')->update(['item_type_id' => $medicineType->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse logic needed for this data migration
    }
};
