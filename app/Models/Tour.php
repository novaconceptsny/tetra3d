<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $guarded = ['id'];

    public function spots()
    {
        return $this->hasMany(Spot::class);
    }

    public function surfaces()
    {
        return $this->hasMany(Surface::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
