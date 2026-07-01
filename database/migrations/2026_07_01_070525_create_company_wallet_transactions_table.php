<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->decimal('credit', 12, 2)->default(0);
            $table->decimal('debit', 12, 2)->default(0);
            $table->decimal('balance_after', 12, 2)->default(0);
            $table->enum('type', ['renewal', 'upgrade', 'downgrade', 'refund', 'manual_credit', 'overpayment', 'sms_purchase']);
            $table->string('reference')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_wallet_transactions');
    }
};
