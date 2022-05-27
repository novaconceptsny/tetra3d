<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spot extends Model
{
    protected $guarded = ['id'];

    public function surfaces()
    {
        return $this->hasMany(Surface::class);
    }
}
