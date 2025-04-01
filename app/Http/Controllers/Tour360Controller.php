<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Tour360Controller extends Controller
{
    public function index()
    {
        // Add sample data for projects
        $projects = [
            [
                'title' => 'Living Room',
                'image' => 'path_to_living_room_image',
                'created_at' => '2024-06-11',
            ],
            // Add more projects as needed
        ];

        return view('tour360.index', compact('projects'));
    }
} 