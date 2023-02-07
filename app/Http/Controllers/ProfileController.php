<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {

    }

    public function edit()
    {
        $data['user'] = auth()->user();

        return view('pages.profile', $data);
    }

    public function update(Request $request)
    {
        $user = user();

        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => "required|unique:users,email,$user->id"
        ]);

        user()->update($request->only([
            'first_name', 'last_name', 'email'
        ]));

        user()->addFromMediaLibraryRequest($request->avatar)
            ->toMediaCollection('avatar');

        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        auth()->user()->update([
            'password' => $request->new_password
        ]);

        return redirect()->back()->with('success', 'Password updated successfully');
    }
}
