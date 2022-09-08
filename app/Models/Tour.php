<?php

namespace App\Models;

use App\MediaLibrary\InteractsWithMedia;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

class Tour extends Model implements HasMedia
{
    use HasCompany, InteractsWithMedia;

    protected $guarded = ['id'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')->singleFile();
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
}
