<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ride extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function ride_stations()
    {
        return $this->belongsToMany(RouteStation::class, 'ride_stations', 'ride_id', 'station_id')
            ->withPivot('arrival', 'departure') // Access pivot columns
            ->withTimestamps();
    }
    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }
}
