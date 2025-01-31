<?php
namespace App\Http\Controllers\admin;

use App\Models\Car;
use App\Models\City;
use App\Models\User;
use App\Models\Brand;
use App\Models\State;
use App\Models\Country;
use App\Models\CarModel;
use App\Models\UserDocument;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                ['title' => 'ID', 'field' => 'index'], // Change 'id' to 'index' for numbering
                ['title' => 'Name', 'field' => 'full_name', 'headerFilter' => "input"],
                ['title' => 'Email', 'field' => 'email', 'headerFilter' => "input"],
                ['title' => 'Phone', 'field' => 'phone', 'headerFilter' => "input"],
                ['title' => 'Active', 'field' => 'suspend_status', 'formatter' => "html"],
                ['title' => 'Verify', 'field' => 'verify_status', 'formatter' => "html"],
                ['title' => 'Action', 'field' => 'delete_action', 'formatter' => 'html'],
            ];

            // Query drivers
            $query = User::role('Driver');

            // Pagination and sorting
            $page      = $request->query('page', 1);
            $perPage   = $request->query('size', 10);
            $sortField = $request->query('sort.0.field', 'id');
            $sortOrder = $request->query('sort.0.dir', 'asc');

            if ($sortField && $sortOrder) {
                $query->orderBy($sortField, $sortOrder);
            }

            $drivers = $query->paginate($perPage, ['*'], 'page', $page);

            $startIndex = ($page - 1) * $perPage + 1;

            $drivers->getCollection()->transform(function ($item) use (&$startIndex) {
                $item->index = $startIndex++; 

                $item->delete_action = '
                    <i class="fa-solid fa-trash text-danger delete_alert" data-id="' . $item->id . '" data-alert_message="Are you sure want to delete Driver?" data-alert_title="Delete"
                    data-alert_type="warning" data-alert_url="' . route('admin.driver.destroy', $item->id) . '"></i>&nbsp;&nbsp;
                    <a href="' . route('admin.driver.edit', $item->id) . '" class="text-black"><i class="fa-solid fa-pen-to-square text-warning"></i></a>&nbsp;&nbsp;
                    <a href="' . route('admin.driver.profile', $item->id) . '" class="text-primary"><i class="fa-solid fa-eye"></i></a>&nbsp;&nbsp;
                    <a href="' . route('admin.driver.cars', $item->id) . '" class="text-success"><i class="fa-solid fa-car"></i></a>
                ';

                $item->image = $item->user_image
                    ? '<img src="' . Storage::url($item->user_image) . '" alt="Driver Image" style="height: 40px;">'
                    : '<span class="text-muted">No Image</span>';

                $item->suspend_status = '<div class="form-check form-switch">
                    <input class="form-check-input confirmation_alert" type="checkbox" id="flexSwitchCheckChecked" ';
                $item->suspend_status .= $item->is_active ? 'checked=""' : '';
                $item->suspend_status .= 'data-id="' . $item->id . '" data-alert_message="want to change suspend status?"
                    data-alert_title="Are you sure" data-alert_type="warning" data-alert_url="' . route('admin.user.status.update') . '"
                    data-status_field="is_active">
                    </div>';
                $item->verify_status = '<div class="form-check form-switch">
                    <input class="form-check-input confirmation_alert" type="checkbox" id="flexSwitchCheckChecked" ';
                $item->verify_status .= $item->is_verify ? 'checked=""' : '';
                $item->verify_status .= 'data-id="' . $item->id . '" data-alert_message="want to change verification status?"
                    data-alert_title="Are you sure" data-alert_type="warning" data-alert_url="' . route('admin.user.status.update') . '"
                    data-status_field="is_veify">
                    </div>';
                return $item;
            });

            // Return response
            return response()->json([
                'columns'   => $columns,
                'last_page' => $drivers->lastPage(),
                'data'      => $drivers->items(),
                'total'     => $drivers->total(),
            ]);
        }

        // For non-AJAX requests, render the view
        $drivers = User::role('Driver')->where('is_profile', 1)->get();
        return view('admin.user.driver', compact('drivers'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Handle form rendering for creating a new driver (optional)
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Add logic to store new driver (optional)
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Add logic to show a specific driver (optional)
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $roles       = Role::all();
        $editUser    = User::findOrFail($id);
        $drivers     = User::all();
        $currentRole = $editUser->roles->first()->id ?? null;
        $countries   = Country::get();
        $states      = State::get();
        $cities      = City::get();
    
        $documents    = $editUser->documents; 
        
        
        return view('admin.driver.editDriver', compact('editUser', 'drivers', 'roles', 'currentRole', 'countries', 'states', 'cities', 'documents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateDriver(Request $request, string $id)
    {
        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required',
            'gender' => 'required',
            'password' => 'nullable',
            'user_image' => 'nullable',
            
        ]);

        $user = User::find($id);
        if (! $user) {
            toastr()->error('User not found');
            return redirect()->route('admin.driver.index');
        }
        $user->gender = $request->input( 'gender' );
        $user->dob = $request->input( 'dob' );
        $user->country_id = $request->input( 'country_id' );
        $user->state_id = $request->input( 'state_id' );
        $user->city_id = $request->input( 'city_id' );
        $user->address = $request->input( 'address' );

        if ( $request->hasFile( 'user_image' ) ) {
            $imageName = $request->file( 'user_image' )->store( 'userImages', 'public' );
            $user->user_image = $imageName;
        }
        if ( $request->filled( 'password' ) ) {
            $validated[ 'password' ] = Hash::make( $request->input( 'password' ) );
        } else {
            unset( $validated[ 'password' ] );
        }

        $user->update($validated);
        $role_name = Role::find( $request->roleid );
        if ( $role_name ) {
            $user->syncRoles( [ $role_name->name ] );
        }
        $role = $user->getRoleNames()->first();

        $documentTypes = ['driving_licence' => 'dl', 'adhar_card' => 'aadhar', 'pan_card' => 'pan'];

        foreach ($documentTypes as $type => $prefix) {
            $identityNumber = $request->input("{$prefix}_number");
            $documentFront = $request->file("{$prefix}_front") ? $request->file("{$prefix}_front")->store('userDocuments', 'public') : null;
            $documentBack = $request->file("{$prefix}_back") ? $request->file("{$prefix}_back")->store('userDocuments', 'public') : null;

            if ($identityNumber) {
                UserDocument::updateOrCreate(
                    ['user_id' => $user->id, 'identity_type' => $type],
                    [
                        'identity_number' => $identityNumber,
                        'document'        => $documentFront,
                        'document_back'   => $documentBack,
                    ]
                );
            }
        }

        toastr()->success('User and documents updated successfully');
        return redirect()->route('admin.driver.index');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $drivers = User::find($id);

        if ($drivers) {
            $drivers->delete();
            return response()->json(['success' => true, 'message' => 'Driver deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Driver not found.'], 404);
    }
    public function profile($id)
    {
        $countries = Country::get();
        $states    = State::get();
        $cities    = City::get();
        $user    = User::role('Driver')->with('documents')->findOrFail($id);
        return view('admin.user.driverProfile', compact('user', 'countries', 'states', 'cities'));
    }

    public function cars(Request $request, $id)
    {
        $driver = User::role('Driver')->findOrFail($id);

        $query = Car::with(['brand', 'model'])->where('driver_id', $driver->id);

        if ($request->ajax()) {
            $columns = [
                ['title' => 'ID', 'field' => 'id'],
                ['title' => 'Brand', 'field' => 'brand_name', 'headerFilter' => "input"], 
                ['title' => 'Model', 'field' => 'model_name', 'headerFilter' => "input"], 
                ['title' => 'Interior', 'field' => 'interior', 'headerFilter' => "input"],
                ['title' => 'Color', 'field' => 'color', 'headerFilter' => "input"],
                ['title' => 'Registration Number', 'field' => 'registration_number', 'headerFilter' => "input"],
                ['title' => 'Actions', 'field' => 'actions', 'formatter' => "html"], // Add an Actions column
            ];

            // Pagination and sorting
            $page      = $request->query('page', 1);
            $perPage   = $request->query('size', 10);
            $sortField = $request->query('sort.0.field', 'id');
            $sortOrder = $request->query('sort.0.dir', 'asc');

            if ($sortField && $sortOrder) {
                $query->orderBy($sortField, $sortOrder);
            }

            $paginatedCars = $query->paginate($perPage, ['*'], 'page', $page);

            $paginatedCars->getCollection()->transform(function ($item) {
                $item->brand_name = $item->brand->brand_name ?? 'N/A'; 
                $item->model_name = $item->model->model_name ?? 'N/A'; 

                // Add actions with View and Edit buttons
                $viewUrl = route('admin.cars.viewCars', $item->id); // Adjust route name as needed
                $editUrl = route('admin.cars.editcar', $item->id); // Adjust route name as needed
                $item->actions = '
                    <a href="' . $viewUrl . '" class="btn btn-sm ">
                        <i class="fa-solid fa-eye text-primary"></i> 
                    </a>
                    <a href="' . $editUrl . '" class="btn btn-sm ">
                        <i class="fa-solid fa-pen-to-square text-warning"></i> 
                    </a>
                ';
                return $item;
            });

            return response()->json([
                'columns'   => $columns,
                'last_page' => $paginatedCars->lastPage(),
                'data'      => $paginatedCars->items(),
                'total'     => $paginatedCars->total(),
            ]);
        }

        $cars = $query->get();
        return view('admin.driver.car', compact('driver', 'cars'));
    }


    public function viewCars($id)
    {
        $car = Car::with(['brand', 'model'])->findOrFail($id);
        return view('admin.driver.car.viewCar', compact( 'car'));
    }
    public function editcar($id)
    {
        $brands = Brand::get();
        $carModels = CarModel::get();
        $car = Car::findOrFail($id);
        return view('admin.driver.car.editCar', compact('car','brands','carModels')); 
    }
    public function updateCar(Request $request, $id)
    {
        $request->validate([
            'model_id'            => 'required',
            'brand_id'            => 'required',
            'color'               => 'nullable',
            'interior'            => 'nullable',
            'seat'                => 'nullable|',
            'registration_number' => 'required|string|max:255|unique:cars,registration_number,' . $id,
            'insurance_number'    => 'nullable|string|max:255',
            'pollution_number'    => 'nullable|string|max:255',
            'rc_number'           => 'nullable|string|max:255',
            'car_images.*'        => 'nullable', 
            'rc_document'         => 'nullable', 
        ]);

        $car = Car::findOrFail($id);

        $car->update([
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
            foreach ($request->file('car_images') as $image) {
                $path = $image->store('car_images', 'public'); 
                $carImages[] = $path;
            }
            $car->car_images = json_encode($carImages); 
        }

        if ($request->hasFile('rc_document')) {
            if ($car->rc_document) {
                Storage::disk('public')->delete($car->rc_document);
            }

            $rcPath = $request->file('rc_document')->store('rc_documents', 'public');
            $car->rc_document = $rcPath;
        }

        $car->save();

        return redirect()->back()->with('success', 'Car updated successfully!');
    }
    public function getModels($brandId)
    {
        $models = CarModel::where('brand_id', $brandId)->get();

        return response()->json([
            'models' => $models
        ]);
    }
    public function deleteImage($id, $image)
    {
        $car = Car::findOrFail($id);
        $images = json_decode($car->car_images, true);  

        $images = array_filter($images, fn($img) => $img !== 'car_images/' . $image);

        $car->car_images = json_encode($images);  
        $car->save();

        Storage::delete('public/car_images/' . $image);

        return back()->with('success', 'Image deleted successfully.');
    }




}
