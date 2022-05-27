<?php

namespace Database\Seeders;

use App\Models\Artwork;
use Illuminate\Database\Seeder;

class ArtworkSeeder extends Seeder
{
    public function run()
    {
        $artworks = array();

        require (database_path('source/artworks.php'));

        foreach ($artworks as $artwork){
            Artwork::updateOrCreate([
                'name' => $artwork['title'],
                'artist' => $artwork['artist'],
                'type' => $artwork['type'],
                'image_url' => $artwork['image_url'],
                'data' => array(
                    'scale' => $artwork['scale'],
                    'width_inch' => $artwork['width_inch'],
                    'height_inch' => $artwork['height_inch'],
                    'no_dimension' => $artwork['no_dimension'],
                )
            ]);
        }
    }
}
