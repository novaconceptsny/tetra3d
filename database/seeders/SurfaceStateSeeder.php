<?php

namespace Database\Seeders;

use App\Models\SurfaceState;
use Illuminate\Database\Seeder;

class SurfaceStateSeeder extends Seeder
{
    public function run()
    {
        SurfaceState::updateOrCreate([
            'surface_id' => 1,
            'name' => 'Test',
            'active' => 1,
            'canvas' => [],
        ], []);
    }
}
