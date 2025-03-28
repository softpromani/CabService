<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ThirtPartApiRequest;

class SettingController extends Controller
{
    public function businessPages()
    {
        return view('admin.setting.business-pages');
    }

    public function thirdPartyApi($config = null)
    {
        $data = getBusinessSetting($config);
        $page = $config;
        return view('admin.Setting.third-party-api', compact('data', 'page'));
    }

    public function thirdPartyApiPost(ThirtPartApiRequest $request)
    {
        // dd($request->all());
        $data = $request->validated();
        updateBusinessSetting($request->type, json_encode($data));
        toastr()->success('Data updated successfully');
        return redirect()->back();
    }

    public function socialMediaLinks()
    {
        $blogcategories = BlogCategory::get();
        return view('admin.setting.social-media-links', compact('blogcategories'));
    }
}
