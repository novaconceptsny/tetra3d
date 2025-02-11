<?php

namespace App\Models;

use App\MediaLibrary\InteractsWithMedia;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\HasMedia;

class Tour extends Model implements HasMedia
{
    use HasCompany, InteractsWithMedia;

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'company_id',
        'created_at',
        'updated_at',
        'has_model'
    ];


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')->singleFile();
    }

    public static function boot()
    {
        parent::boot();

        static::deleted(function(self $model) {
            $model->projects()->detach();

            $model->surfaces()->cursor()->each(
                fn (Surface $surface) => $surface->delete()
            );

            $model->spots()->cursor()->each(fn (Spot $spot) => $spot->delete());
            $model->maps()->cursor()->each(fn (Map $map) => $map->delete());

            $tour_dir = public_path("storage/tours/$model->id");
            if (\File::isDirectory($tour_dir)){
                \File::deleteDirectory("$tour_dir");
            }
        });
    }

    public function spots()
    {
        return $this->hasMany(Spot::class);
    }

    public function surfaces()
    {
        return $this->hasMany(Surface::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    public function map()
    {
        return $this->hasOne(Map::class);
    }

    public function maps()
    {
        return $this->hasMany(Map::class);
    }

    public function scopeRelevant(Builder $builder)
    {
        if (user()->isCompanyAdmin()){
            $builder->where('company_id', user()->company_id);
        }
    }

    public function reflectCompanyChanges()
    {
        $this->spots()->update(['company_id' => $this->company_id]);
        $this->surfaces()->update(['company_id' => $this->company_id]);

        SurfaceState::whereIn(
            'surface_id', $this->surfaces()->pluck('id')->toArray()
        )->cursor()->each(
            fn (SurfaceState $state) => $state->delete()
        );
    }
}
