<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->longText('car_images')->nullable()->after('pollution_number');
            $table->string('rc_number')->nullable()->after('car_images');
            $table->string('rc_document')->nullable()->after('rc_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('car_images');
            $table->dropColumn('rc_number');
            $table->dropColumn('rc_document');
        });
    }
};
