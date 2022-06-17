<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class SurfaceState extends Model
{
    protected $guarded = ['id'];

    public function surface()
    {
        return $this->belongsTo(Surface::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function url(): Attribute
    {
        return Attribute::make(
            get: fn () => \Str::replace('public', 'storage', $this->hotspot_url),
        );
    }
}
