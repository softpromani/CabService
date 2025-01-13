<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate request data
        $validated = Validator::make($request->all(), [
            'phone' => 'required|numeric',
            'is_otp_verify' => 'required|in:true',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $validatedData = $validated->validated();
        $phone = $validatedData['phone'];

        // Retrieve or create the user
        $user = User::firstOrCreate(
            ['phone' => $phone],
            ['is_profile' => 0]// Default attributes if user is created
        );
        if($user->is_active==false){
            return response()->json([
                'message'=>'!OPPs Your Account Suspended,Please contact with support'
            ],500);
        }
        try {
            // Assign the "Driver" role if not already assigned
            if (!$user->hasRole('Driver')) {
                $user->assignRole('Driver');
            }

            // Generate access token
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->accessToken;

            // Determine response message
            $message = $user->wasRecentlyCreated || $user->is_profile == 0
            ? 'Profile is not completed yet. Please complete your profile to login.'
            : 'Login successful';

            $statusCode = $user->wasRecentlyCreated || $user->is_profile == 0 ? 201 : 200;

            // Return response
            return response()->json([
                'message' => $message,
                'data' => $user,
                'token' => $token,
            ], $statusCode);
        } catch (\Exception $e) {
            Log::error('Error processing login: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while processing your login.',
            ], 500);
        }
    }



}
