<?php

namespace App\Models;

use App\MediaLibrary\InteractsWithMedia;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Project extends Model implements HasMedia
{
    use HasRelationships;
    use HasCompany, InteractsWithMedia;

    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();

        static::deleted(function(self $model) {
            $model->artworkCollections()->detach();
            $model->users()->detach();

            $model->surfaceStates()->cursor()->each(
                fn (SurfaceState $state) => $state->delete()
            );
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')->singleFile();
    }

    public function tours()
    {
        return $this->belongsToMany(Tour::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function contributors()
    {
        return $this->belongsToMany(User::class);
    }

    public function artworkCollections()
    {
        return $this->belongsToMany(ArtworkCollection::class);
    }

    public function surfaceStates()
    {
        return $this->hasMany(SurfaceState::class);
    }

    public function layouts()
    {
        return $this->hasMany(Layout::class);
    }

    public function artworks()
    {
        return $this->hasManyDeep(
            Artwork::class,
            ['artwork_collection_project', ArtworkCollection::class],
        )->withoutGlobalScope('forCurrentCompany');
    }

    public function scopeRelevant(Builder $builder)
    {
        $project_ids = user()->projects()->pluck('id')->toArray();
        if (user()->isEmployee()){
            return $builder->whereIn('id', $project_ids);
        }
    }

    public function addActivity($action, $data = [])
    {
        $oldName = $data['old_name'] ?? '';
        $newName = $data['new_name'] ?? '';

        $actions = [
            'name_updated' => "Project name changed to $newName from $oldName",
            'tours_updated' => 'Project tours updated',
            'collections_updated' => 'Project collections updated',
            'users_updated' => 'Project contributors updated',
        ];

        $activity = $actions[$action];

        Activity::create([
            'user_id' => auth()->id(),
            'project_id' => $this->id,
            'activity' => $activity,
        ]);
    }
}
