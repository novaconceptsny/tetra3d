<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

class Spot extends Model
{
    protected $guarded = ['id'];

    public $casts = [
        'xml' => SchemalessAttributes::class,
    ];

    public function scopeWithXml(): Builder
    {
        return $this->xml->modelScope();
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function surfaces()
    {
        return $this->belongsToMany(Surface::class);
    }
}
