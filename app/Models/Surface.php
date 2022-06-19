<?php

namespace App\Models;

use App\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\HasMedia;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

class Surface extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = ['id'];

    public static bool $organiseMediaByCollection = true;

    public $casts = [
        'data' => SchemalessAttributes::class,
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main')->singleFile();
        $this->addMediaCollection('shared')->singleFile();
    }

    public function scopeWithData(): Builder
    {
        return $this->data->modelScope();
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
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

    public function uploadImages(Request $request)
    {
        $this->addFromMediaLibraryRequest($request->main)
            ->toMediaCollection('main');

        $this->addFromMediaLibraryRequest($request->shared)
            ->toMediaCollection('shared');
    }
}
