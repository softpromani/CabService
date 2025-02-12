<?php
namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'nullable|email|unique:users,email,' . auth()->id(),
            'gender'     => 'nullable|string|max:10',
            'user_image' => 'nullable|image|max:2048',
        ]);
        $user = Auth::user();
        $user->fill([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'gender'     => $request->gender,
            'is_profile' => 1,
        ]);

        // Handle user image upload
        if ($request->hasFile('user_image')) {
            $path             = $request->file('user_image')->store('user_images', 'public');
            $user->user_image = $path;
        }

        // Ensure the User role is assigned
        if (! $user->hasRole('User')) {
            $user->assignRole('User');
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'role'    => 'User',
            'data'    => $user,
        ], 200);
    }
}
