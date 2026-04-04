<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Normal, Urgent, Scheduled
            $table->string('color_hex')->nullable();
            $table->boolean('is_urgent')->default(false); // Affects sorting
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_types');
    }
};
