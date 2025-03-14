<?php
namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Models\UserDocument;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{

    public function profile_update(Request $request)
    {
        // return response()->json($request->all());
        $validated = $request->validate([
            'first_name'             => 'required',
            'last_name'              => 'required',
            'email'                  => 'required|email|unique:users,email,' . Auth::id(),
            'gender'                 => 'required|in:male,female',
            'dob'                    => 'required|date',
            'user_image'             => 'sometimes|image|max:2024',
            'driving_licence_number' => 'required',
            'driving_licence_front'  => 'required|image|max:1024',
            'driving_licence_back'   => 'required|image|max:1024',
            'aadhar_number'          => 'required',
            'aadhar_front'           => 'required|image|max:1024',
            'aadhar_back'            => 'required|image|max:1024',
            'pan_number'             => 'sometimes',
            'pan'                    => 'required_if:pan_number,!null|image|max:1024',
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

        try {
            if ($request->hasFile('user_image')) {
                $user->user_image = $request->file('user_image')->store('user_images', 'public');
            }

            $user->save();

            $documentTypes = [
                'driving_licence' => ['number' => 'driving_licence_number', 'front' => 'driving_licence_front', 'back' => 'driving_licence_back'],
                'adhar_card'      => ['number' => 'aadhar_number', 'front' => 'aadhar_front', 'back' => 'aadhar_back'],
                'pan_card'        => ['number' => 'pan_number', 'front' => 'pan'],
            ];

            foreach ($documentTypes as $type => $fields) {
                $identityNumber = $validated[$fields['number']] ?? null;
                $documentFront  = $request->hasFile($fields['front']) ? $request->file($fields['front'])->store('userDocuments', 'public') : null;
                $documentBack   = isset($fields['back']) && $request->hasFile($fields['back']) ? $request->file($fields['back'])->store('userDocuments', 'public') : null;

                if ($identityNumber) {
                    UserDocument::updateOrCreate(
                        ['user_id' => $user->id, 'identity_type' => $type],
                        [
                            'identity_number' => $identityNumber,
                            'document'        => $documentFront,
                            'document_back'   => $documentBack,
                        ]
                    );
                }
            }

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

    public function get_profile()
    {
        $createdAt = Carbon::parse(auth()->user()->created_at); // Force Carbon instance
        $now       = Carbon::now();

        $years  = $createdAt->diff($now)->y; // Get only years
        $months = $createdAt->diff($now)->m; // Get only months

        $data = [
            'user'              => auth()->user(),
            'orders'            => 10,
            'registered_months' => "{$years}y {$months}m", // Correct string formatting
            'cars_registration' => optional(auth()->user()->car)->registration_number,
            'documents'         => auth()->user()->documents,
        ];
        return response()->json(['data' => $data, 'message' => 'Profile Fetch Successfully']);
    }

}
