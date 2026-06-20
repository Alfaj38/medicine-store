<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demo_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('company_name');
            $table->string('branch_count')->default('1');
            $table->json('current_software')->nullable();   // ["Excel","Manual",...]
            $table->json('pain_points')->nullable();        // ["Stock Expiry","Slow Billing",...]
            $table->string('preferred_language')->default('English');
            $table->date('preferred_date');
            $table->string('preferred_time');
            $table->text('questions')->nullable();
            $table->string('referral_source')->nullable();  // Google, Facebook, etc.
            $table->integer('lead_score')->default(0);
            $table->enum('status', ['pending', 'confirmed', 'completed', 'no_show'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_requests');
    }
};
