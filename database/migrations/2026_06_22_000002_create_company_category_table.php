<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('company_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->onDelete('cascade');
            $table->foreignId('category_id')
                ->constrained('item_categories')
                ->onDelete('cascade');
            $table->timestamps();
            $table->unique(['company_id', 'category_id'], 'company_category_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_category');
    }
};
?>
