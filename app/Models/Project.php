<?php

namespace App\Models;

use App\MediaLibrary\InteractsWithMedia;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Project extends Model implements HasMedia
{
    use HasRelationships;
    use HasCompany, InteractsWithMedia;

    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();
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
}
