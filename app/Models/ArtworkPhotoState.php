<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtworkPhotoState extends Model
{
    use HasFactory;

    protected $fillable = [
        'artwork_id',
        'photo_state_id',
        'curate2d_surface_id',
        'layout_id',
        'pos',
        'scale'
    ];

    protected $casts = [
        'pos' => 'array'
    ];

    public function artwork()
    {
        return $this->belongsTo(Artwork::class);
    }

    public function photoState()
    {
        return $this->belongsTo(PhotoState::class);
    }

    public function layout()
    {
        return $this->belongsTo(Layout::class);
    }

}
