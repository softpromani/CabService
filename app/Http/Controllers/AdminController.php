<?php

namespace App\Http\Controllers;

use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }
    public function userList()
    {
        $users = User::get();
        return view('admin.user.userList', compact('users'));
    }
}
