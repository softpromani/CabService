<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory, SoftDeletes;
    // app logo,splash_screen,primary_color,secondary_color,text_color,google_map_api,
    //web logo
    protected $guarded=[];
}
