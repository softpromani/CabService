<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RideSeatSegment extends Model
{
    protected $guarded = [];
    public static function checkAvailableSeats($rideId, $pickupStationId, $dropoffStationId)
    {
        // Get total seats in the ride
        $totalSeats = Ride::where('id', $rideId)->value('available_seats');

        // Get max occupied seats in the requested segment range
        $maxOccupied = self::where('ride_id', $rideId)
            ->where('from_station_id', '>=', $pickupStationId)
            ->where('to_station_id', '<=', $dropoffStationId)
            ->max('occupied_seats');

        return max(0, $totalSeats - ($maxOccupied ?? 0)); // Available seats = total - max occupied
    }

    // 🎟️ Reserve Seats for Booking
    public static function updateSeatSegments($rideId, $pickupStationId, $dropoffStationId, $seats)
    {
        self::where('ride_id', $rideId)
            ->where('from_station_id', '>=', $pickupStationId)
            ->where('to_station_id', '<=', $dropoffStationId)
            ->increment('occupied_seats', $seats);
    }

    // ♻️ Release Seats (if payment fails)
    public static function releaseSeatSegments($rideId, $pickupStationId, $dropoffStationId, $seats)
    {
        self::where('ride_id', $rideId)
            ->where('from_station_id', '>=', $pickupStationId)
            ->where('to_station_id', '<=', $dropoffStationId)
            ->decrement('occupied_seats', $seats);
    }
}
