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

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function isLiked()
    {
        return $this->likes->where('user_id', auth()->id())->count();
    }

    public function toggleLike()
    {
        if ($this->isLiked()) {
            $this->likes()->where('user_id', auth()->id())->delete();
        } else {
            $this->likes()->create([
                'user_id' => auth()->id(),
            ]);
        }
    }

    public function surface()
    {
        return $this->belongsTo(Surface::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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

    public function setAsActive()
    {
        $this->surface->states()
            ->where('project_id', $this->project_id)->update([
                'active' => 0,
            ]);

        $this->update([
            'active' => 1
        ]);
    }

    public function isActive()
    {
        return $this->active;
    }

    //todo::cleanup: not using anymore
    /*public function url(): Attribute
    {
        return Attribute::make(
            get: fn () => \Str::replace('public', 'storage', $this->hotspot_url),
        );
    }*/

    public function scopeCurrent(Builder $builder)
    {
        $builder->where('active', 1);
    }

    public function scopeActive(Builder $builder)
    {
        $builder->where('active', 1);
    }

    public function scopeForProject(Builder $builder, $project_id)
    {
        $builder->where('project_id', $project_id);
    }
}
