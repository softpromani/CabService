<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;

class CscController extends Controller
{
    public function country_index(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                ['title' => 'ID', 'field' => 'id'],
                ['title' => 'Country Name', 'field' => 'name', 'headerFilter' => "input"],
                ['title' => 'Country Code', 'field' => 'code', 'headerFilter' => "input"],
                ['title' => 'Short Name', 'field' => 'sname', 'headerFilter' => "input"],

            ];
            // Get query parameters
            $page = $request->query('page', 1); // Current page
            $perPage = $request->query('size', 10); // Rows per page
            $sortField = $request->query('sort[0][field]', 'id'); // Sort field
            $sortOrder = $request->query('sort[0][dir]', 'asc'); // Sort order

            // Query data from the database
            $query = Country::query();

            // Apply sorting
            if ($sortField && $sortOrder) {
                $query->orderBy($sortField, $sortOrder);
            }

            // Paginate results
            $countries = $query->paginate($perPage, ['*'], 'page', $page);

            // Return response in Tabulator format
            return response()->json([
                'columns' => $columns,
                'last_page' => $countries->lastPage(),
                'data' => $countries->items(),
                'total' => $countries->total(),
            ]);
        }
        return view('admin.country'); // Pass countries to the view
    }

    // Show the form to create a new country

    public function country_store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:countries,code',
            'sname' => 'required|string|max:50',
        ]);

        // Store data in the database
        Country::create([
            'name' => $request->input('name'),
            'code' => $request->input('code'),
            'sname' => $request->input('sname'),
        ]);

        // Redirect with a success message
        return redirect()->route('admin.master.country')
            ->with('success', 'Country added successfully!');
    }

    // Update a country's information
    // public function country_update(Request $request, $id)
    // {
    //     $country = Country::findOrFail($id);

    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'code' => 'required|string|max:10|unique:countries,code,' . $id,
    //         'sname' => 'required|string|max:10|',
    //     ]);

    //     $country->update([
    //         'name' => $request->name,
    //         'code' => $request->code,
    //         'sname' => $request->sname,
    //     ]);

    //     toastr()->success('Country Updated Successfully');
    //     return redirect()->route('admin.country.index+'); // Redirect to the country list page
    // }

    // // Delete a country
    // public function country_destroy($id)
    // {
    //     $country = Country::findOrFail($id);
    //     $country->delete();

    //     toastr()->success('Country Deleted Successfully');
    //     return redirect()->route('admin.country.index'); // Redirect to the country list page
    // }

    // STATE CONTROLLER
    public function state_index(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                ['title' => 'ID', 'field' => 'id'],
                ['title' => 'Country ID', 'field' => 'id', 'headerFilter' => "input"],
                ['title' => 'State Name', 'field' => 'state_name', 'headerFilter' => "input"],
                ['title' => 'Short Name', 'field' => 'short_name', 'headerFilter' => "input"],

            ];
            // Get query parameters
            $page = $request->query('page', 1); // Current page
            $perPage = $request->query('size', 10); // Rows per page
            $sortField = $request->query('sort[0][field]', 'id'); // Sort field
            $sortOrder = $request->query('sort[0][dir]', 'asc'); // Sort order

            // Query data from the database
            $query = State::query();

            // Apply sorting
            if ($sortField && $sortOrder) {
                $query->orderBy($sortField, $sortOrder);
            }

            // Paginate results
            $states = $query->paginate($perPage, ['*'], 'page', $page);

            // Return response in Tabulator format
            return response()->json([
                'columns' => $columns,
                'last_page' => $states->lastPage(),
                'data' => $states->items(),
                'total' => $states->total(),
            ]);
        }
        return view('admin.state'); // Pass countries to the view
    }

    // Show the form to create a new state

    public function state_store(Request $request)
    {
        // dd($request->all());
        // Validate the incoming request
        $request->validate([
            'state_name' => 'required|string|max:255|unique:states,state_name',
            'short_name' => 'required|string|max:50',
        ]);

        // Store data in the database
        State::create([
            'id' => $request->input('id'),
            'state_name' => $request->input('state_name'),
            'short_name' => $request->input('short_name'),
        ]);

        // Redirect with a success message
        return redirect()->route('admin.master.state')
            ->with('success', 'State added successfully!');
    }

    // CITY CONTROLLER

    public function city_index(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                ['title' => 'ID', 'field' => 'id'],
                ['title' => 'State ID', 'field' => 'id', 'headerFilter' => "input"],
                ['title' => 'City Name', 'field' => 'city_name', 'headerFilter' => "input"],
                ['title' => 'Pin Code', 'field' => 'pin_code', 'headerFilter' => "input"],

            ];
            // Get query parameters
            $page = $request->query('page', 1); // Current page
            $perPage = $request->query('size', 10); // Rows per page
            $sortField = $request->query('sort[0][field]', 'id'); // Sort field
            $sortOrder = $request->query('sort[0][dir]', 'asc'); // Sort order

            // Query data from the database
            $query = City::query();

            // Apply sorting
            if ($sortField && $sortOrder) {
                $query->orderBy($sortField, $sortOrder);
            }

            // Paginate results
            $cities = $query->paginate($perPage, ['*'], 'page', $page);

            // Return response in Tabulator format
            return response()->json([
                'columns' => $columns,
                'last_page' => $cities->lastPage(),
                'data' => $cities->items(),
                'total' => $cities->total(),
            ]);
        }
        return view('admin.city'); // Pass countries to the view
    }

    // Show the form to create a new state

    public function city_store(Request $request)
    {
        // dd($request->all());
        // Validate the incoming request
        $request->validate([
            'city_name' => 'required|string|max:255|unique:states,state_name',
            'pin_code' => 'required|string|max:50',
        ]);

        // Store data in the database
        City::create([
            'id' => $request->input('id'),
            'city_name' => $request->input('city_name'),
            'pin_code' => $request->input('pin_code'),
        ]);

        // Redirect with a success message
        return redirect()->route('admin.master.city')
            ->with('success', 'City added successfully!');
    }

}