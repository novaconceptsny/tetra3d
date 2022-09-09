<?php

namespace App\Models;

use App\Enums\Spot\PanoStatus;
use App\Enums\Spot\XmlStatus;
use App\Services\SpotXmlGenerator;
use Cache;
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
        'metadata' => SchemalessAttributes::class,
    ];

    public static function boot() {
        parent::boot();

        static::created(function(self $spot) {
            $spot->generateXml();
        });

        static::deleted(function(self $model) {
            $model->surfaces()->detach();
            $model->maps()->detach();

            if (\File::isDirectory($model->tour_path)){
                \File::deleteDirectory("$model->tour_path");
            }
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image_360')->singleFile();
        $this->addMediaCollection('overlays');
    }

    public function generateXml()
    {
        $xmlGenerator = new SpotXmlGenerator($this->fresh());
        $xmlGenerator->createXml();
    }

    public function scopeWithXml(): Builder
    {
        return $this->xml->modelScope();
    }

    public function scopeWithMetadata(): Builder
    {
        return $this->metadata->modelScope();
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function maps()
    {
        return $this->belongsToMany(Map::class)->withPivot(['x', 'y']);
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

    public function friendlyName(): Attribute
    {
        $name = $this->name;
        if (config('app.debug_tour')) {
            $name .= " (spot_{$this->id})";
        }

        return Attribute::make(
            get: fn() => $name
        );
    }

    public function xmlPath(): Attribute
    {
        $path = "{$this->tour_path}/tour.xml";
        return Attribute::make(
            get: fn() => $path
        );
    }

    public function xmlUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => asset("storage/tours/{$this->tour_id}/{$this->id}/tour.xml")
        );
    }

    public function xmlStatus()
    {
        if (! file_exists($this->xml_path)) {
            return XmlStatus::NOT_PRESENT;
        }

        return XmlStatus::PRESENT;
    }

    public function panoStatus($reset_cache = false)
    {
        $base_dir = $this->tour_path;
        $pano_dir = "$base_dir/panos";
        $cache_key = "pano_{$this->id}_status";

        if ($reset_cache){
            Cache::forget($cache_key);
        }

        return Cache::remember($cache_key, now()->addMinutes(10),
            function () use ($pano_dir) {
                if ( ! \File::isDirectory($pano_dir)) {
                    return PanoStatus::NOT_PRESENT;
                }

                if (count(\File::allFiles($pano_dir)) < 415) {
                    return PanoStatus::INVALID;
                }

                return PanoStatus::PRESENT;
            }
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
