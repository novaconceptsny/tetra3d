<?php

namespace App\Models;

use App\Traits\HasCompany;
use Gate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasCompany;

    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();

        /*if (auth()->check()){
            Gate::allowIf(fn ($user) => $user->isAdmin());
        }*/
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function contributors()
    {
        return $this->belongsToMany(User::class);
    }

    public function scopeRelevant(Builder $builder)
    {
        $project_ids = user()->projects()->pluck('id')->toArray();
        if (user()->isEmployee()){
            return $builder->whereIn('id', $project_ids);
        }
    }
}
