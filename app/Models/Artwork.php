<?php

namespace App\Models;

use App\MediaLibrary\InteractsWithMedia;
use App\Traits\HasCompany;
use App\Traits\Searchable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Intervention\Image\Facades\Image;
use Spatie\MediaLibrary\HasMedia;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

class Artwork extends Model implements HasMedia
{
    use HasCompany, InteractsWithMedia, Sortable, Searchable;

    protected $guarded = ['id'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile();
    }

    public $casts = [
        'data' => SchemalessAttributes::class,
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(fn($model) => $model->data->scale = $model->calculateScale());
        static::updating(fn($model) => $model->data->scale = $model->calculateScale());

        static::deleted(function (self $model) {
            $model->surfaceStates()->detach();
        });
    }

    public function collection()
    {
        return $this->belongsTo(
            ArtworkCollection::class,
            'artwork_collection_id'
        )->withDefault(['name' => 'No Collection']);
    }

    public function scopeWithData(): Builder
    {
        return $this->data->modelScope();
    }

    public function surfaceStates()
    {
        return $this->belongsToMany(SurfaceState::class);
    }

    public function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->getFirstMediaUrl('image')
            ? $this->getFirstMediaUrl('image') : $value
        );
    }

    public function dimensions(): Attribute
    {
        return Attribute::make(
            get: fn($value) => "{$this->data->height_inch}x{$this->data->width_inch}x1"
        );
    }

    public function calculateScale()
    {
        if (!$this->data->width_inch || !$this->data->height_inch) {
            return 1;
        }

        $maxWidth = $maxHeight = 1000;
        $scaleWidth = $maxWidth / $this->data->width_inch;
        $scaleHeight = $maxHeight / $this->data->height_inch;

        return intval(max($scaleWidth, $scaleHeight));
    }

    public function resizeImage()
    {
        $media = $this->getFirstMedia('image');

        ini_set('memory_limit', '1G');

        $image = Image::make($media->getPath());

        $image->resize(
            $this->data->scale * $this->data->width_inch,
            $this->data->scale * $this->data->height_inch
        );

        $this->addMediaFromBase64($image->encode('data-url'))
            ->usingFileName($media->file_name)
            ->usingName($media->name)
            ->toMediaCollection('image');
    }

    public function getTypeAttribute($value)
    {
        return $value ?? 'Unknown';
    }
}
