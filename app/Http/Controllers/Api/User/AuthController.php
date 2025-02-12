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
        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $validatedData = $validated->validated();
        $phone         = $validatedData['phone'];

        // Assign role if not already assigned
        if (! $user->hasRole($role)) {
            $user->assignRole($role);
        }

        // Token generation
        $tokenResult = $user->createToken('Personal Access Token');
        $token       = $tokenResult->accessToken;
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
