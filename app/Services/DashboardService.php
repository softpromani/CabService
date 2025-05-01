<?php
namespace App\Services;

use App\Models\Booking;
use App\Models\Ride;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardData(Request $request)
    {
        $filter = $request->filter ?? null;
        $type   = $request->type ?? null;

        $response = [];

        // Always include drivers and customers
        $response['active_drivers'] = User::where('is_active', 1)->role('Driver')->count();
        $response['total_customer'] = User::where('is_active', 1)->role('User')->count();

        if ($type === 'booking') {
            $bookingQuery = Booking::query();

            if ($filter === 'today') {
                $bookingQuery->whereDate('created_at', Carbon::today());
            } elseif ($filter === 'month') {
                $bookingQuery->whereMonth('created_at', Carbon::now()->month);
            } elseif ($filter === 'year') {
                $bookingQuery->whereYear('created_at', Carbon::now()->year);
            }

            $response['total_booking'] = $bookingQuery->count();

        } elseif ($type === 'revenue') {
            $revenueQuery = Transaction::where('status', 'success');
        
            if ($filter === 'today') {
                $revenueQuery->whereDate('created_at', Carbon::today());
            } elseif ($filter === 'month') {
                $revenueQuery->whereMonth('created_at', Carbon::now()->month);
            } elseif ($filter === 'year') {
                $revenueQuery->whereYear('created_at', Carbon::now()->year);
            }
        
            $response['total_revenue'] = $revenueQuery->sum('amount');
        } else {
            // If no type is passed, return both booking and ride counts for default page
            $response['total_booking']       = Booking::count();
            $response['total_revenue']       = Transaction::where('status', 'success')->sum('amount');
            $response['total_rides']         = Ride::count();
            $response['cancelled_rides']     = Ride::where('status', 'cancel')->count();
            $response['scheduled_rides']     = Ride::where('status', 'schedule')->count();
            $response['recent_bookings']     = Booking::orderBy('id', 'desc')->take(5)->get();
            $response['top_earning_drivers'] = DB::table('bookings')
                ->join('transactions', 'transactions.booking_id', '=', 'bookings.id')
                ->join('users as drivers', 'drivers.id', '=', 'bookings.rider_id')
                ->where('transactions.status', 'success')
                ->select(
                    'drivers.id',
                    DB::raw("CONCAT(drivers.first_name, ' ', drivers.last_name) as name"),
                    DB::raw('SUM(bookings.fare_amount) as total_earnings')
                )
                ->groupBy('drivers.id', 'drivers.first_name', 'drivers.last_name')
                ->orderByDesc('total_earnings')
                ->limit(3)
                ->get();

        }

        return $response;
    }

}
