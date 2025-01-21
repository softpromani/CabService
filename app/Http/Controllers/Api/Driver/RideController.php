<?php
namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\RideSchedule;
use App\Models\RideScheduleStation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RideController extends Controller
{
    public function addRoute(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'start_at' => 'required|date|after_or_equal:now',
            'end_at'   => 'required|date|after:start_at',
        ]);
        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $driver_id = auth()->id();
        $car       = Car::where('driver_id', $driver_id)->first();
        if (! $car) {
            return response()->json([
                'success' => false,
                'message' => 'No car associated with this driver.',
            ], 404);
        }

        $start_at               = Carbon::parse($request->start_at);
        $end_at                 = Carbon::parse($request->end_at);
        $travel_time_in_minutes = $start_at->diffInMinutes($end_at);

        $travel_time = [
            'minutes' => "{$travel_time_in_minutes} min",
            'hours'   => round($travel_time_in_minutes / 60, 2) . " hr",
            'seconds' => ($travel_time_in_minutes * 60) . " sec",
        ];

        $route = RideSchedule::create([
            'driver_id'   => $driver_id,
            'car_id'      => $car->id,
            'start_at'    => $request->start_at,
            'end_at'      => $request->end_at,
            'travel_time' => $travel_time_in_minutes,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Route added successfully!',
            'data'    => [
                'route'       => $route,
                'travel_time' => $travel_time,
            ],
        ]);
    }

    public function addStation(Request $request)
    {

        $station = RideScheduleStation::create([
            'ride_schedule_id' => $request->ride_schedule_id,
            'city_id'          => $request->city_id,
            'point_name'       => $request->point_name,
            'longitute'        => $request->longitude,
            'latitude'         => $request->latitude,
            'scheduled_time'   => $request->scheduled_time,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Station added successfully!',
            'data'    => $station,
        ]);
    }

    public function getStations($route_id)
    {
        // Validate if the route exists
        $stations = RideScheduleStation::where('ride_schedule_id', $route_id)->get();

        if ($stations->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No stations found for this route.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $stations,
        ]);
    }
    public function getRoutes()
    {
        // Fetch all routes with their related driver and car
        $routes = RideSchedule::with(['driver', 'car'])->get();

        return response()->json([
            'status' => 'success',
            'data'   => $routes,
        ]);
    }
}
