<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\RideSchedule;
use App\Models\RideScheduleStation;
use Illuminate\Http\Request;

class RideScheduleController extends Controller
{
    public function schedule(Request $req)
    {
        $data = $req->validate([
            "car_id" => 'required|exists:cars,id',
            'start_at' => 'required',
            'end_at' => 'required',
            'ride_schedule' => 'required|array',
            'ride_schedule.*.city_id' => 'required',
            'ride_schedule.*.point_name' => 'required',
            'ride_schedule.*.longitude' => 'required',
            'ride_schedule.*.latitude' => 'required',

        ]);
        try {
            $data['driver_id'] = auth()->id();
            $res = RideSchedule::create($data);
            if ($res) {
                foreach ($data['ride_schedule'] as $ride_schedule) {
                    $ride_schedule['ride_schedule_id'] = $res->id;
                    RideScheduleStation::create($ride_schedule);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Ride Created',
                'data' => $res,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

    }
}
