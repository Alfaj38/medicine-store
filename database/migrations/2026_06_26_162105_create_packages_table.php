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
        // 1. Create Packages table
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->decimal('monthly_price', 10, 2)->default(0);
            $table->decimal('yearly_price', 10, 2)->default(0);
            $table->integer('trial_days')->default(15);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 2. Create Package Features table
        Schema::create('package_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->string('feature_code');
            $table->boolean('is_enabled')->default(true);
            $table->integer('limit')->nullable(); // null means unlimited
            $table->timestamps();
            
            $table->unique(['package_id', 'feature_code']);
        });

        // 3. Update Subscriptions table
        Schema::table('subscriptions', function (Blueprint $table) {
            // Add new column
            $table->foreignId('package_id')->nullable()->after('company_id')->constrained('packages');
            $table->integer('grace_period_days')->default(5)->after('trial_ends_at');
            $table->string('payment_status')->nullable()->after('status');
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->string('transaction_id')->nullable()->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropColumn(['package_id', 'grace_period_days', 'payment_status', 'payment_method', 'transaction_id']);
        });

        Schema::dropIfExists('package_features');
        Schema::dropIfExists('packages');
    }
};
