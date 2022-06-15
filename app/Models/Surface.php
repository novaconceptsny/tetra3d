<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

class Surface extends Model
{
    protected $guarded = ['id'];

    public $casts = [
        'data' => SchemalessAttributes::class,
    ];

    public function scopeWithData(): Builder
    {
        return $this->data->modelScope();
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function states()
    {
        return $this->hasMany(SurfaceState::class);
    }

    public function state()
    {
        return $this->hasOne(SurfaceState::class)
            ->where('active', 1);
    }

    public function spots()
    {
        return $this->belongsToMany(Spot::class);
    }
}
