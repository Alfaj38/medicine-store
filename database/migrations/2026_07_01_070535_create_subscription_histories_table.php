<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('old_plan_id')->nullable()->constrained('subscription_plans')->nullOnDelete();
            $table->foreignId('new_plan_id')->constrained('subscription_plans')->cascadeOnDelete();
            
            $table->enum('type', ['renew', 'upgrade', 'downgrade']);
            
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('credit_used', 10, 2)->default(0);
            $table->decimal('wallet_used', 10, 2)->default(0);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_histories');
    }
};
