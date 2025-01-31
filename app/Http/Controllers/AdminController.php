<?php
namespace App\Http\Controllers;

use App\Models\City;
use App\Models\User;
use App\Models\State;
use App\Models\Country;
use App\Models\Business;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller {
    public function index() {
        return view( 'admin.dashboard' );
    }

    public function userList(Request $request) {
        if ($request->ajax()) {
            $columns = [
                ['title' => 'ID', 'field' => 'user_id'],
                ['title' => 'Name', 'field' => 'full_name', 'headerFilter' => 'input'],
                ['title' => 'Email', 'field' => 'email', 'headerFilter' => 'input'],
                ['title' => 'Contact', 'field' => 'phone', 'headerFilter' => 'input'],
                ['title' => 'Role', 'field' => 'role_name', 'headerFilter' => 'input'],
                ['title' => 'Active', 'field' => 'suspend_status', 'formatter' => "html"],
                ['title' => 'Verify', 'field' => 'verify_status', 'formatter' => "html"],
                ['title' => 'Action', 'field' => 'delete_action', 'formatter' => 'html']
            ];
    
            $page      = $request->query('page', 1);
            $perPage   = $request->query('size', 10);
            $sortField = $request->query('sort.0.field', 'id');
            $sortOrder = $request->query('sort.0.dir', 'asc');
    
            $query = User::whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', ['Driver', 'User']);
            });
    
            if ($sortField && $sortOrder) {
                $query->orderBy($sortField, $sortOrder);
            }
           
    
            $users = $query->paginate($perPage, ['*'], 'page', $page);
            $serialNumber = ($page - 1) * $perPage + 1;
    
            $users->getCollection()->transform(function ($item)use (&$serialNumber) {
                $item->user_id = $serialNumber++; 
                $item->delete_action = '
                    <i class="fa-solid fa-trash text-danger delete_alert" data-id="' . $item->id . '" data-alert_message="Are you sure want to delete this user?" data-alert_title="Delete"
                    data-alert_type="warning" data-alert_url="' . route('admin.customer.destroy', $item->id) . '"></i>&nbsp;&nbsp;
                    <a href="' . route('admin.editUser', $item->id) . '" class="text-black"><i class="fa-solid fa-pen-to-square text-warning"></i></a>&nbsp;&nbsp;
                    <a href="' . route('admin.userProfile', $item->id) . '" class="text-primary"><i class="fa-solid fa-eye"></i></a>
                ';
    
                $item->suspend_status = '<div class="form-check form-switch">
                      <input class="form-check-input confirmation_alert" type="checkbox" id="flexSwitchCheckChecked" ';
                $item->suspend_status .= $item->is_active ? 'checked=""' : '';
                $item->suspend_status .= 'data-id="' . $item->id . '" data-alert_message="Want to change suspend status?"
                      data-alert_title="Are you sure" data-alert_type="warning" data-alert_url="' . route('admin.user.status.update') . '"
                      data-status_field="is_active">
                    </div>';
    
                $item->verify_status = '<div class="form-check form-switch">
                    <input class="form-check-input confirmation_alert" type="checkbox" id="flexSwitchCheckChecked" ';
                $item->verify_status .= $item->is_verify ? 'checked=""' : '';
                $item->verify_status .= 'data-id="' . $item->id . '" data-alert_message="Want to change verification status?"
                      data-alert_title="Are you sure" data-alert_type="warning" data-alert_url="' . route('admin.user.status.update') . '"
                      data-status_field="is_verify">
                    </div>';
    
                return $item;
            });
    
            return response()->json([
                'columns'   => $columns,
                'last_page' => $users->lastPage(),
                'data'      => $users->items(),
                'total'     => $users->total(),
            ]);
        }
    
        $users = User::whereDoesntHave('roles', function ($q) {
            $q->whereIn('name', ['Driver', 'User']);
        })->get();
    
        return view('admin.user.userList', compact('users'));
    }
    

    public function addUser() {
        $roles = Role::all();
        $countries = Country::get();
        $states = State::get();
        $cities = City::get();
        return view( 'admin.user.add-user', compact( 'roles' ,'countries','cities','states') );
    }

    public function editUser( string $id ) {
        $roles       = Role::all();
        $editUser    = User::findOrFail( $id );
        $currentRole = $editUser->roles->first()->id ?? null;
        $countries = Country::get();
        $states    = State::get();
        $cities    = City::get();
        return view('admin.user.add-user', compact( 'roles', 'editUser', 'currentRole' ,'countries','states','cities') );
    }

    public function updateUser( Request $request, string $id ) {
        // Validation
        $validated = $request->validate( [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|string',
            'gender' => 'required',
            'password' => 'nullable|string|min:6',
            'user_image' => 'nullable|image',
            
            'roleid' => 'required|exists:roles,id',
        ] );

        $user = User::find( $id );

        if ( !$user ) {
            toast( 'User not found', 'error' );
            return redirect()->route( 'admin.user.index' );
        }

        $user->gender = $request->input( 'gender' );
        $user->dob = $request->input( 'dob' ) ?? NULL;
        $user->country_id = $request->input( 'country_id' );
        $user->state_id = $request->input( 'state_id' );
        $user->city_id = $request->input( 'city_id' );
        $user->address = $request->input( 'address' );

        // Handle image upload
        if ( $request->hasFile( 'user_image' ) ) {
            $imageName = $request->file( 'user_image' )->store( 'userImages', 'public' );
            $user->user_image = $imageName;
        }
        if ( $request->filled( 'password' ) ) {
            $validated[ 'password' ] = Hash::make( $request->input( 'password' ) );
        } else {
            unset( $validated[ 'password' ] );
        }

        $user->update( $validated );

        $role_name = Role::find( $request->roleid );
        if ( $role_name ) {
            $user->syncRoles( [ $role_name->name ] );
        }
        $role = $user->getRoleNames()->first();
        switch ( $role ) {
            case 'driver':
            toastr()->success( 'Driver updated successfully' );

            return redirect()->route( 'admin.driver.index' );

            case 'user':
            toastr()->success( 'User updated successfully' );

            return redirect()->route( 'admin.customer.index' );

            case 'admin':
            toastr()->success( 'Admin updated successfully' );
            return redirect()->route( 'admin.userList' );

            default:
            toastr()->success( 'User created successfully' );

            return redirect()->route( 'admin.customer.index' );
        }
    }

    public function storeUser( Request $request ) {
        $validated = $request->validate( [

            'first_name'  => 'required',
            'last_name'  => 'required',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'required',
            'gender'     => 'required',
            'password'   => 'nullable',
            'user_image' => 'required',
            'roleid'     => 'required',
        ] );

        // Process the image upload if provided
        if ( $request->hasFile( 'user_image' ) ) {
            $validated[ 'user_image' ] = $request->file( 'user_image' )->store( 'userImages', 'public' );
        }

        if ( ! empty( $validated[ 'password' ] ) ) {
            $validated[ 'password' ] = Hash::make( $validated[ 'password' ] );
        }

        $user = User::create( [

            'first_name'  => $validated[ 'first_name' ],
            'last_name'  => $validated[ 'last_name' ],
            'email'      => $validated[ 'email' ],
            'phone'      => $validated[ 'phone' ],
            'gender'     => $validated[ 'gender' ] ?? null,
            'password'   => $validated[ 'password' ] ?? null,
            'user_image' => $validated[ 'user_image' ] ?? null,
            'roleid'     => $validated[ 'roleid' ] ?? null,
            'dob'     => $request->input( 'dob' ) ?? null,
            'country_id'     => $request->input( 'country_id'),
            'state_id'     => $request->input( 'state_id' ),
            'city_id'     => $request->input( 'city_id' ),
            'address'     => $request->input( 'address' ),
            
        ] );

        if ( ! $user ) {
            toastr()->error( 'User creation failed' );
            return redirect()->route( 'admin.user.index' );
        }

        if ( ! empty( $validated[ 'roleid' ] ) ) {
            $role = Role::find( $validated[ 'roleid' ] );
            if ( $role ) {
                $user->syncRoles( $role );
            }
        }

        toastr()->success( 'User created successfully' );
        return redirect()->route( 'admin.userList' );
    }

    public function business_setting() {
        $business = Business::get();
        return view( 'admin.business', compact( 'business' ) );
    }

    public function business_update( Request $request ) {
        // dd( $request->all() );
        // Validate incoming request
        $request->validate( [
            'app_logo'        => 'nullable|file|mimes:jpeg,png,jpg,gif',
            'splash_screen'   => 'nullable|file|mimes:jpeg,png,jpg,gif',
            'primary_color'   => 'nullable|string|max:7', // Hex color code
            'secondary_color' => 'nullable|string|max:7',
            'text_color'      => 'nullable|string|max:7',
            'google_map_api'  => 'nullable|string',
            'web_logo'        => 'nullable|file|mimes:jpeg,png,jpg,gif',
        ] );
        if ( $request->hasFile( 'app_logo' ) ) {
            $file = $request->file( 'app_logo' );
            $path = $file->store( 'business', 'public' );
            Business::updateOrCreate( [ 'key' => 'app_logo' ], [ 'value' => $path, 'type' => 'file', 'device_type' => 'app' ] );
        }
        if ( $request->hasFile( 'splash_screen' ) ) {
            $file = $request->file( 'splash_screen' );
            $path = $file->store( 'business', 'public' );
            Business::updateOrCreate( [ 'key' => 'splash_screen' ], [ 'value' => $path, 'type' => 'file', 'device_type' => 'app' ] );
        }
        if ( $request->primary_color ) {
            Business::updateOrCreate( [ 'key' => 'primary_color' ], [ 'value' => $request->primary_color, 'type' => 'string', 'device_type' => 'app' ] );
        }
        if ( $request->secondary_color ) {
            Business::updateOrCreate( [ 'key' => 'secondary_color' ], [ 'value' => $request->secondary_color, 'type' => 'string', 'device_type' => 'app' ] );
        }
        if ( $request->text_color ) {
            Business::updateOrCreate( [ 'key' => 'text_color' ], [ 'value' => $request->text_color, 'type' => 'string', 'device_type' => 'app' ] );
        }
        if ( $request->google_map_api ) {
            Business::updateOrCreate( [ 'key' => 'google_map_api' ], [ 'value' => $request->google_map_api, 'type' => 'string', 'device_type' => 'app' ] );
        }
        if ( $request->hasFile( 'web_logo' ) ) {
            $file = $request->file( 'web_logo' );
            $path = $file->store( 'business', 'public' );
            Business::updateOrCreate( [ 'key' => 'web_logo' ], [ 'value' => $path, 'type' => 'file', 'device_type' => 'web' ] );
        }

        return redirect()->route( 'admin.business' )->with( 'success', 'Business settings updated successfully!' );
    }
    public function getStates($countryId)
    {
        $states = State::where('country_id', $countryId)->get();
        
        return response()->json($states);
    }

    public function getCities($stateId)
    {
        $cities = City::where('state_id', $stateId)->get();
        return response()->json($cities);
    }

}
