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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('subscription_plans');
            $table->foreignId('plan_price_id')->constrained('subscription_plan_prices');
        
            $table->enum('billing_cycle', ['monthly', 'yearly', 'multi_year']);
            $table->unsignedSmallInteger('cycle_years')->default(1);
        
            $table->decimal('amount_paid', 10, 2);
            $table->decimal('discount_applied', 10, 2)->default(0);
            $table->string('currency')->default('BDT');
        
            $table->timestamp('starts_at');
            $table->timestamp('expires_at');
            $table->timestamp('trial_ends_at')->nullable();
        
            $table->enum('status', [
                'trial', 'active', 'past_due', 'cancelled', 'expired', 'suspended'
            ])->default('trial');
        
            $table->boolean('auto_renew')->default(true);
            $table->timestamp('next_renewal_at')->nullable();
        
            $table->string('payment_gateway')->nullable();
            $table->string('gateway_subscription_id')->nullable();
        
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
        
            $table->timestamp('period_starts_at');
            $table->timestamp('period_ends_at')->nullable();
        
            $table->string('gateway');
            $table->string('transaction_id')->unique()->nullable();
            $table->string('gateway_ref')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2);
            $table->string('currency')->default('BDT');
        
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');
            $table->enum('type', ['new', 'renewal', 'upgrade', 'downgrade', 'manual']);
            $table->json('gateway_response')->nullable();
        
            $table->string('invoice_no')->unique()->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
        Schema::dropIfExists('subscriptions');
    }
};
