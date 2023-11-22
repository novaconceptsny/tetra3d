<?php

namespace App\Models;

use App\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    public static function boot()
    {
        parent::boot();

        static::updated(function (self $model) {
            if ($model->isDirty('name')) {
                $model->addActivity('renamed');
            }
        });

        static::deleted(function(self $model) {
            $model->spots()->detach();

            $model->surfaceStates()->cursor()->each(
                fn (SurfaceState $state) => $state->delete()
            );
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main')->singleFile();
        $this->addMediaCollection('background')->singleFile();
        $this->addMediaCollection('shared');
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


    public function getCurrentState($layout_id)
    {
        return $this->states()->current()->forLayout($layout_id)->first();
    }

    public function createNewState($project_id)
    {
        $this->states()->where('project_id', $project_id)->update([
            'active' => 0
        ]);

        return $this->states()->create([
            'user_id' => auth()->id(),
            'project_id' => $project_id
        ]);
    }

    public function friendlyName(): Attribute
    {
        $name = $this->name;
        if (config('app.debug_tour')) {
            $name .= " (surface_{$this->id})";
        }

        return Attribute::make(
            get: fn() => $name
        );
    }

    public function spots()
    {
        return $this->belongsToMany(Spot::class);
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function getStateThumbnail($state, $tour_is_shared = false)
    {
        // if the tour is shared tour, and no state present,
        // we will show blank surface

        if ($tour_is_shared && !$state){
            return '';
        }

        if (!$state){
            return asset('images/defaults/no-artwork.png');
        }

        return $state->getFirstMediaUrl('hotspot');
    }

    public function addActivity($action)
    {
        $actions = [
            'renamed' => 'Surface renamed',
        ];

        $activity = $actions[$action];

        Activity::create([
            'user_id' => auth()->id(),
            'tour_id' => $this->tour_id,
            'activity' => $activity,
            'url' => route('surfaces.show', $this->id, false)
        ]);
    }
}
