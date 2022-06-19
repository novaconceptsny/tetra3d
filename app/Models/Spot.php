<?php

namespace App\Models;

use App\Services\SpotXmlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;
use Spatie\MediaLibrary\MediaCollections\File;


class Spot extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = ['id'];

    public static bool $organiseMediaByCollection = true;

    public $casts = [
        'xml' => SchemalessAttributes::class,
    ];

    public static function boot() {
        parent::boot();

        static::created(function(self $spot) {
            $spot->generateXml();
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image_360')->singleFile();
        $this->addMediaCollection('overlays');
    }

    public function generateXml()
    {
        $xmlGenerator = new SpotXmlGenerator($this);
        $xmlGenerator->createXml();
    }

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


    public function tourPath(): Attribute
    {
        $path = public_path("storage/tours/{$this->tour_id}/{$this->id}");
        return Attribute::make(
            get: fn() => $path
        );
    }

    public function xmlPath(): Attribute
    {
        $path = "{$this->tour_path}/pano.xml";
        return Attribute::make(
            get: fn() => $path
        );
    }

    public function getOverlayImageUrl($uuid){
        $media = $this->getFirstMedia('overlays', ['uuid' => $uuid]);

        if(!$media){
            return "";
        }

        return $media->getUrl();
    }
}
