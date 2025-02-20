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
        $request->validate([
            'model_id'            => 'required',
            'brand_id'            => 'required',
            'color'               => 'nullable',
            'interior'            => 'nullable',
            'seat'                => 'nullable|integer',
            'registration_number' => 'required|string|max:255|unique:cars,registration_number',
            'insurance_number'    => 'nullable|string|max:255',
            'pollution_number'    => 'nullable|string|max:255',
            'rc_number'           => 'required|string|max:255|unique:cars,rc_number',
            'car_images.*'        => 'nullable|image',
            'rc_document'         => 'sometimes|image',
        ]);

        $car = Car::create([
            'driver_id'           => auth()->id(),
            'model_id'            => $request->model_id,
            'brand_id'            => $request->brand_id,
            'color'               => $request->color,
            'interior'            => $request->interior,
            'seat'                => $request->seat,
            'registration_number' => $request->registration_number,
            'insurance_number'    => $request->insurance_number,
            'pollution_number'    => $request->pollution_number,
            'rc_number'           => $request->rc_number,
        ]);

        if ($request->hasFile('car_images')) {
            $carImages = [];
            foreach ($request->file('car_images') as $cimage) {
                $path        = $cimage->store('car_images', 'public');
                $carImages[] = $path;
            }
            $car->car_images = json_encode($carImages);
        }

        if ($request->hasFile('rc_document')) {
            $rcPath           = $request->file('rc_document')->store('rc_documents', 'public');
            $car->rc_document = $rcPath;
        }

        $car->save();

        return response()->json([
            'message' => 'Car added successfully!',
            'data'    => $car,
        ], 201);
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
    public function updateCar(Request $request, $carId)
    {
        $car = Car::findOrFail($carId);

        $request->validate([
            'model_id'            => 'required',
            'brand_id'            => 'required',
            'color'               => 'nullable',
            'interior'            => 'nullable',
            'seat'                => 'nullable|integer',
            'registration_number' => 'required|unique:cars,registration_number,' . $car->id,
            'insurance_number'    => 'nullable',
            'pollution_number'    => 'nullable',
            'rc_number'           => 'required',
            'car_images.*'        => 'nullable|image|max:1024',
            'rc_document'         => 'nullable',
        ]);

        $car->model_id            = $request->model_id;
        $car->brand_id            = $request->brand_id;
        $car->color               = $request->color;
        $car->interior            = $request->interior;
        $car->seat                = $request->seat;
        $car->registration_number = $request->registration_number;
        $car->insurance_number    = $request->insurance_number;
        $car->pollution_number    = $request->pollution_number;
        $car->rc_number           = $request->rc_number;

        if ($request->hasFile('car_images')) {
            $carImages = [];
            foreach ($request->file('car_images') as $cimage) {
                $path        = $cimage->store('car_images', 'public');
                $carImages[] = $path;
            }
            $car->car_images = json_encode($carImages);
        }

        if ($request->hasFile('rc_document')) {
            $rcPath           = $request->file('rc_document')->store('rc_documents', 'public');
            $car->rc_document = $rcPath;
        }

        $car->save();

        return response()->json([
            'message' => 'Car updated successfully!',
            'data'    => $car,
        ], 200);
    }
    public function carView()
    {
        $car = Car::where('driver_id', auth()->id())->get();

        if (! $car) {
            return response()->json([
                'message' => 'No car found for this driver.',
            ], 404);
        }

        return response()->json([
            'message' => 'Car details retrieved successfully.',
            'data'    => $car,
        ], 200);
    }

}
