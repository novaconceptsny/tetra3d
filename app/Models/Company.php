<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $guarded = ['id'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function admins()
    {
        return $this->hasMany(User::class)->role('company_admin');
    }

    public function admin()
    {
        return $this->hasOne(User::class)->role('company_admin');
    }


    public function hasCollector()
    {
        return (bool) $this->collector_subscription_id;
    }
}
