<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourModel extends Model
{
    use HasFactory;
    protected $fillable = ['tour_id', 'name', 'surface'] ;
}
