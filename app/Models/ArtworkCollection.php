<?php

namespace App\Models;

use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Model;

class ArtworkCollection extends Model
{
    use HasCompany;

    protected $guarded = ['id'];

    public function artworks()
    {
        return $this->hasMany(Artwork::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }
}
