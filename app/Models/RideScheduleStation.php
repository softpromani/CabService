<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RideScheduleStation extends Model
{
    protected $guarded = [];
    public function rideSchedule()
    {
        return $this->belongsTo(RideSchedule::class, 'ride_schedule_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
