<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;
use Vinkla\Hashids\Facades\Hashids;

class SharedTour extends Model
{
    protected $guarded = ['id'];

    public $casts = [
        'surface_states' => SchemalessAttributes::class,
    ];

    public function scopeWithSurfaceStates(): Builder
    {
        return $this->surface_states->modelScope();
    }

    protected function id(): Attribute
    {
        return  Attribute::make(
            get: fn ($value) => Hashids::encode($value)
        );
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->findOrFail(Hashids::decode($value)[0] ?? '');
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
