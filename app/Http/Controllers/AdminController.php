<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }
    public function userList()
    {
        $users = User::get();
        return view('admin.user.userList', compact('users'));
    }
    public function addUser()
    {
        $roles = Role::get();
        return view('admin.user.add-user', compact('roles'));
    }
    public function editUser(string $id)
    {
        $roles = Role::get();
        $editUser = User::find($id);
        $currentRole = $editUser->roles->first()->id ?? null;
        return view('admin.user.add-user', compact('roles', 'editUser', 'currentRole'));
    }
    public function updateUser(Request $request, string $id)
    {

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required',
            'gender' => 'nullable',
            'password' => 'nullable',
            'user_image' => 'nullable',
            'roleid' => 'nullable',
        ]);

        $user = User::find($id);
        $user->gender = $request->input('gender');

        if (!$user) {
            toastr()->error('User not found');
            return redirect()->route('admin.user.index');
        }
        if ($request->hasFile('user_image')) {
            $imageName = $request->file('user_image')->store('userImages', 'public');

            $user->user_image = $imageName;
        }
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->input('password'));
        } else {
            unset($validated['password']);
        }
        $user->update($validated);

        $role_name = Role::find($request->roleid);
        if ($role_name) {
            $user->syncRoles($role_name);
        }
        toastr()->success('User updated successfully');
        return redirect()->route('admin.userList');
    }
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'gender' => 'nullable',
            'password' => 'nullable',
            'user_image' => 'nullable',
            'roleid' => 'nullable',
        ]);

        // Process the image upload if provided
        if ($request->hasFile('user_image')) {
            $validated['user_image'] = $request->file('user_image')->store('userImages', 'public');
        }

        // Hash the password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        // Create the user
        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'] ?? null,
            'password' => $validated['password'] ?? null,
            'user_image' => $validated['user_image'] ?? null,
        ]);

        if (!$user) {
            toastr()->error('User creation failed');
            return redirect()->route('admin.user.index');
        }

        // Assign role if provided
        if (!empty($validated['roleid'])) {
            $role = Role::find($validated['roleid']);
            if ($role) {
                $user->syncRoles($role);
            }
        }

        toastr()->success('User created successfully');
        return redirect()->route('admin.userList');
    }
}
