<?php
namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
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
            ['is_profile' => 0]
        );
        $tokenResult = $user->createToken('Personal Access Token');
        $token       = $tokenResult->accessToken;

        if ($user->is_active == 0) {
            return response()->json([
                'message' => '!OPPs Your Account Suspended,Please contact with support',
                'token'   => $token,
            ], 401);
        }
        try {
            if (! $user->hasRole('Driver')) {
                $user->assignRole('Driver');
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

}
