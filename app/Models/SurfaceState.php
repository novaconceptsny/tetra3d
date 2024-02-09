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

    public static function boot() {
        parent::boot();

        static::deleted(function(self $model) {
            $model->artworks()->detach();
            $model->comments()->delete();
            $model->likes()->delete();
            $model->addActivity('deleted');
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')
            ->useFallbackUrl($this->surface ? $this->surface->getFirstMediaUrl('background') : '')
            ->singleFile();
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
        return $this->belongsTo(User::class)->withDefault([
            'first_name' => 'Guest'
        ]);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function layout()
    {
        return $this->belongsTo(Layout::class);
    }

    public function artworks()
    {
        return $this->belongsToMany(Artwork::class)->withPivot([
            'top_position', 'left_position', 'crop_data', 'override_scale'
        ]);
    }

    public function setAsActive()
    {
        if ($this->isActive()) {
            return false;
        }

        $this->surface->states()
            ->whereNot('id', $this->id)
            ->where('project_id', $this->project_id)
            ->where('layout_id', $this->layout_id)
            ->update([
                'active' => 0,
            ]);

        $this->update([
            'active' => 1
        ]);

        $this->addActivity('switched_state');
    }

    public function isActive()
    {
        return $this->active;
    }

    public function remove(): void
    {
        $this->delete();

        if ($this->isActive()){
            $stateToActive = SurfaceState::query()
                ->where('surface_id', $this->surface_id)
                ->where('layout_id', $this->layout_id)
                ->first();
            $stateToActive?->setAsActive();
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

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

    public function scopeForLayout(Builder $builder, $layout_id)
    {
        $builder->where('layout_id', $layout_id);
    }

    public function addActivity($action)
    {
        $actions = [
            'created' => 'added',
            'updated' => 'edited',
            'deleted' => 'deleted',
            'new_comment' => 'comment added',
            'switched_state' => 'selected to display in tour',
        ];

        $activity = "Surface {$this->surface->name} version '$this->name' ". $actions[$action];

        $urls = [
            'created' => route('tours.surfaces', ['tour' => $this->surface?->tour_id, 'layout_id' => $this->layout_id], false),
            'updated' => route('surfaces.show', ['surface' => $this->surface_id,'layout_id' => $this->layout_id], false),
            'new_comment' => route('surfaces.show', ['surface' => $this->surface_id, 'layout_id' => $this->layout_id, 'sidebar' => 'comments'], false),
            'switched_state' => route('tours.show', ['tour' => $this->surface?->tour_id, 'layout_id' => $this->layout_id], false),
        ];

        Activity::create([
            'user_id' => auth()->id(),
            'project_id' => $this->layout?->project_id,
            'layout_id' => $this->layout_id,
            'tour_id' => $this->surface?->tour_id,
            'activity' => $activity,
            'url' => $urls[$action] ?? null
        ]);
    }
}
