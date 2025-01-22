<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RideSchedule extends Model
{
    protected $guarded = ["id"];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    // Relationship with Car
    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }
}
