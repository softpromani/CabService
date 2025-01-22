<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    function businessPages()
    {
        return view('admin.setting.business-pages');
    }

    function thirdPartyApi($config=null)
    {
        $data = getBusinessSetting($config);
        $page = $config;
        return view('admin.setting.third-party-api', compact('data','page'));
    }

    function thirdPartyApiPost(Request $request)
    {
        $type = $request->type;

        // Remove _token from request data
        $value = json_encode($request->except('_token'));

        // Update or create the setting
        $data = updateBusinessSetting($type, $value);

        if ($data === true) {
            sweetalert()->success('Data updated successfully');
        } else {
            sweetalert()->error('Something went wrong!');
        }

        return redirect()->back();
    }


    function socialMediaLinks()
    {
        $blogcategories = BlogCategory::get();
        return view('admin.setting.social-media-links', compact('blogcategories'));
    }
}
