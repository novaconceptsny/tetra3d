<?php

namespace App\Models;

use App\Traits\HasCompany;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasCompany;

    protected $fillable = ['name', 'email', 'password',];

    protected $hidden = ['password', 'remember_token',];

    protected $casts = ['email_verified_at' => 'datetime',];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /*** ============= Attributes ============= ***/

    public function getAvatarUrlAttribute()
    {
        /*$path = asset('images/defaults/avatar.jpg');*/
        $path = asset('images/tetra__logo.png');
        if ($this->avatar && file_exists($this->avatar)) {
            $path = asset($this->avatar);
        }

        return $path;
    }

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn() => "{$this->first_name} {$this->last_name}"
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
}
