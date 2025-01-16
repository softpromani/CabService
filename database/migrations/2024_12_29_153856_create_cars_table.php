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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id')->comment('id from user table');
            $table->unsignedBigInteger('model_id');
            $table->unsignedBigInteger('brand_id');
            $table->string('color')->nullable();
            $table->string('interior');
            $table->string('seat');
            $table->string('registration_number')->unique();
            $table->string('insurance_number');
            $table->string('pollution_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
