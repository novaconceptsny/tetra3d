<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurfaceInfo extends Model
{
    use HasFactory;

    // Define the fillable fields
    protected $fillable = ['surface_id', 'normalvector', 'start_pos', 'width', 'height'];
    
}
