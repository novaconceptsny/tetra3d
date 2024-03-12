<?php

namespace App\Models;

use App\MediaLibrary\InteractsWithMedia;
use App\Traits\HasCompany;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    use
        HasApiTokens,
        HasFactory,
        Notifiable,
        HasCompany,
        HasRoles,
        InteractsWithMedia;

    protected $guarded = ['id'];

    protected $hidden = ['password', 'remember_token',];

    protected $casts = ['email_verified_at' => 'datetime',];

    public static function boot()
    {
        parent::boot();

        static::deleted(function(self $model) {
            $model->projects()->detach();
            $model->layouts()->delete();
            $model->surfaceStates()->delete();
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->useFallbackUrl(asset('images/defaults/no-avatar.png'))
            ->singleFile();
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    public function layouts()
    {
        return $this->hasMany(Layout::class);
    }

    public function surfaceStates()
    {
        return $this->hasMany(SurfaceState::class);
    }

    /*** ============= Attributes ============= ***/

    public function getAvatarUrlAttribute()
    {
        return $this->getFirstMediaUrl('avatar');
    }

    public function setPasswordAttribute($value)
    {
        // well, there are chances that some password really starts with
        // $2y$, but having password of length 60 is practically impossible.
        // so let's assume that it's an encrypted string!
        // let's not encrypt it!

        $isEncrypted = strlen($value) == 60 && strpos($value, '$2y$') == 0;
        $this->attributes['password'] = $isEncrypted ? $value : bcrypt($value);
    }

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn() => "{$this->first_name} {$this->last_name}"
        );
    }

    public function role(): Attribute
    {
        $role = $this->roles->first();

        return Attribute::make(
            get: fn() => $role ? $role->display_name : '-'
        );
    }

    /*** ============= Methods ============= ***/

    public function isAdmin()
    {
        $ids = [1];
        $emails = ['admin@system.com'];

        return in_array($this->id, $ids) || in_array($this->email, $emails);
    }

    public function isSuperAdmin()
    {
        return $this->isAdmin();
    }

    public function isCompanyAdmin()
    {
        return $this->hasRole('company_admin');
    }

    public function isEmployee()
    {
        return $this->hasRole('employee');
    }
}
