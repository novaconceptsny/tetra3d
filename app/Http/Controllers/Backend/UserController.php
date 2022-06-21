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
            'first_name', 'last_name'
        ]));
    }

    public function show(User $user)
    {
        //
    }

    public function edit(User $user)
    {
        //
    }

    public function update(Request $request, User $user)
    {
        $request->validate(ValidationRules::updateUser($user));
    }

    public function destroy(User $user)
    {
        //
    }
}
