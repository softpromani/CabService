<?php
namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function userLogin(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric',
        ]);

        return $this->login($request, 'User');
    }

    private function login(Request $request, $role)
    {
        $validated = $request->validate([
            'phone'         => 'required|numeric',
            'is_otp_verify' => 'required|in:true',
        ]);

        // Update or create user based on phone number
        $user = User::updateOrCreate(
            ['phone' => $validated['phone']], // Lookup condition
            []                                // Defaults (add fields here if needed)
        );

        // Assign role if not already assigned
        if (! $user->hasRole($role)) {
            $user->assignRole($role);
        }

        // Token generation
        $tokenResult = $user->createToken('Personal Access Token');
        $token       = $tokenResult->accessToken->plainTextToken;

        try {
            if (! $user->hasRole('user')) {
                $user->assignRole('user');
            }

            if ($user->wasRecentlyCreated || $user->is_profile == 0) {
                return response()->json([
                    'message' => 'Profile is not completed yet. Please complete your profile to login.',
                    'role'    => $role,
                    'data'    => $user,
                    'token'   => $token,
                ], 201);
            }

            return response()->json([
                'message' => 'Login successful',
                'role'    => $role,
                'data'    => $user,
                'token'   => $token,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong. Please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

}
