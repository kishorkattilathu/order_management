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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->integer('total_quantity');
            $table->decimal('total_amount', 10, 2);
            $table->timestamp('order_date')->default(now());
            $table->timestamp('delivered_date')->nullable();
            $table->foreignId('order_status_id')->constrained('order_statuses')->onDelete('cascade');
            $table->boolean('is_delivered')->default(false);
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->enum('payment_method', ['cash', 'credit_card', 'debit_card', 'paypal', 'upi', 'other'])->default('cash');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
