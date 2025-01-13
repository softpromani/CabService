<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Profile;
use App\Models\UserDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Nette\Schema\Helpers;

class ProfileController extends Controller
{
    /**
     * Summary of profile_update
     * @param \Illuminate\Http\Request $request
     * @return void
     * @header Bearer
     */
    public function profile_update(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'gender' => 'required|in:male,female',
            'user_image' => 'required|image|max:2024',
            'driving_licence_number'=>'required|regex:/^[A-Z]{2}[- ]?[0-9]{2}[- ]?[0-9]{4}[- ]?[0-9]{7}$/',
            'driving_licence'=>'required|image|max:2024',
            'aadhar_number'=>'required|digits:12',
            'aadhar'=>'required|image|max:2024',
            'pan_number'=>'sometimes|digits:12',
            'pan'=>'required_if:pan_number,!=,null|image|max:2024',
            'dob'=>'required|date',
        ]);
        $user = Auth::user();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->gender = $request->gender;
        $user->dob=$validated['dob'];
        $user->is_profile = 1;

        // Handle user image upload
        if ($request->hasFile('user_image')) {
            $image = $request->file('user_image');
            $path = $image->store('user_images', 'public');
            $user->user_image = $path;
        }
        $user->save();
        if($request->hasFile('driving_licence')){
            $dl=$request->file('driving_licence');
            $pathdl = $dl->store('driver_licence','public');
            UserDocument::create(['user_id'=>auth()->id(),'identity_type'=>'dl','identity_number'=>$request->driving_licence_number,'document'=>$pathdl]);
        }
        if($request->hasFile('aadhar')){
            $aadhar=$request->file('aadhar');
            $pathaadhar = $aadhar->store('driver_aadhar','public');
            UserDocument::create(['user_id'=>auth()->id(),'identity_type'=>'dl','identity_number'=>$request->driving_licence_number,'document'=>$pathaadhar]);
        }
        if($request->hasFile('pan')){
            $pan=$request->file('pan');
            $pathpan = $pan->store('driver_pan','public');
            UserDocument::create(['user_id'=>auth()->id(),'identity_type'=>'dl','identity_number'=>$request->driving_licence_number,'document'=>$pathpan]);
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $user,
        ], 200);
    }


}
