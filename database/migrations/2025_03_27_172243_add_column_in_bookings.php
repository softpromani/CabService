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
        Schema::table('bookings', function (Blueprint $table) {
            // First, add the column without unique constraint
            $table->string('booking_number')->nullable()->after('id');
            $table->text('comment')->nullable()->after('status');
        });

        // Fill existing records with unique booking numbers
        foreach (\App\Models\Booking::all() as $booking) {
            $booking->update(['booking_number' => 'BOOK' . $booking->id]);
        }

        // Now add the unique constraint
        Schema::table('bookings', function (Blueprint $table) {
            $table->unique('booking_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('booking_number');
            $table->dropColumn('comment');
        });
    }
};
