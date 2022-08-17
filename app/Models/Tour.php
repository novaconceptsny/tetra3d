<?php

namespace App\Models;

use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use HasCompany;

    protected $guarded = ['id'];

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

    public function scopeRelevant(Builder $builder)
    {
        if (user()->isCompanyAdmin()){
            $builder->where('company_id', user()->company_id);
        }
    }
}
