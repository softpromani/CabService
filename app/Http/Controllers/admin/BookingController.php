<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                ['title' => 'ID', 'field' => 'index'],
                ['title' => 'Booking No.', 'field' => 'booking_number', 'headerFilter' => "input"],
                ['title' => 'User', 'field' => 'user_name', 'headerFilter' => "input"],
                ['title' => 'Fare', 'field' => 'fare_amount', 'headerFilter' => "input"],
                ['title' => 'Distance', 'field' => 'total_distance', 'headerFilter' => "input"],
                ['title' => 'Rider', 'field' => 'rider_name', 'headerFilter' => 'input'],
                ['title' => 'Pickup Station', 'field' => 'pickup_station', 'headerFilter' => 'input'],
                ['title' => 'Dropoff Station', 'field' => 'dropoff_station', 'headerFilter' => 'input'],

                ['title' => 'Created At', 'field' => 'created_at', 'headerFilter' => "input"],
                ['title' => 'Status', 'field' => 'status', 'formatter' => 'html', 'headerFilter' => "input"],
            ];

            $query = Booking::with(['book_by_user', 'rider', 'station_origin', 'station_destination'])->orderBy('id', 'desc');

            $page      = $request->query('page', 1);
            $perPage   = $request->query('size', 10);
            $sortField = $request->query('sort.0.field', 'id');
            $sortOrder = $request->query('sort.0.dir', 'desc');

            if ($sortField && $sortOrder) {
                $query->orderBy($sortField, $sortOrder);
            }

            $bookings   = $query->paginate($perPage, ['*'], 'page', $page);
            $startIndex = ($page - 1) * $perPage + 1;

            $bookings->getCollection()->transform(function ($item) use (&$startIndex) {
                $status      = ucfirst($item->status);
                $statusBadge = match (strtolower($status)) {
                    'confirmed' => '<span class="badge bg-label-success">Confirmed</span>',
                    'cancelled' => '<span class="badge bg-label-danger">Cancelled</span>',
                    'pending'   => '<span class="badge bg-label-warning">Pending</span>',
                    default     => '<span class="badge bg-secondary">' . $status . '</span>',
                };

                return [
                    'index'           => $startIndex++,
                    'booking_number'  => $item->booking_number,
                    'user_name'       => $item->book_by_user
                    ? $item->book_by_user->first_name . ' ' . $item->book_by_user->last_name
                    : 'N/A',
                    'rider_name'      => $item->rider
                    ? $item->rider->first_name . ' ' . $item->rider->last_name
                    : 'N/A',
                    'pickup_station'  => optional($item->pickup_station)->point_name ?? 'N/A',
                    'dropoff_station' => optional($item->dropoff_station)->point_name ?? 'N/A',
                    'fare_amount'     => $item->fare_amount,
                    'total_distance'  => $item->total_distance,
                    'status'          => $statusBadge,
                    'created_at'      => Carbon::parse($item->created_at)
                        ->setTimezone('Asia/Kolkata')
                        ->format('d-F-Y H:i'),
                ];
            });

            return response()->json([
                'columns'   => $columns,
                'last_page' => $bookings->lastPage(),
                'data'      => $bookings->items(),
                'total'     => $bookings->total(),
            ]);
        }

        return view('admin.booking.booking');
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
        //
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
