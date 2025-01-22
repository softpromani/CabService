<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserProfileController extends Controller
{
    public function userProfile($id)
    {
        $roles = Role::all();
        $editUser = User::findOrFail($id);
        $currentRole = $editUser->roles->first()->id ?? null;

        return view('admin.user.userProfile', compact('roles', 'editUser', 'currentRole'));
    }



    public function updateUserProfile(Request $request, $id)
    {
        $validated = $request->validate([
            'full_name'  => 'required',
            'email'      => 'required|email|unique:users,email,' . $id,
            'phone'      => 'required',
            'gender'     => 'required',
            'password'   => 'nullable|min:8',
            'user_image' => 'nullable|image|max:2048',
        ]);

        $user = User::findOrFail($id);
        $nameParts = explode(' ', $request->input('full_name'), 2);
        $user->first_name = $nameParts[0];
        $user->last_name = isset($nameParts[1]) ? $nameParts[1] : '';

        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        $user->gender = $request->input('gender');

        if ($request->hasFile('user_image')) {
            $imageName = $request->file('user_image')->store('userImages', 'public');
            $user->user_image = $imageName;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        // Sync roles


        return redirect()->route('admin.userProfile', $id)
            ->with('success', 'Profile updated successfully!');
    }

    public function changePassword(Request $request)
{
    $validated = $request->validate([
        'password'       => 'required', // Current password
        'newpassword'    => 'required|min:8|confirmed', // New password must match confirmation
    ], [
        'newpassword.confirmed' => 'The new password confirmation does not match.',
    ]);

    $user = Auth::user(); // Get the currently authenticated user

    // Check if the current password matches
    if (!Hash::check($validated['password'], $user->password)) {
        return back()->withErrors(['password' => 'The current password is incorrect.']);
    }

    // Update the user's password
    $user->password = Hash::make($validated['newpassword']);
    $user->save();

    return back()->with('success', 'Password changed successfully!');
}

}



