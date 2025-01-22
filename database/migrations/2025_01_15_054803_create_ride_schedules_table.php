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
        Schema::create('ride_schedules', function (Blueprint $table) {
            $table->id();
            $table->biginteger('driver_id')->comment('id from user table')->unsigned();
            $table->biginteger('car_id')->comment('')->unsigned();
            $table->dateTime('start_at')->comment('ride start time and date ');
            $table->dateTime('end_at')->comment('ride end time and date ');
            $table->bigInteger('travel_time')->default(0);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ride_schedules');
    }
};
