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
        Schema::create('stock_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('medicine_id')->constrained('medicines')->cascadeOnDelete();
            
            $table->string('batch_no')->nullable();
            
            $table->string('reference_type'); // e.g. App\Models\Purchase, App\Models\Sale
            $table->unsignedBigInteger('reference_id');
            
            $table->enum('type', ['in', 'out', 'adjustment']);
            $table->integer('quantity'); // Positive number for IN, Negative for OUT
            $table->integer('balance_after')->default(0);
            
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_ledgers');
    }
};
