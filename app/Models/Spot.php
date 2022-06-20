<?php

namespace App\Models;

use App\Enums\Spot\PanoStatus;
use App\Enums\Spot\XmlStatus;
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

    public function xmlStatus()
    {
        if (! file_exists($this->xml_path)) {
            return XmlStatus::NOT_PRESENT;
        }

        return XmlStatus::PRESENT;
    }

    public function panoStatus()
    {
        // todo::improvement: might have performance issues!

        $base_dir = $this->tour_path;
        $pano_dir = "$base_dir/panos";

        if ( ! \File::isDirectory($pano_dir)) {
            return PanoStatus::NOT_PRESENT;
        }

        if (count(\File::allFiles($pano_dir)) !== 415) {
            return PanoStatus::INVALID;
        }

        return PanoStatus::PRESENT;
    }

    public function getOverlayImageUrl($uuid){
        $media = $this->getFirstMedia('overlays', ['uuid' => $uuid]);

        if(!$media){
            return "";
        }

        return $media->getUrl();
    }
}
