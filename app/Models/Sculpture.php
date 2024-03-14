<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sculpture extends Model
{
    use HasFactory;

    protected $fillable = ['layout_id', 'sculpture_id', 'model_id', 'position_x', 'position_y', 'position_z', 'rotation_x', 'rotation_y', 'rotation_z'] ;
}
