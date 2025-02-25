<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RideSeatSegment extends Model
{
    protected $fillable = ['ride_id', 'from_station_id', 'to_station_id', 'occupied_seats'];
    public static function checkAvailableSeats($rideId, $pickupStationId, $dropoffStationId)
    {
        return self::where('ride_id', $rideId)
            ->whereBetween('station_id', [$pickupStationId, $dropoffStationId - 1])
            ->min('available_seats');
    }

    // 🎟️ Update Seat Segments
    public static function updateSeatSegments($rideId, $pickupStationId, $dropoffStationId, $seats)
    {
        self::where('ride_id', $rideId)
            ->whereBetween('station_id', [$pickupStationId, $dropoffStationId - 1])
            ->decrement('available_seats', $seats);
    }

    // ♻️ Release Seats (if payment fails)
    public static function releaseSeatSegments($rideId, $pickupStationId, $dropoffStationId, $seats)
    {
        self::where('ride_id', $rideId)
            ->whereBetween('station_id', [$pickupStationId, $dropoffStationId - 1])
            ->increment('available_seats', $seats);
    }
}
