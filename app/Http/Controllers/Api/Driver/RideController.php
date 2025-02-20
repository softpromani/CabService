<?php
namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Models\Ride;
use App\Models\RideStations;
use App\Models\Route as RouteModel;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class RideController extends Controller
{
    public function getStations(RouteModel $route)
    {
        return response()->json([
            'status' => 'success',
            'data'   => $route->first()->stations,
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
            'station_timings'                  => 'required|array', // Array of stations with timings
            'station_timings.*.station_id'     => 'required|exists:route_stations,id',
            'station_timings.*.arrival_time'   => 'required|date_format:Y-m-d H:i:s',
            'station_timings.*.departure_time' => 'nullable|date_format:Y-m-d H:i:s|after_or_equal:station_timings.*.arrival_time',

        ]);
        DB::beginTransaction(); // Start the transaction
        try {
            $ride = Ride::create([
                'driver_id'        => auth()->id(),
                'route_id'         => $request->route_id,
                'car_id'           => $request->car_id,
                'available_seats'  => $request->available_seats,
                'ride_schedule_at' => Carbon::parse($request->ride_schedule_at),
                'status'           => 'schedule',
            ]);

            // Store Station Timings
            foreach ($request->station_timings as $station) {
                RideStations::create([
                    'ride_id'    => $ride->id,
                    'station_id' => $station['station_id'],
                    'arrival'    => Carbon::parse($station['arrival_time']),
                    'departure'  => isset($station['departure_time']) ? Carbon::parse($station['departure_time']) : null,
                ]);

            }
            DB::commit(); // Commit the transaction if everything is successful
            return response()->json(['message' => 'Ride scheduled successfully!', 'ride' => $ride], 200);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on any failure

            return response()->json([
                'message' => 'Failed to schedule ride!',
                'error'   => $e->getMessage(),
            ], 500);
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
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'rides'  => $rides,
        ]);
    }
}
