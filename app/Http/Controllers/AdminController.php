<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;

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

    public function business_setting()
    {
        $business = Business::get();
        return view('admin.business', compact( 'business'));
    }


    public function business_update(Request $request)
    {
        // dd($request->all());
        // Validate incoming request
        $request->validate([
            'app_logo' => 'nullable|file|mimes:jpeg,png,jpg,gif',
            'splash_screen' => 'nullable|file|mimes:jpeg,png,jpg,gif',
            'primary_color' => 'nullable|string|max:7', // Hex color code
            'secondary_color' => 'nullable|string|max:7',
            'text_color' => 'nullable|string|max:7',
            'google_map_api' => 'nullable|string',
            'web_logo' => 'nullable|file|mimes:jpeg,png,jpg,gif',
        ]);
        if($request->hasFile('app_logo')){
            $file = $request->file('app_logo');
            $path = $file->store('business','public');
            Business::updateOrCreate(['key' => 'app_logo'],['value'=>$path,'type'=>'file','device_type'=>'app']);
        }
        if($request->hasFile('splash_screen')){
            $file = $request->file('splash_screen');
            $path = $file->store('business','public');
            Business::updateOrCreate(['key' => 'splash_screen'],['value'=>$path,'type'=>'file','device_type'=>'app']);
        }
        if($request->primary_color){
            Business::updateOrCreate(['key'=>'primary_color'],['value'=>$request->primary_color,'type'=>'string','device_type'=>'app']);
        }
        if($request->secondary_color){
            Business::updateOrCreate(['key'=>'secondary_color'],['value'=>$request->secondary_color,'type'=>'string','device_type'=>'app']);
        }
        if($request->text_color){
            Business::updateOrCreate(['key'=>'text_color'],['value'=>$request->text_color,'type'=>'string','device_type'=>'app']);
        }
        if($request->google_map_api){
            Business::updateOrCreate(['key'=>'google_map_api'],['value'=>$request->google_map_api,'type'=>'string','device_type'=>'app']);
        }
        if($request->hasFile('web_logo')){
            $file = $request->file('web_logo');
            $path = $file->store('business','public');
            Business::updateOrCreate(['key' => 'web_logo'],['value'=>$path,'type'=>'file','device_type'=>'web']);
        }

        return redirect()->route('admin.business')->with('success', 'Business settings updated successfully!');
    }

}
