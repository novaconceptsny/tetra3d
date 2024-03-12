<?php

namespace App\Models;

use App\Traits\HasCompany;
use App\Traits\Searchable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use Searchable, Sortable;
    use HasCompany;

    protected $guarded = ['id'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function layout()
    {
        return $this->belongsTo(Layout::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUrl()
    {
        return $this->url ? url($this->url) : null;
    }
}
