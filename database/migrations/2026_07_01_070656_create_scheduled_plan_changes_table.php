<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scheduled_plan_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            
            $table->foreignId('current_plan_id')->constrained('subscription_plans')->cascadeOnDelete();
            $table->foreignId('next_plan_id')->constrained('subscription_plans')->cascadeOnDelete();
            $table->foreignId('next_price_id')->constrained('subscription_plan_prices')->cascadeOnDelete();
            
            $table->timestamp('effective_date');
            
            $table->enum('status', ['pending', 'processed', 'cancelled'])->default('pending');
            $table->text('failure_reason')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_plan_changes');
    }
};
