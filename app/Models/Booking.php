<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = ['booking_number', 'user_id', 'ride_id', 'pickup_station_id', 'dropoff_station_id', 'total_distance', 'fare_amount', 'seats', 'status', 'comment'];
    protected $with     = ['passengers', 'station_origin', 'station_destination'];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            $yearCode  = Carbon::now()->format('y');             // Last two digits of the year (e.g., 24 for 2024)
            $monthCode = strtoupper(Carbon::now()->format('M')); // 3-letter month abbreviation (e.g., "MAR")

            // Generate base prefix for booking number
            $prefix = "BOOK{$yearCode}{$monthCode}";

            // Count bookings for the same year and month based on created_at
            $count = self::whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->count() + 1;

            // Generate the unique booking number with 7-digit padding
            $booking->booking_number = "{$prefix}" . str_pad($count, 7, '0', STR_PAD_LEFT);
        });
    }
    public function passengers()
    {
        return $this->hasMany(Passenger::class);
    }
    public function book_by_user()
    {
        return $this->belongsTo(User::class);
    }
    public function station_origin()
    {
        return $this->hasOneThrough(
            RouteStation::class, // Final destination model
            RideStations::class, // Intermediate model
            'id',                // Foreign key on RideStation (local key in Booking)
            'id',                // Foreign key on RouteStation
            'pickup_station_id', // Local key in Booking
            'station_id'         // Foreign key in RideStation
        );
    }
    public function station_destination()
    {
        return $this->hasOneThrough(
            RouteStation::class,  // Final destination model
            RideStations::class,  // Intermediate model
            'id',                 // Foreign key on RideStation (local key in Booking)
            'id',                 // Foreign key on RouteStation
            'dropoff_station_id', // Local key in Booking
            'station_id'          // Foreign key in RideStation
        );
    }
}
