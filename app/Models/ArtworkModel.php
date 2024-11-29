<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtworkModel extends Model
{
    use HasFactory;
    protected $fillable = ['layout_id', 'artwork_id', 'position_x', 'position_y', 'position_z', 'rotation_x', 'rotation_y', 'rotation_z'] ;
}
