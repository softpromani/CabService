<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RouteStation extends Model
{ 
    use SoftDeletes;

    protected $guarded=[];
    public function city(){
        return $this->belongsTo(City::class);
    }
    public function route(){
        return $this->belongsTo(Route::class);
    }
}
