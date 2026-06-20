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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company_name')->nullable();
            $table->string('business_type')->nullable(); // Retail, Wholesale, Chain
            $table->string('status')->default('new'); // new, contacted, demo_scheduled, in_trial, converted, lost
            $table->integer('lead_score')->default(0);
            $table->text('notes')->nullable();
            $table->string('source')->nullable(); // web, referral, organic
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
