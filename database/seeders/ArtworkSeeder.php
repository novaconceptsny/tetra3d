<?php

namespace Database\Seeders;

use App\Models\Artwork;
use Illuminate\Database\Seeder;

class ArtworkSeeder extends Seeder
{
    public function run()
    {
        $images = \File::allFiles(public_path('/images/collection'));

        foreach ($images as $image){
            Artwork::firstOrCreate([
                'name' => $image->getBasename(),
            ],[]);
        }
    }
}
