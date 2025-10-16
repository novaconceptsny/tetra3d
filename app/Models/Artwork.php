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
    
    protected $fillable = [
        'name',
        'type',
        'description',
        'artwork_collection_id',
        'data',
        'company_id',
        'original_unit',
        'original_value',
        'tags'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile();
    }

    public $casts = [
        'data' => SchemalessAttributes::class,
        'tags' => 'array',
        'original_value' => SchemalessAttributes::class,
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
        // return Attribute::make(
        //     get: fn($value) => "{$this->data->height_inch}x{$this->data->width_inch}x1"
        // );

        if($this->original_value && !empty($this->original_value) && isset($this->original_value->unit)) {
            $unitSymbol = $this->original_value->unit  === 'inch' ? '"' : 'cm';

            return Attribute::make(
                get: fn($value) => "{$this->original_value->height}{$unitSymbol} x {$this->original_value->width}{$unitSymbol} x 1{$unitSymbol}"
            );
        }else{
            $unit = $this->original_unit ?? 'inch';
            $unitSymbol = $unit === 'inch' ? '"' : 'cm';
            return Attribute::make(
                get: fn($value) => "{$this->data->height_inch}{$unitSymbol} x {$this->data->width_inch}{$unitSymbol} x 1{$unitSymbol}"
            );
        }
    }

    public function getConvertedDimensions($projectUnit = 'imperial')
    {
        // If we have original_value with unit information
        if ($this->original_value && !empty($this->original_value) && isset($this->original_value->unit)) {
            $artworkUnit = $this->original_value->unit;
            
            // If project unit matches artwork unit, use original_value directly
            if (($projectUnit === 'imperial' && $artworkUnit === 'inch') || 
                ($projectUnit === 'metric' && $artworkUnit === 'cm')) {
                
                $height = $this->original_value->height;
                $width = $this->original_value->width;
                $unitSymbol = $artworkUnit === 'inch' ? 'inches' : 'cm';
                
                return "{$height} x {$width}x1 {$unitSymbol}";
            }
            
            if($projectUnit === 'metric' && $artworkUnit === 'inch'){
                $height = round($this->original_value->height * 2.54, 2);
                $width = round($this->original_value->width * 2.54, 2);
                $unitSymbol = 'cm';
                
                return "{$height} x {$width}x1 {$unitSymbol}";
            }

            if($projectUnit === 'imperial' && $artworkUnit === 'cm'){
                $height = round($this->data->height_inch, 1);
                $width = round($this->data->width_inch, 1);
                $unitSymbol = 'inches';
                
                return "{$height} x {$width}x1 {$unitSymbol}";
            }
        }
        
        // Fallback to data properties if no original_value
        if (!$this->data->height_inch || !$this->data->width_inch) {
            return '';
        }

        $height = $this->data->height_inch;
        $width = $this->data->width_inch;
        $unit = 'inches';

        if ($projectUnit === 'metric' ) {
            // Convert from inches to centimeters
            $height = round($height * 2.54, 2);
            $width = round($width * 2.54, 2);
            $unit = 'cm';
        }

        return "{$height} x {$width}x1 {$unit}";
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
        
        // Get the original image actual proportions
        $originalWidth = $image->width();
        $originalHeight = $image->height();
        $originalAspectRatio = $originalWidth / $originalHeight;
        
        // Calculate new dimensions based on actual proportions and data-width value
        $targetWidth = $this->data->scale * $this->data->width_inch;
        $targetHeight = $targetWidth / $originalAspectRatio;
        
        // If the calculated height exceeds the target height, use height as the constraint
        // $maxTargetHeight = $this->data->scale * $this->data->height_inch;
        // if ($targetHeight > $maxTargetHeight) {
        //     $targetHeight = $maxTargetHeight;
        //     $targetWidth = $targetHeight * $originalAspectRatio;
        // }

        $image->resize($targetWidth, $targetHeight);


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
