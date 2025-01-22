<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    //
    protected $fillable = [
        'brand_id',
        'model_name',
        'seats',
        'is_active',
    ];
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
