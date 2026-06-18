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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('max_branches')->default(1);
            $table->integer('max_users')->default(5);
            $table->boolean('has_central_reporting')->default(false);
            $table->boolean('has_branch_transfer')->default(false);
            $table->boolean('has_api_access')->default(false);
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('subscription_plan_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('subscription_plans')->cascadeOnDelete();
            $table->enum('billing_cycle', ['monthly', 'yearly', 'multi_year']);
            $table->unsignedSmallInteger('cycle_years')->default(1);
            $table->decimal('price_per_month', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->string('currency')->default('BDT');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['plan_id', 'billing_cycle', 'cycle_years'], 'sub_plan_cycle_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plan_prices');
        Schema::dropIfExists('subscription_plans');
    }
};
