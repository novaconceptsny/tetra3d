<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $guarded = ['id'];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function scopeRelevant(Builder $builder)
    {
        if (user()->isCompanyAdmin()){
            $builder->where('company_id', user()->company_id);
        }
    }
}
