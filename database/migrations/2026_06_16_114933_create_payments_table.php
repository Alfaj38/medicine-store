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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->cascadeOnDelete();
            
            $table->string('type'); // customer_receipt, supplier_payment, expense, income
            
            $table->string('reference_type')->nullable(); // e.g. App\Models\Customer
            $table->unsignedBigInteger('reference_id')->nullable();
            
            $table->date('payment_date');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method')->default('cash'); // cash, card, bank, etc.
            $table->string('transaction_id')->nullable();
            
            $table->text('notes')->nullable();
            
            $table->foreignId('user_id')->constrained('users'); // Who recorded the payment
            
            $table->timestamps();
            
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
