<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'layout_id',
        'name',
        'background_url',
        'data'
    ];

    protected $casts = [
        'data' => 'object'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the URL for the photo
     * 
     * @return string
     */
    public function getUrl()
    {
        return asset($this->background_url);
    }
}
