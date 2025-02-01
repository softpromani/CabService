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
            'phone' => 'required|numeric',
        ]);

        $user = User::updateOrCreate(
            ['phone' => $validated['phone']],
        );

        // Assign role if not already assigned
        if (! $user->hasRole($role)) {
            $user->assignRole($role);
        }

        // Token generation
        $tokenResult = $user->createToken('Personal Access Token');
        $token       = $tokenResult->accessToken;

        if ($user->is_profile == 1) {
            return response()->json([
                'message' => 'Login successful',
                'role'    => $role,
                'data'    => $user,
                'token'   => $token,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Profile is not completed. Please complete your profile to login.',
                'token'   => $token,
            ], 201);
        }
    }

}
