<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhotoState extends Model
{
    use HasFactory;

    protected $fillable = [
        'photo_id',
        'project_id',
        'layout_id',
        'thumbnail_url',
    ];

    /**
     * Get the photo that owns the state.
     */
    public function photo(): BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }

    /**
     * Get the project that owns the state.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the layout that owns the state.
     */
    public function layout(): BelongsTo
    {
        return $this->belongsTo(Layout::class);
    }

    public function artworkPhotoStates()
    {
        return $this->hasMany(ArtworkPhotoState::class);
    }
} 