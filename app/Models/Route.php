<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $guarded = [];

    public function stations(){
        return $this->hasMany(RouteStation::class,'route_id');
    }
}
