<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SculptureModel extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'artist', 'sculpture_url', 'image_url', 'data'] ;
}
