<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\MasterFare;
use Illuminate\Http\Request;

class FareSetupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                ['title' => 'ID', 'field' => 'id'],
                ['title' => 'Min Km', 'field' => 'min_km', 'headerFilter' => 'number'],
                ['title' => 'Base Fare', 'field' => 'base_fare', 'headerFilter' => 'number'],
                ['title' => 'Rupee/km', 'field' => 'per_km_rate', 'headerFilter' => 'number'],
            ];
            $page      = $request->query('page', 1);
            $perPage   = $request->query('size', 10);
            $sortField = $request->query('sort.0.field', 'id');
            $sortOrder = $request->query('sort.0.dir', 'asc');

            $query = MasterFare::query();
            if ($sortField && $sortOrder) {
                $query->orderBy($sortField, $sortOrder);
            }

            $fares = $query->paginate($perPage, ['*'], 'page', $page);

            // Debugging Output
            return response()->json([
                'columns'   => $columns,
                'last_page' => $fares->lastPage(),
                'data'      => $fares->items(),
                'total'     => $fares->total(),
            ]);
        }

        return view('admin.fare-setup.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'min_km'      => 'required|integer',
            'max_km'      => 'required|integer',
            'base_fare'   => 'required|integer',
            'per_km_rate' => 'required|integer',
        ]);
        MasterFare::create($data);
        return redirect()->back()->with('success', 'Master Fare Added');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
