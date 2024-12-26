<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login()
    {
        return view('admin.auth.login');
    }
    public function register()
    {
        return view('admin.auth.register');
    }
    public function adminStore(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'password' => 'required',

        ]);
        $data = $request->user_image;
        if ($request->hasFile('user_image')) {

            $filePath = $data->store('user_images', 'public');
        }
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_image' => $filePath ?? '',

        ]);
        // Media::upload_media($user, $data, 'user_image');

        if ($user) {
            toastr()->success('You have logged in successfully!');
            return redirect()->route('login');
        } else {
            toastr()->error('Something went wrong!');
            return redirect()->route('register');
        }
    }
    public function loginStore(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember_me)) {

            $user = Auth::user();

            if ($user->hasRole(['Admin', 'Super Admin'])) {
                toastr()->success('Welcome ' . $user->first_name . ' ' . $user->last_name);
                return redirect()->route('admin.dashboard');
            } else {
                Auth::logout();
                toastr()->error('You do not have the required role to access this area.');
                return redirect()->route('login');
            }
        } else {
            toastr()->error('Invalid Username or Password!');
            return redirect()->route('login');
        }
    }

    public function logout()
    {
        Auth::logout();
        Session::flush();
        return redirect()->route('login');
    }
}
