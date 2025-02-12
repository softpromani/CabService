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
        Schema::create('master_fares', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_type')->nullable(); // Example: car, bike, SUV
            $table->decimal('min_km', 8, 2)->default(0);
            $table->decimal('max_km', 8, 2)->nullable();
            $table->decimal('base_fare', 10, 2)->default(0.00);
            $table->decimal('per_km_rate', 10, 2)->default(1.00);
            $table->decimal('per_minute_rate', 10, 2)->default(0); // Charge per minute
            $table->decimal('surge_multiplier', 5, 2)->default(1); // Surge Pricing Multiplier
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_fares');
    }
};
