<?php
namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Models\Ride;
use App\Models\RideSeatSegment;
use App\Models\RideStations;
use App\Models\Route as RouteModel;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class RideController extends Controller
{
    public function getStations($route)
    {
        $routedata = RouteModel::find($route);
        return response()->json([
            'status' => 'success',
            'data'   => $routedata->stations,
        ]);
    }
    public function getRoutes()
    {
        // Fetch all routes with their related driver and car
        $routes = RouteModel::active()->get();
        return response()->json([
            'status' => 'success',
            'data'   => $routes,
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'route_id'                         => 'required|exists:routes,id',
            'car_id'                           => 'required|exists:cars,id',
            'available_seats'                  => 'required|integer|min:1',
            'ride_schedule_at'                 => 'required|date_format:Y-m-d H:i:s',
            'station_timings'                  => 'required|array',
            'station_timings.*.station_id'     => 'required|exists:route_stations,id',
            'station_timings.*.arrival_time'   => 'required|date_format:Y-m-d H:i:s',
            'station_timings.*.departure_time' => 'nullable|date_format:Y-m-d H:i:s|after_or_equal:station_timings.*.arrival_time',
        ]);

        DB::beginTransaction();

        try {
            // Create Ride
            $ride = Ride::create([
                'driver_id'        => auth()->id(),
                'route_id'         => $request->route_id,
                'car_id'           => $request->car_id,
                'available_seats'  => $request->available_seats,
                'ride_schedule_at' => Carbon::parse($request->ride_schedule_at),
                'status'           => 'schedule',
            ]);

            // Store Station Timings
            $stations = [];
            foreach ($request->station_timings as $station) {
                RideStations::create([
                    'ride_id'    => $ride->id,
                    'station_id' => $station['station_id'],
                    'arrival'    => Carbon::parse($station['arrival_time']),
                    'departure'  => $station['departure_time'] ? Carbon::parse($station['departure_time']) : null,
                ]);
                $stations[] = $station['station_id'];
            }

            // Generate Ride Seat Segments
            for ($i = 0; $i < count($stations) - 1; $i++) {
                for ($j = $i + 1; $j < count($stations); $j++) {
                    RideSeatSegment::create([
                        'ride_id'         => $ride->id,
                        'from_station_id' => $stations[$i],
                        'to_station_id'   => $stations[$j],
                        'occupied_seats'  => 0,
                    ]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Ride scheduled successfully!', 'ride' => $ride], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to schedule ride!', 'error' => $e->getMessage()], 500);
        }
    }

    public function driver_rides(Request $request)
    {
        $request->validate([
            'ride_schedule_at' => 'nullable|date',
            'station_id'       => 'nullable|exists:ride_stations,id',
            'route_name'       => 'nullable|string',
        ]);
        $rides = Ride::with(['route', 'car', 'ride_stations'])
            ->when($request->input('ride_date'), function ($query, $date) {
                $query->whereDate('ride_schedule_at', $date);
            })
            ->when($request->input('station_id'), function ($query, $stationId) {
                $query->whereHas('ride_stations', function ($stationQuery) use ($stationId) {
                    $stationQuery->where('station_id', $stationId);
                });
            })
            ->when($request->input('route_name'), function ($query, $routeName) {
                $query->whereHas('route', function ($routeQuery) use ($routeName) {
                    $routeQuery->where('name', 'like', '%' . $routeName . '%');
                });
            })
            ->when($request->input('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'rides'  => $rides,
        ]);
    }

    public function ride_status_change(Request $req)
    {
        $data = $req->validate([
            'ride_id' => 'required|exists:rides,id',
            'status'  => 'required|in:started,completed',
        ]);
        $ride = Ride::find($data['ride_id']);
        if ($ride->driver_id != auth()->user()->id) {
            return response()->json(['error' => 'You are not authorise to change this ride status']);
        }
        $ride->update(['status' => $data['status']]);
        return response()->json(['message' => 'Status Changed Successfully']);
    }
}
