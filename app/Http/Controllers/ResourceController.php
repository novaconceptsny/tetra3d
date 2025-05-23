<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
class ResourceController extends Controller
{
    public function index()
    {
        $companies = Company::all();
        return view('resource.index', compact('companies'));
    }
}
