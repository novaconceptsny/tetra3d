<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'background_url',
        'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
