<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run()
    {
        $companies = array(
            ['name' => 'Tishman Speyer'],
            ['name' => 'Phillips Gallery'],
            ['name' => 'Ford Foundation'],
        );

        foreach ($companies as $company){
            Company::updateOrCreate([
                'name' => $company['name']
            ],[]);
        }
    }
}
