<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('online_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            
            // Customer Info
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            
            // Delivery Info
            $table->string('delivery_method'); // home, pickup
            $table->string('city')->nullable();
            $table->text('delivery_address')->nullable();
            
            // Financials
            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            
            // Prescription & Payment
            $table->string('prescription_path')->nullable();
            $table->string('payment_method')->default('cod'); // cod, online
            $table->string('payment_status')->default('pending'); // pending, paid
            
            // Status Tracking
            $table->string('status')->default('pending'); // pending, accepted, processing, out_for_delivery, delivered, cancelled
            
            $table->string('tracking_number')->unique();
            $table->timestamps();
        });

        Schema::create('online_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('online_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            
            $table->string('item_name');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_order_items');
        Schema::dropIfExists('online_orders');
    }
};
