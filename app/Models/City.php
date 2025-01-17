<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    //
    protected $fillable = [
        'city_name', // The state name
        'pin_code',
        'state_id',
    ];
    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
