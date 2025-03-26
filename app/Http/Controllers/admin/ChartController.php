<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaction;
use Carbon\Carbon;

class ChartController extends Controller
{
    public function getChartData()
    {
        $months = collect(range(1, 12))->map(function ($month) {
            return Carbon::create(null, $month)->format('F'); // January, February, etc.
        });

        $bookings = Booking::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->pluck('count', 'month')->toArray();

        $revenues = Transaction::where('status', 'success')
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month')->toArray();

        return response()->json([
            'months'   => $months,
            'bookings' => array_values(array_replace(array_fill(0, 12, 0), $bookings)),
            'revenues' => array_values(array_replace(array_fill(0, 12, 0), $revenues)),
        ]);
    }
}
