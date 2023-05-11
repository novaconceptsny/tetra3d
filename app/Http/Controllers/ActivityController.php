<?php

namespace App\Http\Controllers;

use App\Models\Activity;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::paginate(25);
        return view('activity.index', compact('activities'));
    }
}
