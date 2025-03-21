<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RouteStation extends Model
{ 

    protected $guarded = [];
    protected $with    = ['city', 'route'];
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
