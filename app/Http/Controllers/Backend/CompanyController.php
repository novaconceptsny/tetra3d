<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ValidationRules;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::paginate(25);
        return view('backend.company.index', compact('companies'));
    }

    public function create()
    {
        $data = array();
        $data['route'] = route('backend.companies.store');

        return view('backend.company.form', $data);
    }

    public function store(Request $request)
    {
        $request->validate(ValidationRules::storeCompany());

        Company::create([
            $request->only('name')
        ]);

        return redirect()->route('backend.companies.index')
            ->with('success', 'Company created successfully');
    }

    public function show(Company $company)
    {
        //
    }

    public function edit(Company $company)
    {
        $data = array();
        $data['route'] = route('backend.companies.update', $company);
        $data['method'] = 'put';
        $data['company'] = $company;

        return view('backend.company.form', $data);
    }

    public function update(Request $request, Company $company)
    {
        $request->validate(ValidationRules::updateCompany());

        $company->update($request->only([
            'name'
        ]));

        return redirect()->route('backend.companies.index')
            ->with('success', 'Company updated successfully');
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->back()->with('success', 'Company deleted successfully');
    }
}
