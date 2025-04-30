<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'surface_id',
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
     * Get the surface that owns the photo.
     */
    public function surface(): BelongsTo
    {
        return $this->belongsTo(Surface::class);
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

    /**
     * Get the states for the photo.
     */
    public function states()
    {
        return $this->hasMany(PhotoState::class);
    }
}
