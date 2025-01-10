<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private function login(Request $request, $role)
    {
        $is_otp = true;

        $validated = Validator::make($request->all(), [
            'phone' => 'required|numeric',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(), 422);
        }

        $validatedData = $validated->validated();
        $phone = $validatedData['phone'];

        $user = User::where('phone', $phone)->first();

        if ($user) {
            if ($is_otp) {
                if ($user->is_profile == 1) {
                    if (!$user->hasRole($role)) {
                        $user->assignRole($role);
                    }

                    // Token Generation
                    $tokenResult = $user->createToken('Personal Access Token');
                    $token = $tokenResult->accessToken;

                    return response()->json([
                        'message' => 'Login successful',
                        'role' => $role,
                        'data' => $user,
                        'token' => $token,
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'Profile is not completed. Please complete your profile to login.',
                    ], 400);
                }
            } else {
                return response()->json([
                    'message' => 'OTP validation failed',
                ], 400);
            }
        } else {
            try {
                $user = User::create([
                    'phone' => $phone,
                    'is_profile' => 0,
                ]);
                $user->assignRole($role);

                // Token Generation
                $tokenResult = $user->createToken('Personal Access Token');
                $token = $tokenResult->accessToken;

                return response()->json([
                    'message' => 'Profile is not completed yet. Please complete your profile to login',
                    'role' => $role,
                    'data' => $user,
                    'token' => $token,
                ], 201);
            } catch (\Exception $e) {
                Log::error('Error creating user: ' . $e->getMessage());
                return response()->json([
                    'message' => 'An error occurred while creating the user.',
                ], 500);
            }
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
            'gender' => 'nullable',
            'user_image' => 'nullable',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(), 422);
        }
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->gender = $request->gender;
        $user->is_profile = 1;

        // Handle user image upload
        if ($request->hasFile('user_image')) {
            $image = $request->file('user_image');
            $path = $image->store('user_images', 'public');
            $user->user_image = $path;
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $user,
        ], 200);
    }

}
