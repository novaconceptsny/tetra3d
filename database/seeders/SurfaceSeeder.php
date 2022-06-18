<?php

namespace Database\Seeders;

use App\Models\Surface;
use Illuminate\Database\Seeder;

class SurfaceSeeder extends Seeder
{
    public function run()
    {
        Surface::create([
            'tour_id' => 1,
            'name' => 's4001',
            'background_url' => '/canvas/s4001.jpg',
            'data' => [
                'bounding_box_top' => 118,
                'bounding_box_left' => 219,
                'bounding_box_height' => 918,
                'bounding_box_width' => 1500,
                'hotspot_width_px' => null,
                'actual_width_inch' => 389,

            ],
        ]);
    }
}
