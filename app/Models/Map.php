<?php

namespace App\Models;

use App\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

class Map extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = ['id'];

    public static function boot() {
        parent::boot();

        static::deleted(function(self $model) {
            $model->spots()->detach();
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function spots()
    {
        return $this->belongsToMany(Spot::class)->withPivot([
            'x', 'y'
        ]);
    }
}
