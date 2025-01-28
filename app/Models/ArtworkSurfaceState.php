<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtworkSurfaceState extends Model
{
    use HasFactory;

    protected $table = "artwork_surface_state";
    protected $fillable = ['artwork_id', 'surface_state_id', 'top_position', 'left_position', 'crop_data', 'override_scale'] ;
}
