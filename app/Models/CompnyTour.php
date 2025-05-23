<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompnyTour extends Model
{
    use HasFactory;

    // If your table name is not the plural of the model name, specify it:
    protected $table = 'compny_tour';

    // Add fillable fields
    protected $fillable = [
        'company_id',
        'tour_id',
    ];

    public function company()
    {
        return $this->belongsToMany(Company::class);
    }

    public function tour()
    {
        return $this->belongsToMany(Tour::class);
    }
}
