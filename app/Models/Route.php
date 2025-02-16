<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
    public function stations()
    {
        return $this->hasMany(RouteStation::class, 'route_id');
    }
}
