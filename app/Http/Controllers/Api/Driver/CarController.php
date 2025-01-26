<?php
namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function car(Request $request)
    {
        // Validate the request data
        $request->validate([
            'model_id'            => 'required',
            'brand_id'            => 'required',
            'color'               => 'required',
            'interior'            => 'required',
            'seat'                => 'required',
            'registration_number' => 'required',
            'insurance_number'    => 'required',
            'pollution_number'    => 'required',
        ]);

        // Create a new Car entry
        $data = Car::create([
            'driver_id'           => auth()->id(),
            'model_id'            => $request->model_id,
            'brand_id'            => $request->brand_id,
            'color'               => $request->color,
            'interior'            => $request->interior,
            'seat'                => $request->seat,
            'registration_number' => $request->registration_number,
            'insurance_number'    => $request->insurance_number,
            'pollution_number'    => $request->pollution_number,
        ]);

        // Return the response
        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }

    public function brand()
    {
        $brand = Brand::paginate(10);
        return response()->json([
            'status' => 'success',
            'data'   => $brand,
        ]);
    }
    public function model(Request $req)
    {
        $model = Brand::findOrFail($req->brand_id)?->carModels;

        return response()->json([
            'status' => 'success',
            'data'   => $model,
        ]);
    }
}
