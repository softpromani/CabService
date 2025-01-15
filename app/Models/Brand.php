<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    //
    protected $fillable = [
        'brand_name',
        'brand_logo',
    ];
    public function carModels(): HasMany
    {
        return $this->hasMany(CarModel::class, 'brand_id');
    }
}
