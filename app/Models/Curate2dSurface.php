<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curate2dSurface extends Model
{
    use HasFactory;

    protected $table = 'curate2d_surfaces';

    protected $guarded = [];
    // Or specify $fillable as needed

    protected $fillable = [
        'project_id',
        'name',
        'display_name',
        'data'
    ];

    protected $casts = [
        'data' => 'object'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
