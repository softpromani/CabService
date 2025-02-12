<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class supportTicket extends Model
{
    protected $guarded = [];

    public function conversations()
    {
        return $this->hasMany(supportTicketConv::class);
    }
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
