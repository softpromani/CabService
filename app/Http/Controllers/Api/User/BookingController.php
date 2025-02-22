<?php
namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Ride;
use App\Models\Transaction;
use Request;

class BookingController extends Controller
{
    public function apply_booking(Request $request)
    {
        $request->validate([
            'ride_id'            => 'required|exists:rides,id',
            'pickup_station_id'  => 'required|exists:ride_stations,id',
            'dropoff_station_id' => 'required|exists:ride_stations,id',
            'payment_method'     => 'required|string',
        ]);
        $ride = Ride::find($request->ride_id);

        if (! $ride || $ride->available_seats <= 0) {
            return response()->json(['message' => 'No seats available for this ride.'], 422);
        }
        $booking = Booking::create([
            'user_id'            => auth()->id(),
            'rider_id'           => $ride->driver_id,
            'pickup_station_id'  => $request->pickup_station_id,
            'dropoff_station_id' => $request->dropoff_station_id,
            'total_distance'     => $request->total_distance,
            'fare_amount'        => $request->fare_amount,
            'status'             => 'pending',
        ]);

        // Create pending transaction
        $transaction = Transaction::create([
            'booking_id'     => $booking->id,
            'user_id'        => $request->user_id,
            'amount'         => $request->fare_amount,
            'payment_method' => 'card',
            'status'         => 'pending',
        ]);

        return response()->json([
            'message'     => 'Booking created. Proceed with payment.',
            'booking'     => $booking,
            'transaction' => $transaction,
        ], 201);
    }
}
