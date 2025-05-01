<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{

    public function getBookingChartData(Request $request)
    {
        $year  = $request->input('year', now()->year);
        $month = $request->input('month');
        $type  = $request->input('type');

        $months = collect(range(1, 12))->map(fn($m) => Carbon::create(null, $m)->format('F'))->toArray();

        $query = DB::table('bookings')->whereYear('created_at', $year);

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

        // Total count
        $total = $query->count();

        $data = $query->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $dataArray = array_replace(array_fill(1, 12, 0), $data);

        return response()->json([
            'months'   => $months,
            'bookings' => array_values($dataArray),
            'total'    => $total,
        ]);
    }

    public function getRevenueChartData(Request $request)
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

        // Total sum
        $total = $query->sum('amount');

        $data = $query->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $dataArray = array_replace(array_fill(1, 12, 0), $data);

        return response()->json([
            'months'   => $months,
            'revenues' => array_map(fn($v) => (float) $v, array_values($dataArray)),
            'total'    => $total,
        ]);
    }

}
