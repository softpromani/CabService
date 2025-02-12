<?php

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    

    public function userLogin(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'phone'         => 'required|numeric',
            'is_otp_verify' => 'required|in:true',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $validatedData = $validated->validated();
        $phone         = $validatedData['phone'];

        $user = User::firstOrCreate(
            ['phone' => $phone],
            ['is_profile' => 0],
            ['is_active' => 1],
        );
        $tokenResult = $user->createToken('Personal Access Token');
        $token       = $tokenResult->accessToken;

        
        try {
            if (! $user->hasRole('user')) {
                $user->assignRole('user');
            }

            $message = $user->wasRecentlyCreated || $user->is_profile == 0
            ? 'Profile is not completed yet. Please complete your profile to login.'
            : 'Login successful';

            $statusCode = $user->wasRecentlyCreated || $user->is_profile == 0 ? 201 : 200;

            return response()->json([
                'message' => $message,
                'data'    => $user,
                'token'   => $token,
            ], $statusCode);
        } catch (\Exception $e) {
            Log::error('Error processing login: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while processing your login.',
            ], 500);
        }
    }

    public function userProfileupdate(Request $request)
    {
        $validated = $request->validate([
            'first_name'             => 'required',
            'last_name'              => 'required',
            'email'                  => 'required|email|unique:users,email,' . Auth::id(),
            'gender'                 => 'required|in:male,female',
            'dob'                    => 'required|date',
            'user_image'             => 'nullable|image|max:2024',
            
        ]);

        $user             = Auth::user();
        $user->first_name = $validated['first_name'];
        $user->last_name  = $validated['last_name'];
        $user->email      = $validated['email'];
        $user->gender     = $validated['gender'];
        $user->dob        = $validated['dob'];
        $user->country_id = $request->country_id;
        $user->state_id   = $request->state_id;
        $user->city_id    = $request->city_id;
        $user->address    = $request->address;
        $user->is_profile = 1;
        $user->is_verify = 1; 

        try {
            if ($request->hasFile('user_image')) {
                $user->user_image = $request->file('user_image')->store('user_images', 'public');
            }
            $user->save();  

           

            return response()->json([
                'message' => 'Profile updated successfully',
                'data'    => $user,
            ], 200);

        } catch (Exception $ex) {
            return response()->json([
                'message' => 'Error updating profile: ' . $ex->getMessage(),
            ], 500);
        }
    }

}
