<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteStation extends Model
{
    protected $guarded=[];
    public function city(){
        return $this->belongsTo(City::class);
    }
    public function route(){
        return $this->belongsTo(Route::class);
    }
}
