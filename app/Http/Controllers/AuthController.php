<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login()
    {
        return view('admin.auth.login');
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
