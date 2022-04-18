<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run()
    {
        $projects = array(
            ['name' => '172 East 72',],
            ['name' => 'Ford_Config A',],
            ['name' => 'Pablo Picasso Show',],
        );

        foreach ($projects as $project) {
            Project::updateOrCreate([
                'name' => $project['name'],
            ], []);
        }
    }
}
