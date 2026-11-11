<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $guarded = ['id'];
    
    protected $fillable = ['name', 'collector_subscription_id', 'collector_account_guid'];

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

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Collector Systems API 2.0 needs the account GUID (the API key is global, see config services.collector).
     */
    public function hasCollector()
    {
        return (bool) $this->collector_account_guid;
    }
}
