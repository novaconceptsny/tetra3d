<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'background_url',
        'data'
    ];

    protected $casts = [
        'data' => 'array'
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
        // Check if the background_url is a base64 string
        if (strpos($this->background_url, 'data:image') === 0) {
            return $this->background_url;
        }
        
        // Fallback for regular URLs/paths
        return asset('storage/' . $this->background_url);
    }
}
