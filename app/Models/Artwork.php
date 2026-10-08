<?php

namespace App\Models;

use App\MediaLibrary\InteractsWithMedia;
use App\Traits\HasCompany;
use App\Traits\Searchable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Image\Image;
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

    public function getOriginalAspectRatio()
    {
        $media = $this->getFirstMedia('image');

        if (!$media) {
            return 1;
        }

        // Read the pixel size from the file header (no need to decode the whole image)
        $size = @getimagesize($media->getPath());

        if (!$size || empty($size[0]) || empty($size[1])) {
            return 1;
        }

        return $size[0] / $size[1];
    }

    public function updateSizeData()
    {
        $this->data->width_inch =  round($this->data->height_inch * $this->getOriginalAspectRatio(), 5);
        $this->save();
    }

    public function resizeImage()
    {
        $media = $this->getFirstMedia('image');

        if (!$media) {
            return;
        }

        ini_set('memory_limit', '1G');

        $image = Image::load($media->getPath());
        
        
        // Calculate new dimensions based on actual proportions and data-height value
        $targetHeight = $this->data->scale * $this->data->height_inch;
        
        // Debug logging: Output values to console/log
        logger('resizeImage Debug', [
            'scale' => $this->data->scale,
            'height_inch' => $this->data->height_inch,
            'targetHeight' => $targetHeight,
        ]);
        // Uncomment below to use dd() instead (stops execution):
        // dd([
        //     'scale' => $this->data->scale,
        //     'height_inch' => $this->data->height_inch,
        //     'targetHeight' => $targetHeight,
        // ]);
        
        $targetWidth = $targetHeight * $this->getOriginalAspectRatio();
        
        // If the calculated width exceeds the target width, use width as the constraint
        // $maxTargetWidth = $this->data->scale * $this->data->width_inch;
        // if ($targetWidth > $maxTargetWidth) {
        //     $targetWidth = $maxTargetWidth;
        //     $targetHeight = $targetWidth / $originalAspectRatio;
        // }

        $targetWidth  = (int) round($targetWidth);
        $targetHeight = (int) round($targetHeight);

        if ($targetWidth < 1 || $targetHeight < 1) {
            return;
        }

        $image->resize($targetWidth, $targetHeight);
        

        $this->save();

        // Keep the original file format (jpg/png/webp...) when re-encoding
        $format = strtolower(pathinfo($media->file_name, PATHINFO_EXTENSION));
        $format = in_array($format, ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? $format : 'jpeg';
        $format = $format === 'jpg' ? 'jpeg' : $format;

        $this->addMediaFromBase64($image->base64($format))
            ->usingFileName($media->file_name)
            ->usingName($media->name)
            ->toMediaCollection('image');
    }

    public function getTypeAttribute($value)
    {
        return $value ?? 'Unknown';
    }
}
