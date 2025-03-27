<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = ['user_id', 'ride_id', 'pickup_station_id', 'dropoff_station_id', 'total_distance', 'fare_amount', 'seats', 'status'];
    protected $with     = ['passangers'];
    public function passengers()
    {
        return $this->hasMany(Passenger::class);
    }
}
