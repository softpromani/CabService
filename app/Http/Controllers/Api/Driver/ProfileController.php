<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Profile;
use App\Models\UserDocument;
use Illuminate\Http\Request;
use Nette\Schema\Helpers;

class ProfileController extends Controller
{
    public function profile_update(Request $request)
    {

        // dd($request->all());
        // return $request->all();
        $request->validate([
            'user_id' => 'required',
            'identity_type' => 'required',
            'identity_number' => 'required',
            'document' => 'required',
            ]);

        $path=null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $path = $file->store('driver_document', 'public');
        }

       $data= UserDocument::create([
            'user_id'=>$request->user_id,
            'identity_type'=>$request->identity_type,
            'identity_number'=>$request->identity_number,
            'document'=>$path,
        ]);

        return response([
            'status' => true,
            'data' => $data,
        ]);
    }


}
