<?php

namespace App\Models;

use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Model;

class ArtworkCollection extends Model
{
    use HasCompany;

    protected $guarded = ['id'];

    public static function boot() {
        parent::boot();

        static::created(function(self $model) {
            $model->addActivity('created');
        });

        static::updated(function(self $model) {
            $model->addActivity('updated');
        });

        static::deleted(function(self $model) {
            $model->addActivity('deleted');
        });
    }

    public function artworks()
    {
        return $this->hasMany(Artwork::class);
    }

    public function sculptureModels()
    {
        return $this->hasMany(SculptureModel::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    public function addActivity($action)
    {
        $actions = [
            'created' => 'added',
            'updated' => 'edited',
            'deleted' => 'deleted',
        ];

        $activity = "Artwork collection '$this->name' ".$actions[$action];

        $urls = [
            'created' => route('artworks.index', ['collection_id' => $this->id], false),
            'updated' => route('artworks.index', ['collection_id' => $this->id], false),
        ];

        Activity::create([
            'user_id' => auth()->id(),
            'activity' => $activity,
            'url' => $urls[$action] ?? null
        ]);
    }
}
