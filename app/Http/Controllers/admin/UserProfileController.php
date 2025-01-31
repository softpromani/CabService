<?php
namespace App\Http\Controllers\admin;

use App\Models\City;
use App\Models\User;
use App\Models\State;
use App\Models\Country;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{
    public function userProfile($id)
    {
        $roles       = Role::all();
        $user    = User::findOrFail($id);
        $countries = Country::get();
        $states    = State::get();
        $cities    = City::get();
        $currentRole = $user->roles->first()->id ?? null;

        return view('admin.user.userProfile', compact('roles', 'user', 'currentRole','countries','states','cities'));
    }

    public function updateUserProfile(Request $request, $id)
    {
        $validated = $request->validate([
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|email|unique:users,email,' . $id,
            'phone'      => 'required',
            'gender'     => 'required',
            'password'   => 'nullable',
            'user_image' => 'nullable',

        ]);

        $user = User::findOrFail($id);

        $user->email      = $request->input('email');
        $user->phone      = $request->input('phone');
        $user->gender     = $request->input('gender');
        $user->dob        = $request->input('dob');
        $user->country_id = $request->input('country_id');
        $user->state_id   = $request->input('state_id');
        $user->city_id    = $request->input('city_id');
        $user->address    = $request->input('address');
        $user->first_name = $request->input('first_name');
        $user->last_name  = $request->input('last_name');

        if ($request->hasFile('user_image')) {
            $imageName        = $request->file('user_image')->store('userImages', 'public');
            $user->user_image = $imageName;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return redirect()->back()
            ->with('success', 'Profile updated successfully!');
    }

    public function changePassword(Request $request, $id)
    {
        $request->validate([
            'current_password'          => ['required', 'string', 'min:8'],
            'new_password'              => ['required', 'string', 'min:8', 'different:current_password'],
            'new_password_confirmation' => ['required', 'same:new_password'],
        ]);

        $driver = User::findOrFail($id);
        if (! Hash::check($request->current_password, $driver->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->with('active_tab', 'profile-change-password');
        }

        // Update the password
        $driver->password = Hash::make($request->new_password);
        $driver->save();

        return back()->with('success', 'Password changed successfully.')->with('active_tab', 'profile-change-password');
    }

}
