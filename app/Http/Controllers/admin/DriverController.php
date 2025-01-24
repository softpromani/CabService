<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Handle AJAX request
        if ($request->ajax()) {
            $columns = [
                ['title' => 'ID', 'field' => 'id'],
                ['title' => 'Driver Image', 'field' => 'image', 'formatter' => 'html'],
                ['title' => 'Name', 'field' => 'full_name', 'headerFilter' => "input"],
                ['title' => 'Email', 'field' => 'email', 'headerFilter' => "input"],
                ['title' => 'Phone', 'field' => 'phone', 'headerFilter' => "input"],
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

            $drivers->getCollection()->transform(function ($item) {
                $item->delete_action = '<i class="fa-solid fa-trash  text-danger delete_alert" data-id="' . $item->id . '" data-alert_message="Are you sure want to delete Driver?" data-alert_title="Delete"
                data-alert_type="warning"  data-alert_url="'.route('admin.driver.destroy',$item->id).'"></i>&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="' . route('admin.master.editBrand', $item->id) . '" class="text-black"><i class="fa-solid fa-pen-to-square text-warning"></i></a>';

                $item->image = $item->user_image
                ? '<img src="' . Storage::url($item->user_image) . '" alt="Driver Image" style="height: 40px;">'
                : '<span class="text-muted">No Image</span>';
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
        $editdriver = User::findOrFail($id);
        $drivers    = User::all();
        return view('admin.user.driver', compact('editdriver', 'drivers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Add logic to update an existing driver (optional)
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $drivers = User::find($id);

        if ($drivers) {
            $drivers->delete();
            return response()->json(['success' => true, 'message' => 'Country deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Country not found.'], 404);
    }
}
