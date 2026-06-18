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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('medicine_id')->constrained('medicines')->cascadeOnDelete();
            
            $table->string('batch_no')->nullable();
            $table->date('expiry_date')->nullable();
            
            $table->integer('quantity')->default(0);
            $table->timestamps();
            
            $table->unique(['company_id', 'branch_id', 'medicine_id', 'batch_no'], 'inventory_unique_batch');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
