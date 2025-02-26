<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Route;
use App\Models\Route as RouteModel;
use App\Models\RouteStation;
use Illuminate\Http\Request;

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
                <i class="fa-solid fa-pen-to-square text-primary"></i></a>';
                $item->delete_action .= '<a href="' . route('admin.master.route-setup.stations', $item->id) . '" class="text-black">
               <i class="fa-solid fa-map-location-dot text-danger ms-2"></i></a>';

                // Toggle Switch for Status
                $item->suspend_status = '<div class="form-check form-switch">
                <input class="form-check-input confirmation_alert" type="checkbox"
                    ' . ($item->is_active ? 'checked' : '') . '
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
            'distance' => 'required|numeric',
            'image'    => 'required|image|max:512',
        ]);
        if ($req->hasFile('image')) {
            $data['image'] = uploadImage($req->image, 'route-image');
        }
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
        $editRoute = RouteModel::find($id);
        return view('admin.route-setup.index', compact('editRoute'));
    }
    public function update(Request $req, $id)
    {
        $data = $req->validate([
            'name'     => 'required',
            'distance' => 'required|numeric']);
        RouteModel::find($id)->update($data);
        toastr()->success('Route updated successfully');
        return redirect()->route('admin.master.route-setup.index');

    }

    public function stations(Request $request, RouteModel $routeModel, $route_id)
    {
        $route = $routeModel->find($route_id);

        if (! $route) {
            return response()->json(['message' => 'Route not found'], 404);
        }

        $arrview = [
            'route'    => $route,
            'stations' => $route->stations,
            'cities'   => City::pluck('city_name', 'id')->toArray(),
        ];

        if ($request->ajax()) {
            $columns = [
                ['title' => 'ID', 'field' => 'id'],
                ['title' => 'Point Name', 'field' => 'point_name', 'headerFilter' => "input"],
                ['title' => 'City', 'field' => 'city_name', 'headerFilter' => "input"],
                ['title' => 'Scheduled Time', 'field' => 'scheduled_time', 'headerFilter' => "input"],
                ['title' => 'Latitude', 'field' => 'latitude', 'headerFilter' => "input"],
                ['title' => 'Longitude', 'field' => 'longitute', 'headerFilter' => "input"],
                ['title' => 'Action', 'field' => 'delete_action', 'formatter' => 'html'],
            ];

            $page      = $request->query('page', 1);
            $perPage   = $request->query('size', 10);
            $sortField = $request->query('sort.0.field', 'id');
            $sortOrder = $request->query('sort.0.dir', 'asc');

            // Route ID ka filter yahan ensure karna hai
            $query = RouteStation::where('route_id', $route_id)->with('city');

            if ($sortField && $sortOrder) {
                $query->orderBy($sortField, $sortOrder);
            }

            $stations = $query->paginate($perPage, ['*'], 'page', $page);

            $stations->getCollection()->transform(function ($station) {
                $station->city_name = $station->city ? $station->city->city_name : 'N/A';

                $station->delete_action = '
            <i class="fa-solid fa-trash text-danger delete_alert" data-id="' . $station->id . '" data-alert_message="Are you sure want to delete Station?" data-alert_title="Delete"
                data-alert_type="warning" data-alert_url="' . route('admin.master.route-setup.station_destroy', $station->id) . '"></i>&nbsp;&nbsp;
                <a href="javascript:void(0)" class="text-black edit-station" data-bs-target="#stationModal" data-bs-toggle="modal"
                    data-id="' . $station->id . '"
                    data-city_id="' . $station->city_id . '"
                    data-point_name="' . $station->point_name . '"
                    data-scheduled_time="' . $station->scheduled_time . '"
                    data-latitude="' . $station->latitude . '"
                    data-longitute="' . $station->longitute . '">
                    <i class="fa-solid fa-pen-to-square text-primary"></i>
                </a>';

                return $station;
            });

            return response()->json([
                'columns'   => $columns,
                'last_page' => $stations->lastPage(),
                'data'      => $stations->items(),
                'total'     => $stations->total(),
            ]);
        }

        return view('admin.route-setup.stations', $arrview);
    }

    public function station_store(Request $req, $route_id)
    {
        $data = $req->validate([
            'city_id'        => 'required',
            'point_name'     => 'required',
            'scheduled_time' => 'required',
            'latitude'       => 'required',
            'longitute'      => 'required',
        ]);
        $data['route_id'] = $route_id;
        RouteStation::create($data);
        return redirect()->back();
    }
    public function stationUpdate(Request $request, $id)
    {
        // Validate input
        $request->validate([
            'city_id'        => 'required',
            'point_name'     => 'required',
            'scheduled_time' => 'required|date',
            'latitude'       => 'required',
            'longitute'      => 'required',
        ]);

        // Find station by ID
        $station = RouteStation::find($id);
        if (! $station) {
            return redirect()->back()->with('error', 'Station not found!');
        }

        // Update station
        $station->update([
            'city_id'        => $request->city_id,
            'point_name'     => $request->point_name,
            'scheduled_time' => $request->scheduled_time,
            'latitude'       => $request->latitude,
            'longitute'      => $request->longitute,
        ]);

        toastr()->success('Station updated successfully!');
        return redirect()->back();
    }
    public function station_destroy(string $id)
    {
        $stat = RouteStation::find($id);

        if ($stat) {
            $stat->delete();
            return response()->json(['success' => true, 'message' => 'Station deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Station not found.'], 404);
    }

}
