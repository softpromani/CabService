<?php
namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Models\UserDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return void
     * @header Bearer
     */
    public function profile_update(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'first_name'             => 'required',
            'last_name'              => 'required',
            'email'                  => 'required|email|unique:users,email,' . Auth::id(),
            'gender'                 => 'required|in:male,female',
            'user_image'             => 'required|image|max:2024',
            'driving_licence_number' => 'required',
            'driving_licence_front'  => 'required|image|max:2024',
            'driving_licence_back'   => 'required|image|max:2024',
            'aadhar_number'          => 'required',
            'aadhar_front'           => 'required|image|max:2024',
            'aadhar_back'            => 'required|image|max:2024',
            'pan_number'             => 'sometimes',
            'pan'                    => 'required_if:pan_number,!=,null|image|max:2024',
            'dob'                    => 'required|date',
        ]);

        if ($validated->fails()) {
            \Log::error($validated->errors());
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $user             = Auth::user();
        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->email      = $request->email;
        $user->gender     = $request->gender;
        $user->dob        = $request->dob;
        $user->is_profile = 1;

        // Handle user image upload
        if ($request->hasFile('user_image')) {
            $image            = $request->file('user_image');
            $path             = $image->store('user_images', 'public');
            $user->user_image = $path;
        }
        $user->save();

        // Handle driving licence uploads
        if ($request->hasFile('driving_licence_front')) {
            $dl_front      = $request->file('driving_licence_front');
            $path_dl_front = $dl_front->store('driver_licence/front', 'public');

            UserDocument::create([
                'user_id'         => auth()->id(),
                'identity_type'   => 'dl',
                'identity_number' => $request->driving_licence_number,
                'document'        => $path_dl_front,
            ]);
        }

        if ($request->hasFile('driving_licence_back')) {
            $dl_back      = $request->file('driving_licence_back');
            $path_dl_back = $dl_back->store('driver_licence/back', 'public');

            UserDocument::create([
                'user_id'         => auth()->id(),
                'identity_type'   => 'dl',
                'identity_number' => $request->driving_licence_number,
                'document'        => $path_dl_back,
            ]);
        }

        // Handle Aadhar uploads
        if ($request->hasFile('aadhar_front')) {
            $aadhar_front      = $request->file('aadhar_front');
            $path_aadhar_front = $aadhar_front->store('driver_aadhar/front', 'public');

            UserDocument::create([
                'user_id'         => auth()->id(),
                'identity_type'   => 'aadhar',
                'identity_number' => $request->aadhar_number,
                'document'        => $path_aadhar_front,
            ]);
        }

        if ($request->hasFile('aadhar_back')) {
            $aadhar_back      = $request->file('aadhar_back');
            $path_aadhar_back = $aadhar_back->store('driver_aadhar/back', 'public');

            UserDocument::create([
                'user_id'         => auth()->id(),
                'identity_type'   => 'aadhar',
                'identity_number' => $request->aadhar_number,
                'document'        => $path_aadhar_back,
            ]);
        }

        // Handle PAN card upload
        if ($request->hasFile('pan')) {
            $pan     = $request->file('pan');
            $pathpan = $pan->store('driver_pan', 'public');

            UserDocument::create([
                'user_id'         => auth()->id(),
                'identity_type'   => 'pan',
                'identity_number' => $request->pan_number,
                'document'        => $pathpan,
            ]);
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'data'    => $user,
        ], 200);
    }

}
