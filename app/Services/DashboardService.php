<?php
namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;

class DashboardService
{
    public function getDashboardData($filter = null)
    {
        $query = Booking::query();

        if ($filter === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($filter === 'month') {
            $query->whereMonth('created_at', Carbon::now()->month);
        } elseif ($filter === 'year') {
            $query->whereYear('created_at', Carbon::now()->year);
        }
        return [
            'active_drivers' => User::where('is_active', 1)->role('Driver')->count(),
            'total_customer' => User::where('is_active', 1)->role('User')->count(),
            'total_booking'  => $query->count(),
        ];
    }

}
