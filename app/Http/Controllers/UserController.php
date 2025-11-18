<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->paginate(15);
        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email',
            'password'=> 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id'
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return back()->with('success', 'Gebruiker aangemaakt.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'password'=> 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id'
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return back()->with('success', 'Gebruiker bijgewerkt.');
    }

    public function destroy(User $user)
    {
        // Optioneel: voorkom dat je jezelf verwijderd
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Je kunt jezelf niet verwijderen.');
        }

        $user->delete();

        return back()->with('success', 'Gebruiker verwijderd.');
    }
}
