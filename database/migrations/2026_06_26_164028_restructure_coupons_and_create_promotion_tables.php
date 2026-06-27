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
        // Make migration idempotent in case of partial failure
        Schema::dropIfExists('coupon_usages');
        Schema::dropIfExists('coupon_business_types');
        Schema::dropIfExists('coupon_packages');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('campaigns');

        // 1. Create campaigns table
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->string('status')->default('active'); // active, scheduled, expired, paused
            $table->timestamps();
        });

        // 2. Create coupons table
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_id')->nullable()->index();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('subscription_type')->default('all'); 
            $table->string('discount_type')->default('percentage'); 
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->decimal('max_discount_amount', 10, 2)->nullable();
            $table->decimal('minimum_purchase', 10, 2)->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('expire_date')->nullable();
            
            $table->integer('max_uses_total')->nullable();
            $table->integer('max_uses_per_company')->nullable();
            $table->integer('total_uses')->default(0);
            
            $table->boolean('is_auto_apply')->default(false);
            $table->boolean('is_hidden')->default(false);
            $table->string('status')->default('active');
            
            $table->timestamps();
        });

        // 3. coupon_packages (Many-to-Many)
        Schema::create('coupon_packages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coupon_id')->index();
            $table->unsignedBigInteger('package_id')->index();
            $table->timestamps();
        });

        // 4. coupon_business_types
        Schema::create('coupon_business_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coupon_id')->index();
            $table->string('business_type');
            $table->timestamps();
        });

        // 5. coupon_usages
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coupon_id')->index();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('invoice_id')->nullable()->index();
            $table->unsignedBigInteger('subscription_id')->nullable()->index();
            $table->decimal('discount_amount_applied', 10, 2)->default(0);
            $table->timestamp('used_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
        Schema::dropIfExists('coupon_business_types');
        Schema::dropIfExists('coupon_packages');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('campaigns');
    }
};
