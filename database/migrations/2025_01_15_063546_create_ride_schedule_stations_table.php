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
        Schema::create('ride_schedule_stations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ride_schedule_id');
            $table->unsignedBigInteger('city_id');
            $table->string('point_name');
            $table->string('longitute');
            $table->string('latitude');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ride_schedule_stations');
    }
};
