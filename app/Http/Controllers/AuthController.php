<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

public function showLoginForm()
{
    return view('auth.login');
}

public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($request->only('email', 'password'))) {
        return redirect()->route('users.index');
    }

    return back()->withErrors(['email' => 'Invalid credentials']);
}

public function logout()
{
    Auth::logout();
    return redirect()->route('login');
}