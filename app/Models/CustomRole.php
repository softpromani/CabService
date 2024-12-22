<?php

namespace App\Models;

use Spatie\Permission\Models\Role;

class CustomRole extends Role
{
    public function media()
    {
        return $this->morphOne(Media::class, 'mediaable');
    }
}
