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
                'bound_box_top' => 118,
                'bound_box_left' => 219,
                'bound_box_height' => 918,
                'bound_box_width' => 1500,
                'hotspot_width_px' => null,
                'actual_width_inch' => 389,

            ],
        ]);
    }
}
