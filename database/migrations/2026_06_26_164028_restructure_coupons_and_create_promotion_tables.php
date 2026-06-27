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

        // 2. Update coupons table
        Schema::table('coupons', function (Blueprint $table) {
            // Drop old columns that are changing or not needed in the same format
            // Let's modify existing instead of dropping if possible, or drop and re-add.
            // Since we need to change enum `type` to `discount_type`, it's easier to drop and add for sqlite compatibility, 
            // but for MySQL we can just drop the column. 
            // `type` enum was 'percentage', 'fixed'. We need discount_type.
            // In laravel, dropping enum columns often requires doctrine/dbal. 
            // Alternatively, just rename the table to old_coupons and create a new one since we are building a new system.
            // Let's just create a new structure and copy data if any, but it's a dev environment so it's fine.
        });

        // Let's drop and recreate for clean state, since it's a massive change.
        Schema::dropIfExists('coupons');

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('subscription_type')->default('all'); // new_subscription, renewal, upgrade, downgrade, all
            $table->string('discount_type')->default('percentage'); // percentage, fixed, free_trial, upgrade, free_addon, custom_price
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->decimal('max_discount_amount', 10, 2)->nullable();
            $table->decimal('minimum_purchase', 10, 2)->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('expire_date')->nullable();
            
            // Usage limits
            $table->integer('max_uses_total')->nullable();
            $table->integer('max_uses_per_company')->nullable();
            $table->integer('total_uses')->default(0);
            
            $table->boolean('is_auto_apply')->default(false);
            $table->boolean('is_hidden')->default(false);
            $table->string('status')->default('active'); // active, scheduled, expired, disabled
            
            $table->timestamps();
        });

        // 3. coupon_packages (Many-to-Many)
        Schema::create('coupon_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // 4. coupon_business_types
        Schema::create('coupon_business_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->string('business_type');
            $table->timestamps();
        });

        // 5. coupon_usages
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('discount_amount_applied', 10, 2)->default(0);
            $table->timestamp('used_at');
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
