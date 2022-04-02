<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@system.com'],
            [
                'first_name' => 'Abdul Majeed',
                'last_name' => 'Shehzad',
                'password' => bcrypt('password'),
            ]);
    }
}
