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
                ['title' => 'Actions', 'field' => 'delete_action', 'formatter' => 'html', 'headerSort' => 'false'],

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

            $countries->getCollection()->transform(function ($item) {
                $item->delete_action = '<i class="fa-solid fa-trash delete-btn text-danger" data-id="' . $item->id . '"></i>&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="' . route('admin.master.editCountry', $item->id) . '" class="text-black"><i class="fa-solid fa-pen-to-square text-warning"></i></a>
                ';
                return $item;
            });

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

    public function editCountry($id)
    {
        $editcountry = Country::findOrFail($id);
        $countries = Country::all();
        return view('admin.country', compact('editcountry', 'countries'));
    }

    public function updateCountry(Request $request, $id)
    {

        $validatedData = $request->validate([
            'name' => 'required|string', // Field to be updated
            'code' => 'required', // New value
            'sname' => 'required',
        ]);

        $country = Country::findOrFail($id);
        $country->name = $request->input('name');
        $country->code = $request->input('code');
        $country->sname = $request->input('sname');
        $country->save();

        return redirect()->route('admin.master.country')
            ->with('success', 'Country updated successfully!');

    }

    // // Delete a country
    public function country_destroy($id)
    {
        $country = Country::find($id);

        if ($country) {
            $country->delete();
            return response()->json(['success' => true, 'message' => 'Country deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Country not found.'], 404);
    }

    // STATE CONTROLLER
    public function state_index(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                ['title' => 'ID', 'field' => 'id'],
                ['title' => 'Country ID', 'field' => 'id', 'headerFilter' => "input"],
                ['title' => 'State Name', 'field' => 'state_name', 'headerFilter' => "input"],
                ['title' => 'Short Name', 'field' => 'short_name', 'headerFilter' => "input"],
                ['title' => 'Actions', 'field' => 'delete_action', 'formatter' => 'html', 'headerSort' => 'false'],

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

            $states->getCollection()->transform(function ($item) {
                $item->delete_action = '<i class="fa-solid fa-trash delete-btn text-danger" data-id="' . $item->id . '"></i>&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="' . route('admin.master.editState', $item->id) . '" class="text-black"><i class="fa-solid fa-pen-to-square text-warning"></i></a>';
                return $item;
            });

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

    public function editState($id)
    {
        $editstate = State::findOrFail($id);
        $states = State::all();
        return view('admin.state', compact('editstate', 'states'));
    }

    public function updateState(Request $request, $id)
    {

        $validatedData = $request->validate([
            'id' => 'required|string', // Field to be updated
            'state_name' => 'required', // New value
            'short_name' => 'required',
        ]);

        $state = State::findOrFail($id);
        $state->id = $request->input('id');
        $state->state_name = $request->input('state_name');
        $state->short_name = $request->input('short_name');
        $state->save();

        return redirect()->route('admin.master.state')
            ->with('success', 'State updated successfully!');

    }

    public function state_destroy($id)
    {
        $country = State::find($id);

        if ($country) {
            $country->delete();
            return response()->json(['success' => true, 'message' => 'Country deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Country not found.'], 404);
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
                ['title' => 'Status', 'field' => 'status', 'formatter' => 'html'],
                ['title' => 'Actions', 'field' => 'delete_action', 'formatter' => 'html', 'headerSort' => 'false'],

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

            $cities->getCollection()->transform(function ($item) {
                $item->delete_action = '<i class="fa-solid fa-trash delete-btn text-danger" data-id="' . $item->id . '"></i>&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="' . route('admin.master.editCity', $item->id) . '" class="text-black"><i class="fa-solid fa-pen-to-square text-warning"></i></a>';
                return $item;
            });

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

    public function editCity($id)
    {
        $editcity = City::findOrFail($id);
        $cities = City::all();
        return view('admin.city', compact('editcity', 'cities'));
    }

    public function updateCity(Request $request, $id)
    {

        $validatedData = $request->validate([
            'id' => 'required|string', // Field to be updated
            'city_name' => 'required', // New value
            'pin_code' => 'required',
        ]);

        $city = City::findOrFail($id);
        $city->id = $request->input('id');
        $city->city_name = $request->input('city_name');
        $city->pin_code = $request->input('pin_code');
        $city->save();

        return redirect()->route('admin.master.city')
            ->with('success', 'City updated successfully!');

    }
    public function city_destroy($id)
    {
        $country = City::find($id);

        if ($country) {
            $country->delete();
            return response()->json(['success' => true, 'message' => 'Country deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Country not found.'], 404);
    }

}
