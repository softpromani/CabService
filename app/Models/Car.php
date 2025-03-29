<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $guarded = [];
    protected $cats    = ['car_images' => 'array'];
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
    public function rideSchedules()
    {
        return $this->hasMany(RideSchedule::class, 'car_id');
    }
    public function model()
    {
        return $this->belongsTo(CarModel::class, 'model_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
