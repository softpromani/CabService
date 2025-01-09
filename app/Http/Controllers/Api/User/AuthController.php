<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function userLogin(Request $request)
    {
        return $this->login($request, 'User');
    }

    private function login(Request $request, $role)
    {
        $validated = $request->validate([
            'phone' => 'required|numeric',
        ]);

        $user = User::updateOrCreate(
            ['phone' => $validated['phone']],
            ['is_profile' => $validated['phone'] ? 1 : 0]
        );

        // Assign role if not already assigned
        if (!$user->hasRole($role)) {
            $user->assignRole($role);
        }

        // Token generation
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->accessToken;

        if ($user->is_profile == 1) {
            return response()->json([
                'message' => 'Login successful',
                'role' => $role,
                'data' => $user,
                'token' => $token,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Profile is not completed. Please complete your profile to login.',
                'token' => $token,
            ], 201);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'gender' => 'nullable|string|max:10',
            'user_image' => 'nullable|image|max:2048',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(), 422);
        }

        $user->fill([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'gender' => $request->gender,
            'is_profile' => 1,
        ]);

        // Handle user image upload
        if ($request->hasFile('user_image')) {
            $path = $request->file('user_image')->store('user_images', 'public');
            $user->user_image = $path;
        }

        // Ensure the User role is assigned
        if (!$user->hasRole('User')) {
            $user->assignRole('User');
        }

        $user->save();

        // Remove roles from the user object in the response
        $userArray = $user->toArray();
        unset($userArray['roles']);

        return response()->json([
            'message' => 'Profile updated successfully',
            'role' => 'User',
            'data' => $userArray,
        ], 200);
    }

}
