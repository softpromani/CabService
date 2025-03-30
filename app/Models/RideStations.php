<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RideStations extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function station()
    {
        return $this->belongsTo(RouteStation::class, 'id');
    }
}
