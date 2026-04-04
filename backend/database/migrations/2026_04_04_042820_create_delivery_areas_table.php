<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_areas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 15, 2)->default(0);
            $table->boolean('requires_shipping_company')->default(false); // If true, requires awb/shipping bill
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_areas');
    }
};
