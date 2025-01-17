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
        Schema::table('states', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->after('id');

        });
        Schema::table('cities', function (Blueprint $table) {
            $table->unsignedBigInteger('state_id')->nullable()->after('id');
            $table->tinyInteger('is_active')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('state_and_city_tables', function (Blueprint $table) {
            //
        });
    }
};
