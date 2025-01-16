<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    //
    public function brand_index(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                ['title' => 'ID', 'field' => 'id'],
                ['title' => 'Brand Name', 'field' => 'brand_name', 'headerFilter' => "input"],
                ['title' => 'Logo', 'field' => 'brand_logo', 'formatter' => 'html'],
                ['title' => 'Action', 'field' => 'delete_action', 'formatter' => 'html'],


            ];
            // Get query parameters
            $page = $request->query('page', 1); // Current page
            $perPage = $request->query('size', 10); // Rows per page
            $sortField = $request->query('sort[0][field]', 'id'); // Sort field
            $sortOrder = $request->query('sort[0][dir]', 'asc'); // Sort order

            // Query data from the database
            $query = Brand::query();

            // Apply sorting
            if ($sortField && $sortOrder) {
                $query->orderBy($sortField, $sortOrder);
            }

            // Paginate results
            $brands = $query->paginate($perPage, ['*'], 'page', $page);

            $brands->getCollection()->transform(function ($item) {
                $item->delete_action = '<i class="fa-solid fa-trash delete-btn text-danger" data-id="' . $item->id . '"></i>&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="' . route('admin.master.editBrand', $item->id) . '" class="text-black"><i class="fa-solid fa-pen-to-square text-warning"></i></a>';
                $item->logo = $item->logo
                ? '<img src="' . Storage::url($item->logo) . '" alt="Logo" style="height: 40px;">'
                : '<span class="text-muted">No Logo</span>';
                return $item;
            });

            // Return response in Tabulator format
            return response()->json([
                'columns' => $columns,
                'last_page' => $brands->lastPage(),
                'data' => $brands->items(),
                'total' => $brands->total(),
            ]);
        }
        return view('admin.brand'); // Pass countries to the view
    }

    public function brand_store(Request $request)
    {


        // Validate the input fields
        $validated = $request->validate([
            'brand_name' => 'required|string|max:255',
            'brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Restrict to image files
        ]);


        $brandLogoPath = null;
        if ($request->hasFile('brand_logo')) {
            $brandLogoPath = $request->file('brand_logo')->store('brands', 'public'); // Save in 'storage/app/public/brands'
        }


        $brand = new Brand();
        $brand->brand_name = $validated['brand_name'];
        $brand->brand_logo = $brandLogoPath; // Save the logo path
        $brand->save();

        return redirect()->route('admin.master.brand')->with('success', 'Brand added successfully!');
    }
}



