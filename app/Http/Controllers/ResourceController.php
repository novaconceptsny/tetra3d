<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
class ResourceController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            // Super admin: get all companies
            $companies = Company::all();
        } else {
            // Not super admin: get only the user's company
            $companies = Company::where('id', $user->company_id)->get();
        }

        return view('resource.index', compact('companies'));
    }
}
