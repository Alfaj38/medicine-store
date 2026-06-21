<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_item_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmaceutical_industry_id')
                  ->constrained('pharmaceutical_industries')
                  ->onDelete('cascade');
            $table->foreignId('item_type_id')
                  ->constrained('item_types')
                  ->onDelete('cascade');
            $table->timestamps();
            $table->unique(['pharmaceutical_industry_id', 'item_type_id'], 'company_item_type_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_item_type');
    }
};
?>
