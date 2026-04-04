<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->constrained(); // Method
            $table->foreignId('currency_id')->constrained();
            $table->decimal('amount', 15, 2);
            $table->decimal('exchange_rate_locked', 15, 6);
            $table->string('type'); // payment, refund, deposit
            $table->string('status'); // PENDING, CONFIRMED, REJECTED
            $table->foreignId('collected_by_user_id')->nullable()->constrained('users');
            $table->foreignId('confirmed_by_user_id')->nullable()->constrained('users');
            $table->string('receipt_file_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
