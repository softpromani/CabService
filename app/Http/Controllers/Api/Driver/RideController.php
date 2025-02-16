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
            'available_seats'                  => 'required|integer|min:1',
            'departure_time'                   => 'required|date',
            'station_timings'                  => 'required|array', // Array of stations with timings
            'station_timings.*.station_id'     => 'required|exists:stations,id',
            'station_timings.*.arrival_time'   => 'required|date_format:Y-m-d H:i:s',
            'station_timings.*.departure_time' => 'nullable|date_format:Y-m-d H:i:s|after_or_equal:station_timings.*.arrival_time',

        ]);
        DB::beginTransaction(); // Start the transaction
        try {
            $ride = Ride::create([
                'driver_id'       => auth()->id(),
                'route_id'        => $request->route_id,
                'available_seats' => $request->available_seats,
                'status'          => 'schedule',
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
}
