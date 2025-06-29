<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;;

class UserController extends Controller
{
    public function index()
{
    $users = User::all();
    return view('profile.index', compact('users'));
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255|unique:users,name',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:5',
    ]);

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
}
public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    $request->validate([
        'name' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
        'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
        'password' => ['nullable', 'string', 'min:6'],
    ]);

    $user->name = $request->name;
    $user->email = $request->email;

    if ($request->filled('password')) {
        $user->password = bcrypt($request->password);
    }

    $user->save();

    return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
}
public function destroy($id)
{
    $user = User::findOrFail($id);
    $user->delete();

    return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
}
}
