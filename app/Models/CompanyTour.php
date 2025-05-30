<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyTour extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'tour_id'];
    protected $table = 'company_tour';
    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function companies()   
    {
        return $this->belongsTo(Company::class);
    }
}
