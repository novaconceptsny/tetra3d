<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ValidationRules;
use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function index()
    {
        $users = User::forCurrentCompany()->paginate(25);
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

        $user->addFromMediaLibraryRequest($request->avatar)
            ->toMediaCollection('avatar');

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
            'first_name', 'last_name', 'email', 'company_id'
        ]));

        // update password
        if ($request->password){
            $user->update([
                'password' => $request->password
            ]);
        }

        $user->assignRole($request->role);

        $user->addFromMediaLibraryRequest($request->avatar)
            ->toMediaCollection('avatar');

        return redirect()->back()->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        //
    }

    public function loginAs(Request $request, User $user)
    {
        $this->authorize('switch to user');
        session()->put('admin_id', auth()->id());
        Auth::login($user);
        $route = $user->can('access-backend') ? 'backend.dashboard' : 'dashboard';
        return redirect()->route($route)->with('success', __("You are now logged in as "). $user->name);
    }

    public function backToAdmin(Request $request)
    {
        $admin_id = session()->pull('admin_id');
        $user = User::findOrFail($admin_id);
        $this->authorizeForUser($user, 'switch to user');

        Auth::loginUsingId($admin_id);
        session()->forget(['applicant_type', 'applicant_id', 'applicant', 'admin_id']);
        return redirect()->route('backend.users.index')->with('success', __("You are now logged in as admin"));
    }
}
