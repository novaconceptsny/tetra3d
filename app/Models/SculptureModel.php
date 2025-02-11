<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Intervention\Image\Facades\Image;
use App\MediaLibrary\InteractsWithMedia;
use App\Traits\HasCompany;
use App\Traits\Searchable;
use App\Traits\Sortable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;
class SculptureModel extends Model implements HasMedia
{
    use HasCompany, InteractsWithMedia, Sortable, Searchable;

    protected $fillable = ['artwork_collection_id', 'company_id', 'name', 'artist', 'type', 'sculpture_url', 'image_url', 'data'] ;

    protected $guarded = ['id'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')
            ->singleFile();

        $this->addMediaCollection('sculpture')
            ->singleFile();

        $this->addMediaCollection('interaction')
            ->singleFile();
    }

    public $casts = [
        'data' => SchemalessAttributes::class,
    ];

    public static function boot()
    {
        parent::boot();

        // static::deleted(function (self $model) {
        //     $model->surfaceStates()->detach();
        // });
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

    public function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->getFirstMediaUrl('thumbnail')
            ? $this->getFirstMediaUrl('thumbnail') : $value
        );
    }

    public function dimensions(): Attribute
    {
        $length = number_format((float)$this->data->length, 2);
        $width = number_format((float)$this->data->width, 2);
        $height = number_format((float)$this->data->height, 2);
        
        return Attribute::make(
            get: fn($value) => "{$length}x{$width}x{$height}"
        );
    }

    public function getTypeAttribute($value)
    {
        return $value ?? 'Unknown';
    }
}
