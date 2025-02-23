<?php
namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Models\UserDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return void
     * @header Bearer
     */
    public function profile_update(Request $request)
    {
        $validated = $request->validate([
            'first_name'             => 'required',
            'last_name'              => 'required',
            'email'                  => 'required|email|unique:users,email,' . Auth::id(),
            'gender'                 => 'required|in:male,female',
            'dob'                    => 'required|date',
            'user_image'             => 'sometimes|image|max:2024',
            'driving_licence_number' => 'required',
            'driving_licence_front'  => 'nullable',
            'driving_licence_back'   => 'nullable',
            'aadhar_number'          => 'required',
            'aadhar_front'           => 'nullable',
            'aadhar_back'            => 'nullable',
            'pan_number'             => 'sometimes',
            'pan'                    => 'nullable',
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

}
