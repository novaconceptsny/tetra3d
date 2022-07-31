<?php

namespace App\Models;

use App\MediaLibrary\InteractsWithMedia;
use App\Traits\HasCompany;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\HasMedia;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

class Artwork extends Model implements HasMedia
{
    use HasCompany, InteractsWithMedia, Sortable;

    protected $guarded = ['id'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile();
    }

    public $casts = [
        'data' => SchemalessAttributes::class,
    ];

    public function scopeWithData(): Builder
    {
        return $this->data->modelScope();
    }

    public function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->getFirstMediaUrl('image')
                ? $this->getFirstMediaUrl('image') : $value
        );
    }
}
