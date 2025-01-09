<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function car(Request $request)
    {
        // Validate the request data
        $request->validate([
            'type_id' => 'required',
            'model_id' => 'required',
            'brand_id' => 'required',
            'color' => 'required',
            'interior' => 'required',
            'seat' => 'required',
            'registration_number' => 'required',
            'insurance_number' => 'required',
            'pollution_number' => 'required',
        ]);

        // Create a new Car entry
        $data = Car::create([
            'type_id' => $request->type_id,
            'model_id' => $request->model_id,
            'brand_id' => $request->brand_id,
            'color' => $request->color,
            'interior' => $request->interior,
            'seat' => $request->seat,
            'registration_number' => $request->registration_number,
            'insurance_number' => $request->insurance_number,
            'pollution_number' => $request->pollution_number,
        ]);

        // Return the response
        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }


    public function route(Request $request)
    {
        // dd($request->all());
        // Validate the request data
        $request->validate([
            'start_point' => 'required',
            'destination_point' => 'required',
            'pickup_point' => 'required',

        ]);
       
        // Create a new Car entry
        $data = Car::create([
            'start_point' => $request->start_point,
            'destination_point' => $request->destination_point,
            'pickup_point' => $request->pickup_point,

        ]);

        // Return the response
        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }



}
