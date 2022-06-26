<?php

namespace App\Models;

use App\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

class SurfaceState extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = ['id'];

    public $casts = [
        'canvas' => SchemalessAttributes::class,
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')->singleFile();
        $this->addMediaCollection('hotspot')->singleFile();
    }

    public function scopeWithCanvas(): Builder
    {
        return $this->canvas->modelScope();
    }

    public function surface()
    {
        return $this->belongsTo(Surface::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function artworks()
    {
        return $this->belongsToMany(Artwork::class)->withPivot([
            'top_position', 'left_position', 'crop_data', 'override_scale'
        ]);
    }

    public function url(): Attribute
    {
        return Attribute::make(
            get: fn () => \Str::replace('public', 'storage', $this->hotspot_url),
        );
    }

    public function scopeCurrent(Builder $builder, $project_id = 1)
    {
        $builder->where('active', 1)->project($project_id);
    }

    public function scopeProject(Builder $builder, $project_id)
    {
        $builder->where('project_id', $project_id);
    }
}
