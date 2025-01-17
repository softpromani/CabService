<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CarModel;
use Illuminate\Http\Request;

class ModelController extends Controller
{
    //
    public function model_index(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                ['title' => 'ID', 'field' => 'id'],
                ['title' => 'Brand', 'field' => 'brand_name', 'headerFilter' => "input"], // Display the brand name
                ['title' => 'Model Name', 'field' => 'model_name', 'headerFilter' => "input"],
                ['title' => 'Seats', 'field' => 'seats', 'headerFilter' => "input"],
                ['title' => 'Status', 'field' => 'is_active', 'headerFilter' => "input"], // Display the status as Active/Inactive
                ['title' => 'Action', 'field' => 'delete_action', 'formatter' => 'html'],
            ];

                                                                  // Get query parameters
            $page      = $request->query('page', 1);              // Current page
            $perPage   = $request->query('size', 10);             // Rows per page
            $sortField = $request->query('sort[0][field]', 'id'); // Sort field
            $sortOrder = $request->query('sort[0][dir]', 'asc');  // Sort order

                                              // Query data from the database with the Brand relationship
            $query = CarModel::with('brand'); // Eager loading for brand data

            // Apply sorting
            if ($sortField && $sortOrder) {
                $query->orderBy($sortField, $sortOrder);
            }

            // Paginate results
            $models = $query->paginate($perPage, ['*'], 'page', $page);

            // Transform the data
            $models->getCollection()->transform(function ($item) {
                                                                                  // Get brand name from the related Brand model
                $item->brand_name = $item->brand ? $item->brand->brand_name : ''; // Ensure the correct column name

                // Transform the status field to display Active/Inactive
                $item->is_active = $item->is_active == 1 ? 'Active' : 'Inactive';

                // Define delete action
                $item->delete_action = '<i class="fa-solid fa-trash delete-btn text-danger" data-id="' . $item->id . '"></i>&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="' . route('admin.master.editModel', $item->id) . '" class="text-black"><i class="fa-solid fa-pen-to-square text-warning"></i></a>';
                return $item;
            });

            // Return response in Tabulator format
            return response()->json([
                'columns'   => $columns,
                'last_page' => $models->lastPage(),
                'data'      => $models->items(),
                'total'     => $models->total(),
            ]);
        }

        // Pass brands to the view
        $brands = Brand::get();
        return view('admin.model', compact('brands'));
    }

    public function model_store(Request $request)
    {

        // Validate the input fields
        $validated = $request->validate([
            'brand_id'   => 'required|string|max:10',
            'model_name' => 'required|string|max:255',
        ]);

        CarModel::create([
            'brand_id'   => $request->input('brand_id'),
            'model_name' => $request->input('model_name'),

        ]);

        return redirect()->route('admin.master.model')->with('success', 'Model added successfully!');
    }

    public function editModel($id)
    {
        $editmodel = CarModel::findOrFail($id);
        $models    = CarModel::all();
        $brands    = Brand::get();
        return view('admin.model', compact('editmodel', 'models', 'brands'));
    }

    public function updateModel(Request $request, $id)
    {

        $validatedData = $request->validate([
            'brand_id'   => 'required', // Field to be updated
            'model_name' => 'required', // New value

        ]);

        $model             = CarModel::findOrFail($id);
        $model->brand_id   = $request->input('brand_id');
        $model->model_name = $request->input('model_name');

        $model->save();

        return redirect()->route('admin.master.model')
            ->with('success', 'Model updated successfully!');

    }

    // // Delete a country
    public function model_destroy($id)
    {
        $model = CarModel::find($id);

        if ($model) {
            $model->delete();
            return response()->json(['success' => true, 'message' => 'Model deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Model not found.'], 404);
    }

}
