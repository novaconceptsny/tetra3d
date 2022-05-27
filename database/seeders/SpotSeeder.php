<?php

namespace Database\Seeders;

use App\Models\Spot;
use Illuminate\Database\Seeder;

class SpotSeeder extends Seeder
{
    public function run()
    {
        Spot::updateOrCreate([
            'name' => 'p4point1',
            'krpano_path' => 'assets/KRpano/p4/p48001'
        ], []);
    }
}
