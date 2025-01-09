<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name', // The country name
        'code', // The country code
        'sname',
    ];
}
