<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code')->unique(); // e.g. YMN-2026-000123
            $table->foreignId('store_id')->constrained();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('sales_user_id')->nullable()->constrained('users');
            $table->foreignId('driver_user_id')->nullable()->constrained('users');
            $table->foreignId('currency_id')->constrained();
            $table->decimal('exchange_rate_locked', 15, 6);

            $table->string('order_status'); // NEW, CONFIRMED, PROCESSING, READY, WITH_DRIVER, DELIVERED, RETURNED, CANCELLED, RESERVED
            $table->string('delivery_status')->nullable();
            $table->string('payment_status'); // UNPAID, PARTIAL, PAID
            $table->string('accounting_status'); // PENDING, COLLECTED, POSTED

            $table->foreignId('delivery_area_id')->nullable()->constrained();
            $table->foreignId('delivery_type_id')->nullable()->constrained();
            $table->decimal('delivery_fee', 15, 2)->default(0);

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);

            $table->decimal('deposit_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2)->default(0);

            $table->timestamp('reservation_expires_at')->nullable();
            $table->timestamp('scheduled_delivery_at')->nullable();

            // Lifecycle timestamps
            $table->timestamp('prepared_at')->nullable();
            $table->timestamp('driver_assigned_at')->nullable();
            $table->timestamp('handed_to_driver_at')->nullable();
            $table->timestamp('out_for_delivery_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamp('collected_at')->nullable(); // Accountant collected

            $table->text('notes')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->string('return_reason')->nullable();
            $table->foreignId('linked_return_order_id')->nullable()->constrained('orders');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
