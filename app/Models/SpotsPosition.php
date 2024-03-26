<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpotsPosition extends Model
{
    use HasFactory;
    protected $fillable = ['spot_id', 'tour_id', 'x', 'y', 'z'] ;
}
