<?php
namespace App\Http\Controllers\admin;

use App\Models\Route;
use Illuminate\Http\Request;
use App\Models\Route as RouteModel;
use App\Http\Controllers\Controller;

class RouteController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                ['title' => 'ID', 'field' => 'id'],
                ['title' => 'Route Name', 'field' => 'name', 'headerFilter' => "input"],
                ['title' => 'Distance', 'field' => 'distance', 'headerFilter' => "input"],
                ['title' => 'Status', 'field' => 'suspend_status', 'formatter' => "html"],
                ['title' => 'Action', 'field' => 'delete_action', 'formatter' => 'html'],
            ];

            $page      = $request->query('page', 1);
            $perPage   = $request->query('size', 10);
            $sortField = $request->query('sort[0][field]', 'id');
            $sortOrder = $request->query('sort[0][dir]', 'asc');

            $query = Route::query();

            // Apply sorting
            if ($sortField && $sortOrder) {
                $query->orderBy($sortField, $sortOrder);
            }

            // Paginate results
            $routes = $query->paginate($perPage, ['*'], 'page', $page);

            $routes->getCollection()->transform(function ($item) {

                $item->delete_action = '
                <a href="' . route('admin.master.route-setup.edit', $item->id) . '" class="text-black">
                <i class="fa-solid fa-pen-to-square text-success"></i></a>';

                // Toggle Switch for Status
                $item->suspend_status = '<div class="form-check form-switch">
                <input class="form-check-input confirmation_alert" type="checkbox" 
                    '.($item->is_active ? 'checked' : '').' 
                    data-id="' . $item->id . '" 
                    data-alert_message="Want to change suspend status?"
                    data-alert_title="Are you sure?" 
                    data-alert_type="warning" 
                    data-alert_url="' . route('admin.master.route-setup.status') . '"
                    data-status_field="is_active">
                </div>';

                return $item;
            });

            return response()->json([
                'columns'   => $columns,
                'last_page' => $routes->lastPage(),
                'data'      => $routes->items(),
                'total'     => $routes->total(),
            ]);
        }
        return view('admin.route-setup.index');

    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'name'     => 'required|string|unique:routes,name',
            'distance' => 'required|numeric']);
        RouteModel::create($data);
        return redirect()->back();

    }
    public function route_status(Request $request)
    {
        try {
            $route                    = RouteModel::findOrFail($request->id);
            $route->{$request->field} = $request->status;
            $route->save();

            return response()->json(['success' => true, 'status' => $route->{$request->field}], 200);
        } catch (Exception $ex) {
            return response()->json(['message' => 'Something went wrong', 'error' => $ex->getMessage()], 500);
        }
    }
    public function edit($id)
    {
        $editRoute= RouteModel::find($id);
        $routeModel   = RouteModel::all();
        return view('admin.route-setup.index', compact('editRoute', 'routeModel'));
    }
    public function update(Request $req , $id)
    {
        $data = $req->validate([
            'name'     => 'required',
            'distance' => 'required|numeric']);
        RouteModel::find($id)->update($data);
        toastr()->success('Route updated successfully');
        return redirect()->route('admin.master.route-setup.index');

    }

}
