<?php

namespace Database\Seeders;

use App\Models\Spot;
use Illuminate\Database\Seeder;

class SpotSeeder extends Seeder
{
    public function run()
    {
        Spot::updateOrCreate([
            'tour_id' => 1,
            'name' => 'p4point1',
        ], []);
    }
}
