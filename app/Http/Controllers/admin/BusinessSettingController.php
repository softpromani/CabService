<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BusinessSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $countries = Country::get();
        return view('admin.setting.business-setting', compact('countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Remove the `_token` key from the request data
        $data = $request->except('_token', 'website_header_logo', 'website_footer_logo', 'website_favicon');

        // Process regular settings
        foreach ($data as $type => $value) {
            $updated = updateBusinessSetting($type, $value);

            if (! $updated) {
                Log::error("Failed to update business setting for type: $type");
            }
        }

        // Handle file uploads for website_header_logo, footer_logo, and favicon
        $fileFields = ['website_header_logo', 'website_footer_logo', 'website_favicon'];
        foreach ($fileFields as $fileField) {
            if ($request->hasFile($fileField)) {
                $file     = $request->file($fileField);
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('uploads/settings', $fileName, 'public');

                // Update the file path in the business settings
                $updated = updateBusinessSetting($fileField, $filePath);

                if (! $updated) {
                    Log::error("Failed to update business setting for file: $fileField");
                }
            }
        }

        // Success message
        sweetalert()->success('Business settings updated successfully.');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
