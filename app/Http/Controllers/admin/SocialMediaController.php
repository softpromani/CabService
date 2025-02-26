<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\SocialMedia;
use Illuminate\Http\Request;

class SocialMediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $socialmedias = SocialMedia::get();
        return view('admin.Setting.social-media-links', compact('socialmedias'));
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
        $data = [
            //Database column_name => Form field name
            'name' => $request->name,
            'link' => $request->link,
        ];

        $socialmedia = SocialMedia::create($data);
        return redirect()->route('admin.setting.socialmedia.index');
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
    public function edit(SocialMedia $socialmedia)
    {
        $editsocialmedia = $socialmedia;
        // dd(vars: $editsocialmedia);

        $socialmedias = SocialMedia::get();
        return view('admin.setting.social-media-links', compact('socialmedias', 'editsocialmedia'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SocialMedia $socialmedia)
    {
        // dd($request->all());
        $socialmedia = SocialMedia::find($request->id);

        if ($socialmedia) {
            $socialmedia->status = $request->status;
            $socialmedia->save();

            return response()->json([
                'success' => true,
                'message' => 'Social Media status updated successfully!',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Social Media not found!',
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SocialMedia $socialmedia)
    {
        $socialmedia->delete();
        return redirect()->route('admin.setting.socialmedia.index');
    }
}
