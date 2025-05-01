<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function bookingReport(Request $request)
    {
        $totalBooking     = Booking::whereNotIn('status', ['cancelled'])->count();
        $cancelledBooking = Booking::where('status', 'cancelled')->count();
        $totalRevenue     = Booking::where('status', 'confirmed')->sum('fare_amount');

        return view('admin.report.booking-report', compact('totalRevenue', 'totalBooking', 'cancelledBooking'));
    }
    public function getBookingChartData(Request $request)
    {
        $year   = $request->year;
        $month  = $request->month;
        $filter = $request->filter;

        $labels    = [];
        $confirmed = [];
        $cancelled = [];
        $pending   = [];

        if ($filter === 'today') {
            $start = Carbon::today();
            $end   = Carbon::today()->endOfDay();

            foreach (range(0, 23) as $hour) {
                $labels[] = $hour . ':00';

                $confirmed[] = Booking::whereBetween('created_at', [$start, $end])
                    ->whereRaw('HOUR(created_at) = ?', [$hour])
                    ->where('status', 'confirmed')
                    ->count();

                $cancelled[] = Booking::whereBetween('created_at', [$start, $end])
                    ->whereRaw('HOUR(created_at) = ?', [$hour])
                    ->where('status', 'cancelled')
                    ->count();

                $pending[] = Booking::whereBetween('created_at', [$start, $end])
                    ->whereRaw('HOUR(created_at) = ?', [$hour])
                    ->where('status', 'pending')
                    ->count();
            }

        } elseif ($filter === 'yesterday') {
            $start = Carbon::yesterday()->startOfDay();
            $end   = Carbon::yesterday()->endOfDay();

            foreach (range(0, 23) as $hour) {
                $labels[] = $hour . ':00';

                $confirmed[] = Booking::whereBetween('created_at', [$start, $end])
                    ->whereRaw('HOUR(created_at) = ?', [$hour])
                    ->where('status', 'confirmed')
                    ->count();

                $cancelled[] = Booking::whereBetween('created_at', [$start, $end])
                    ->whereRaw('HOUR(created_at) = ?', [$hour])
                    ->where('status', 'cancelled')
                    ->count();

                $pending[] = Booking::whereBetween('created_at', [$start, $end])
                    ->whereRaw('HOUR(created_at) = ?', [$hour])
                    ->where('status', 'pending')
                    ->count();
            }

        } elseif ($filter === 'last_7_days') {
            $start = Carbon::now()->subDays(6)->startOfDay();
            $end   = Carbon::now()->endOfDay();

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $labels[] = $date->format('d M');

                $confirmed[] = Booking::whereDate('created_at', $date)
                    ->where('status', 'confirmed')
                    ->count();

                $cancelled[] = Booking::whereDate('created_at', $date)
                    ->where('status', 'cancelled')
                    ->count();

                $pending[] = Booking::whereDate('created_at', $date)
                    ->where('status', 'pending')
                    ->count();
            }

        } elseif ($month) {
            $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $end   = Carbon::createFromDate($year, $month, 1)->endOfMonth();

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $labels[] = $date->format('d M');

                $confirmed[] = Booking::whereDate('created_at', $date)
                    ->where('status', 'confirmed')
                    ->count();

                $cancelled[] = Booking::whereDate('created_at', $date)
                    ->where('status', 'cancelled')
                    ->count();

                $pending[] = Booking::whereDate('created_at', $date)
                    ->where('status', 'pending')
                    ->count();
            }

        } elseif ($year) {
            foreach (range(1, 12) as $m) {
                $start = Carbon::createFromDate($year, $m, 1)->startOfMonth();
                $end   = Carbon::createFromDate($year, $m, 1)->endOfMonth();

                $labels[] = Carbon::createFromDate($year, $m, 1)->format('F');

                $confirmed[] = Booking::whereBetween('created_at', [$start, $end])
                    ->where('status', 'confirmed')
                    ->count();

                $cancelled[] = Booking::whereBetween('created_at', [$start, $end])
                    ->where('status', 'cancelled')
                    ->count();

                $pending[] = Booking::whereBetween('created_at', [$start, $end])
                    ->where('status', 'pending')
                    ->count();
            }
        }

        return response()->json([
            'labels'    => $labels,
            'confirmed' => $confirmed,
            'cancelled' => $cancelled,
            'pending'   => $pending,
        ]);
    }

    public function revenueReport(Request $request)
    {
        $today        = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        // Summary data
        $totalRevenue = Transaction::where('status', 'success')->sum('amount');
        $todayRevenue = Transaction::where('status', 'success')
            ->whereDate('created_at', $today)
            ->sum('amount');

        $monthlyRevenue = Transaction::where('status', 'success')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $successfulBookings    = Transaction::where('status', 'success')->count();
        $averageRevenuePerRide = $successfulBookings > 0 ? round($totalRevenue / $successfulBookings, 2) : 0;

        // Top earning driver
        $topDriver = DB::table('bookings')
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
            ->first();

        // Revenue chart data (last 30 days)
        $revenueChart = Transaction::where('status', 'success')
            ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Payment method breakdown (pie chart)
        $paymentBreakdown = Transaction::where('status', 'success')
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get();

        return view('admin.report.revenue-report', [
            'revenue'          => [
                'total'               => $totalRevenue,
                'today'               => $todayRevenue,
                'month'               => $monthlyRevenue,
                'successful_bookings' => $successfulBookings,
                'avg_per_ride'        => $averageRevenuePerRide,
                'top_driver'          => $topDriver,
            ],
            'revenueChart'     => $revenueChart,
            'paymentBreakdown' => $paymentBreakdown,
        ]);
    }

    public function revenueChartData(Request $request)
    {
        $year  = $request->input('year', now()->year);
        $month = $request->input('month');
        $type  = $request->input('type');

        $months = collect(range(1, 12))->map(fn($m) => Carbon::create(null, $m)->format('F'))->toArray();

        $query = DB::table('transactions')
            ->where('status', 'success')
            ->whereYear('created_at', $year);

        if ($month) {
            $query->whereMonth('created_at', $month);
        }

        if ($type === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($type === 'yesterday') {
            $query->whereDate('created_at', Carbon::yesterday());
        } elseif ($type === 'last_7_days') {
            $query->whereDate('created_at', '>=', Carbon::today()->subDays(7));
        }

        $total = $query->sum('amount');

        $data = $query->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $dataArray = array_replace(array_fill(1, 12, 0), $data);

        // Previous period revenue
        $prevQuery = DB::table('transactions')->where('status', 'success');

        if ($type === 'today') {
            $prevQuery->whereDate('created_at', Carbon::yesterday());
        } elseif ($type === 'yesterday') {
            $prevQuery->whereDate('created_at', Carbon::yesterday()->subDay());
        } elseif ($type === 'last_7_days') {
            $prevQuery->whereBetween('created_at', [Carbon::today()->subDays(14), Carbon::today()->subDays(8)]);
        } elseif ($month) {
            $prevQuery->whereYear('created_at', $year)->whereMonth('created_at', $month - 1);
        } else {
            $prevQuery->whereYear('created_at', $year - 1);
        }

        $previousTotal = $prevQuery->sum('amount');

        return response()->json([
            'months'         => $months,
            'revenues'       => array_map(fn($v) => (float) $v, array_values($dataArray)),
            'total'          => $total,
            'previous_total' => $previousTotal,
        ]);
    }
    public function customerReport()
    {
        $totalUsers = User::role('user')->count();

        $newUsers = User::role('user')
            ->whereMonth('created_at', now()->month)
            ->count();

        $totalRides          = DB::table('rides')->count();
        $avgRidesPerCustomer = $totalUsers > 0 ? $totalRides / $totalUsers : 0;

        // Get user IDs who made bookings in the last 30 days
        $activeUserIds = DB::table('bookings')
            ->where('created_at', '>=', now()->subDays(30))
            ->pluck('user_id')
            ->unique()
            ->toArray();

        // Inactive users: All users except those in activeUserIds
        $inactiveCustomers = User::role('user')
            ->whereNotIn('id', $activeUserIds)
            ->count();

        $topSpender = DB::table('transactions')
            ->join('users', 'users.id', '=', 'transactions.user_id')
            ->select(DB::raw('CONCAT(users.first_name, " ", users.last_name) as name'), DB::raw('SUM(transactions.amount) as total'))
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', 'user')
            ->groupBy('users.id', 'users.first_name', 'users.last_name')
            ->orderByDesc('total')
            ->first();

        $totalRevenue = DB::table('transactions')->sum('amount');
        $avgRevenue   = $totalUsers > 0 ? $totalRevenue / $totalUsers : 0;

        // Top 10 Customers by number of bookings
        $topCustomers = DB::table('bookings')
            ->join('users', 'users.id', '=', 'bookings.user_id')
            ->select(
                'users.id',
                DB::raw('CONCAT(users.first_name, " ", users.last_name) as name'),
                DB::raw('COUNT(bookings.id) as booking_count'),
                DB::raw('(SELECT status FROM bookings b2 WHERE b2.user_id = users.id ORDER BY created_at DESC LIMIT 1) as latest_status')
            )
            ->groupBy('users.id', 'users.first_name', 'users.last_name')
            ->orderByDesc('booking_count')
            ->limit(10)
            ->get();

        // Users who made their first booking this month
        $newBookingsThisMonth = DB::table('bookings')
            ->join('users', 'users.id', '=', 'bookings.user_id')
            ->join('route_stations as pickup', 'pickup.id', '=', 'bookings.pickup_station_id')
            ->join('route_stations as dropoff', 'dropoff.id', '=', 'bookings.dropoff_station_id')
            ->whereMonth('bookings.created_at', now()->month)
            ->select(
                'users.id',
                DB::raw('CONCAT(users.first_name, " ", users.last_name) as name'),
                'pickup.point_name as pickup_station',
                'dropoff.point_name as dropoff_station'
            )
            ->groupBy('users.id', 'users.first_name', 'users.last_name', 'pickup.point_name', 'dropoff.point_name')
            ->get();

        return view('admin.report.customer-report', [
            'totalUsers'           => $totalUsers,
            'newUsers'             => $newUsers,
            'topSpenderName'       => $topSpender->name ?? 'N/A',
            'topSpenderAmount'     => $topSpender->total ?? 0,
            'avgRevenue'           => $avgRevenue,
            'avgRidesPerCustomer'  => $avgRidesPerCustomer,
            'inactiveCustomers'    => $inactiveCustomers,
            'topCustomers'         => $topCustomers,
            'newBookingsThisMonth' => $newBookingsThisMonth,
        ]);
    }

    public function customerChartDatacc(Request $request)
    {
        // Filters
        $year  = $request->get('year', now()->year);
        $month = $request->get('month', null);

        // Query to get total customers per month
        $query = User::role('user')->whereYear('created_at', $year);

        if ($month) {
            $query->whereMonth('created_at', $month);
        }

        $totalCustomers = $query->count();

        // Monthly customer count
        $monthlyCustomers = User::role('user')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as customers')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $months    = [];
        $customers = [];

        foreach ($monthlyCustomers as $data) {
            $months[]    = date('F', mktime(0, 0, 0, $data->month, 1)); // Month name
            $customers[] = $data->customers;
        }

        return response()->json([
            'total_customers' => $totalCustomers,
            'months'          => $months,
            'customers'       => $customers,
        ]);
    }

    public function customerChartData(Request $request)
    {
        $year  = $request->get('year', now()->year);
        $month = $request->get('month');

        $baseQuery = User::role('user')->whereYear('created_at', $year);

        if ($month) {
            $baseQuery->whereMonth('created_at', $month);
        }

        $totalCustomers = $baseQuery->count();

                                                // Get active user IDs (who booked in last 30 days)
        $activeUserIds = \DB::table('bookings') // or 'rides'
            ->where('created_at', '>=', now()->subDays(30))
            ->pluck('user_id')
            ->unique()
            ->toArray();

        // Get monthly stats
        $monthlyCustomers = User::role('user')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $months   = [];
        $total    = [];
        $active   = [];
        $inactive = [];

        foreach ($monthlyCustomers as $data) {
            $usersInMonth = User::role('user')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $data->month)
                ->get();

            $activeCount = $usersInMonth->filter(function ($user) use ($activeUserIds) {
                return in_array($user->id, $activeUserIds);
            })->count();

            $inactiveCount = $data->total - $activeCount;

            $months[]    = date('F', mktime(0, 0, 0, $data->month, 1));
            $total[]     = $data->total;
            $active[]    = $activeCount;
            $inactive[]  = $inactiveCount;
            $totalSum    = array_sum($total);
            $activeSum   = array_sum($active);
            $inactiveSum = array_sum($inactive);
        }

        return response()->json([
            'total_customers' => $totalCustomers,
            'months'          => $months,
            'total'           => $total,
            'active'          => $active,
            'inactive'        => $inactive,
            'total_sum'       => $totalSum,
            'active_sum'      => $activeSum,
            'inactive_sum'    => $inactiveSum,
        ]);
    }

}
