<?php

namespace App\Http\Controllers;

use App\Models\Layout;

class SharePageController extends Controller
{
    public function index()
    {
        // Fetch all layouts
        $layouts = Layout::all();

        // Attach a thumbnail_url property to each layout
        foreach ($layouts as $layout) {
            $tour = $layout->assignedTour();
            $thumbnailUrl = $tour ? $tour->getFirstMediaUrl('thumbnail') : null;
            $layout->thumbnail_url = $thumbnailUrl;
        }

        return view('share.index', compact('layouts'));
    }
}
