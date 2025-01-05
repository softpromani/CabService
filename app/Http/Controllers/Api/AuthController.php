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
    public function driverLogin(Request $request)
    {
        return $this->login($request, 'Driver');
    }

    public function userLogin(Request $request)
    {
        return $this->login($request, 'User');
    }

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

    // public function login(Request $request)
    // {
    //     $is_otp = true; // Assuming OTP validation is always true for now

    //     // Validate request parameters
    //     $validated = Validator::make($request->all(), [
    //         'phone' => 'required|numeric',
    //         'role' => 'required|in:Driver,User',
    //     ]);

    //     // If validation fails, return the errors
    //     if ($validated->fails()) {
    //         return response()->json($validated->errors(), 422);
    //     }

    //     // Get validated data
    //     $validatedData = $validated->validated();
    //     $phone = $validatedData['phone'];
    //     $role = $validatedData['role'];

    //     // Check if the user exists with the given phone number
    //     $user = User::where('phone', $phone)->first();

    //     if ($user) {
    //         // If OTP is valid (assuming it's always true for now)
    //         if ($is_otp) {
    //             // Check if the profile is completed (is_profile = 1)
    //             if ($user->is_profile == 1) {
    //                 // If profile is completed, allow login
    //                 if (!$user->hasRole($role)) {
    //                     // Assign the role to the user if not already assigned
    //                     $user->assignRole($role);
    //                 }

    //                 return response()->json([
    //                     'message' => 'Login successful',
    //                     'role' => $role,
    //                     'data' => $user,
    //                 ], 200);
    //             } else {
    //                 // If the profile is not completed (is_profile = 0), do not allow login
    //                 return response()->json([
    //                     'message' => 'Profile is not completed. Please complete your profile to login.',
    //                 ], 400);
    //             }
    //         } else {
    //             // OTP validation failed
    //             return response()->json([
    //                 'message' => 'OTP validation failed',
    //             ], 400);
    //         }
    //     } else {
    //         // If the user does not exist, create a new user
    //         try {
    //             $user = User::create([
    //                 'phone' => $phone,
    //                 'is_profile' => 0, // Profile is not completed yet
    //             ]);

    //             // Assign the role to the new user
    //             $user->assignRole($role);

    //             return response()->json([
    //                 'message' => 'User created, profile is not completed yet. Please complete your profile.',
    //                 'role' => $role,
    //                 // 'data' => $user,
    //             ], 201);
    //         } catch (\Exception $e) {
    //             // Log the error for debugging
    //             Log::error('Error creating user: ' . $e->getMessage());
    //             return response()->json([
    //                 'message' => 'An error occurred while creating the user.',
    //             ], 500);
    //         }
    //     }
    // }

}
