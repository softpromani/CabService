<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    //
    protected $fillable = [
        'state_name', // The state name
        'short_name',
    ];
}
