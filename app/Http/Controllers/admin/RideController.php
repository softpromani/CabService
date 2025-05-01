<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Ride;
use App\Models\RideSeatSegment;
use App\Models\RideStations;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RideController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                ['title' => 'ID', 'field' => 'user_id'],
                ['title' => 'Driver', 'field' => 'driver_name', 'headerFilter' => "input"],
                ['title' => 'Route', 'field' => 'route_name', 'headerFilter' => "input"],
                ['title' => 'Schedule At', 'field' => 'ride_schedule_at', 'formatter' => 'html', 'headerFilter' => "input"],
                ['title' => 'Available Seats', 'field' => 'available_seats', 'headerFilter' => "input"],
                ['title' => 'Status', 'field' => 'status', 'formatter' => 'html', 'headerFilter' => "input"],
                ['title' => 'Car', 'field' => 'car', 'formatter' => 'html'],
                ['title' => 'Seat Segments', 'field' => 'seat_segments', 'formatter' => 'html'], 
                ['title' => 'Stations', 'field' => 'stations', 'formatter' => 'html'], 
            ];

            $query = Ride::with(['driver', 'route', 'ride_stations', 'rideSeatSegments', 'car.brand', 'car.model']);

            $page      = $request->query('page', 1);
            $perPage   = $request->query('size', 10);
            $sortField = $request->query('sort.0.field', 'id');
            $sortOrder = $request->query('sort.0.dir', 'asc');

            if ($sortField && $sortOrder) {
                $query->orderBy($sortField, $sortOrder);
            }

            $rides = $query->paginate($perPage, ['*'], 'page', $page);

            $serialNumber = ($page - 1) * $perPage + 1;

            $rides->getCollection()->transform(function ($item) use (&$serialNumber) {
                $carButton         = '';
                $seatSegmentButton = '';
                $stationButton = '';

                if ($item->car) {
                    $carButton = '<button class="btn bg-label-danger btn-sm view-car" data-car=\'' . json_encode([
                        'brand'           => $item->car->brand->brand_name ?? 'N/A',
                        'model'           => $item->car->model->model_name ?? 'N/A',
                        'interior'        => $item->car->interior ?? 'N/A',
                        'seats'           => $item->car->seat ?? 'N/A',
                        'register_number' => $item->car->registration_number ?? 'N/A',
                    ]) . '\'>View Car</button>';
                } else {
                    $carButton = '<span class="badge bg-secondary">No Car</span>';
                }

                $seatSegmentButton = '<a href="' . route('admin.rides.seat-segments', $item->id) . '" class="btn badge bg-label-orange btn-sm">View Seat Segments</a>';
                $stationButton = '<a href="' . route('admin.rides.stations', $item->id) . '" class="btn badge bg-label-pink btn-sm">View Stations</a>';

                $status      = ucfirst($item->status);
                $statusBadge = '';

                if (strtolower($status) === 'schedule') {
                    $statusBadge = '<span class="badge bg-label-success">Scheduled</span>';
                } elseif (strtolower($status) === 'cancel') {
                    $statusBadge = '<span class="badge bg-label-danger">Cancelled</span>';
                } elseif (strtolower($status) === 'start') {
                    $statusBadge = '<span class="badge bg-label-warning ">Started</span>';
                } else {
                    $statusBadge = '<span class="badge bg-secondary">' . $status . '</span>';
                }

                return [
                    'user_id'          => $serialNumber++,
                    'driver_name'      => $item->driver ? $item->driver->first_name . ' ' . $item->driver->last_name : 'N/A',
                    'route_name'       => $item->route ? $item->route->name : 'N/A',
                    'available_seats'  => $item->available_seats,
                    'status'           => $statusBadge,
                    'ride_schedule_at' => Carbon::parse($item->ride_schedule_at)->setTimezone('Asia/Kolkata')->format('d-F-Y H:i'),
                    'car'              => $carButton,
                    'seat_segments'    => $seatSegmentButton,
                    'stations' => $stationButton,
                ];
            });

            return response()->json([
                'columns'   => $columns,
                'last_page' => $rides->lastPage(),
                'data'      => $rides->items(),
                'total'     => $rides->total(),
            ]);
        }

        $rides = Ride::get();

        return view('admin.rides.list', compact('rides'));
    }
    public function seatSegments($rideId, Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                ['title' => 'ID', 'field' => 'id'],
                ['title' => 'From Station', 'field' => 'from_station_name', 'headerFilter' => 'input'],
                ['title' => 'To Station', 'field' => 'to_station_name', 'headerFilter' => 'input'],
                ['title' => 'Occupied Seats', 'field' => 'occupied_seats', 'headerFilter' => 'input'],
            ];

            $query = RideSeatSegment::where('ride_id', $rideId)
                ->with(['fromStation', 'toStation']);

            $page      = $request->query('page', 1);
            $perPage   = $request->query('size', 10);
            $sortField = $request->query('sort.0.field', 'id');
            $sortOrder = $request->query('sort.0.dir', 'asc');

            if ($sortField && $sortOrder) {
                $query->orderBy($sortField, $sortOrder);
            }

            $segments = $query->paginate($perPage, ['*'], 'page', $page);

            $serialNumber = ($page - 1) * $perPage + 1;

            $segments->getCollection()->transform(function ($segment) use (&$serialNumber) {
                return [
                    'id'                => $serialNumber++,
                    'from_station_name' => $segment->fromStation->point_name ?? 'N/A',
                    'to_station_name'   => $segment->toStation->point_name ?? 'N/A',
                    'occupied_seats'    => $segment->occupied_seats,
                ];
            });

            return response()->json([
                'columns'   => $columns,
                'last_page' => $segments->lastPage(),
                'data'      => $segments->items(),
                'total'     => $segments->total(),
            ]);
        }

        $ride = Ride::findOrFail($rideId);

        return view('admin.rides.seat_segments', compact('ride'));
    }

    public function stations($rideId, Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                ['title' => 'ID', 'field' => 'id'],
                ['title' => 'Station Name', 'field' => 'station_name', 'headerFilter' => 'input'],
                ['title' => 'Arrival Time', 'field' => 'arrival_time', 'headerFilter' => 'input'],
                ['title' => 'Departure Time', 'field' => 'departure_time', 'headerFilter' => 'input'],
            ];

            $query = RideStations::with('station')->where('ride_id', $rideId);

            $page      = $request->query('page', 1);
            $perPage   = $request->query('size', 10);
            $sortField = $request->query('sort.0.field', 'id');
            $sortOrder = $request->query('sort.0.dir', 'asc');

            if ($sortField && $sortOrder) {
                $query->orderBy($sortField, $sortOrder);
            }

            $stations = $query->paginate($perPage, ['*'], 'page', $page);

            $serialNumber = ($page - 1) * $perPage + 1;

            $stations->getCollection()->transform(function ($station) use (&$serialNumber) {
                return [
                    'id'             => $serialNumber++,
                    'station_name'   => $station->station->point_name ?? 'N/A',
                    'arrival_time'   => $station->arrival ? Carbon::parse($station->arrival)->format('d-M-Y H:i') : 'N/A',
                    'departure_time' => $station->departure ? Carbon::parse($station->departure)->format('d-M-Y H:i') : 'N/A',
                ];
            });

            return response()->json([
                'columns'   => $columns,
                'last_page' => $stations->lastPage(),
                'data'      => $stations->items(),
                'total'     => $stations->total(),
            ]);
        }

        $ride = Ride::findOrFail($rideId);

        return view('admin.rides.stations', compact('ride'));
    }
}
