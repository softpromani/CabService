<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE rides MODIFY COLUMN status ENUM('schedule', 'started', 'cancel', 'completed', 'pending')");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE rides MODIFY COLUMN status ENUM('schedule', 'start', 'cancel')");
    }
};
