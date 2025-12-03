<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorialVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'video_path',
        'order'
    ];

    /**
     * Get the full URL for the video
     *
     * @return string
     */
    public function getVideoUrlAttribute()
    {
        return asset($this->video_path);
    }

}
