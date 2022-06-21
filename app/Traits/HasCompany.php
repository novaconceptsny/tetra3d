<?php

namespace App\Traits;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;


trait HasCompany
{
    public function company()
    {
        return $this->belongsTo(Company::class)->withDefault([
            'name' => '-'
        ]);
    }

    protected static function booted()
    {
        if (auth()->check() && $company_id = auth()->user()->company_id) {
            if (self::class != User::class && ! auth()->user()->isSuperAdmin()) {
                static::addGlobalScope('forCurrentCompany', function (Builder $builder) use ($company_id) {
                    $builder->where('company_id', $company_id);
                });
            }

            self::created(function ($model) use ($company_id) {
                $model->company_id = $company_id;
                $model->save();
            });
        }
    }

    public function scopeForCurrentCompany(Builder $builder, $guard = null)
    {
        if (auth()->user()->isSuperAdmin()){
            return $builder;
        }

        if (auth($guard)->check() && $company_id = auth($guard)->user()->company_id) {
            $builder->where('company_id', $company_id);
        }
    }
}
