<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RegisteredUserController extends Controller
{
    public function create()
    {
        if (User::exists()) {
            return redirect()->route('login');
        }

        return view('auth.register');
    }

    public function store(Request $request)
    {
        if (User::exists()) {
            return redirect()->route('login')->with('error', 'Public registration is disabled. Ask a superadmin to create your account.');
        }

        $userAttributes = $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        $userAttributes['role'] = User::count() === 0 ? 'superadmin' : 'employee';
        $userAttributes['status'] = 'active';

        $user = User::create($userAttributes);

        Auth::login($user);

        return redirect('/');
    }
}