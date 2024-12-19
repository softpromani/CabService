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
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->first_name,
            'email' => $request->first_name,
            'password' => $request->first_name,
        ]);
        if ($user) {
            toast('You have logged in successfully!', 'success');
            return redirect()->route('login');
        } else {
            toast('Something went wrong!', 'error');
            return redirect()->route('register');
        }
    }
    public function loginStore(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember_me)) {
            $user = Auth::user()->first_name . '' . Auth::user()->last_name;
            toast('Welcome '  . $user, 'success');
            return redirect()->route('admin.dashboard');
        } else {
            toast('Invalid Username or Password!', 'error');
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
