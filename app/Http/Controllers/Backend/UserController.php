<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ValidationRules;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(25);
        return view('backend.user.index', compact('users'));
    }

    public function create()
    {
        $data = array();
        $data['route'] = route('backend.users.store');

        return view('backend.user.form', $data);
    }

    public function store(Request $request)
    {
        $request->validate(ValidationRules::storeUser());

        $user = User::create($request->only([
            'first_name', 'last_name', 'email', 'password', 'company_id'
        ]));

        $user->assignRole($request->role);

        return redirect()->route('backend.users.index')->with('success', 'User created successfully');
    }

    public function show(User $user)
    {
        //
    }

    public function edit(User $user)
    {
        $data = array();
        $data['route'] = route('backend.users.update', $user);
        $data['method'] = 'put';
        $data['user'] = $user;

        return view('backend.user.form', $data);
    }

    public function update(Request $request, User $user)
    {
        $request->validate(ValidationRules::updateUser($user));

        $user->update($request->only([
            'first_name', 'last_name', 'email', 'password', 'company_id'
        ]));

        $user->assignRole($request->role);

        return redirect()->back()->with('success', 'User created successfully');
    }

    public function destroy(User $user)
    {
        //
    }
}
