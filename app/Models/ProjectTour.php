<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTour extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'tour_id'];
    protected $table = 'project_tour';  

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tour()
    {   
        return $this->belongsTo(Tour::class);
    }
}
