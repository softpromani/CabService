<?php
namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\City;
use App\Models\Ride;
use App\Models\RideSeatSegment;
use App\Models\RideStations;
use App\Models\RouteStation;
use App\Models\Transaction;
use App\Services\FareCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function get_route(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:2', // User must enter at least 2 characters
        ]);
        $query  = $request->search;
        $cities = City::where('city_name', 'LIKE', "%$query%")->pluck('id');
        $points = RouteStation::whereIn('city_id', $cities)
            ->orWhere('point_name', 'LIKE', "%$query%")
            ->with('city:id,city_name') // Fetch city name
            ->get();
        if ($points->isEmpty()) {
            return response()->json(['message' => 'No matching points found.'], 404);
        }

        return response()->json([
            'message' => 'Matching points found.',
            'points'  => $points,
        ], 200);
    }
    public function find_rides(Request $request)
    {
        $request->validate([
            'pickup_station_id'  => 'required|exists:ride_stations,station_id',
            'dropoff_station_id' => 'required|exists:ride_stations,station_id',
            'seats'              => 'required|integer|min:1',
        ]);

        $pickupStationId  = $request->pickup_station_id;
        $dropoffStationId = $request->dropoff_station_id;
        $requiredSeats    = $request->seats;

        // 🚕 Get all rides that pass through both pickup & dropoff stations
        $rides = Ride::where('status', 'schedule')
            ->whereHas('ride_stations', function ($query) use ($pickupStationId) {
                return $query->where('station_id', $pickupStationId);
            })
            ->whereHas('ride_stations', function ($query) use ($dropoffStationId) {
                $query->where('station_id', $dropoffStationId);
            })
            ->with(['ride_stations' => function ($query) use ($pickupStationId, $dropoffStationId) {
                return $query->whereIn('station_id', [$pickupStationId, $dropoffStationId])
                    ->orderBy('arrival'); // Ensure correct order
            }, 'car'])
            ->get();

        $availableRides = [];

        foreach ($rides as $ride) {
            // 🔍 Ensure pickup comes before dropoff in the ride stations
            $stations     = $ride->ride_stations->pluck('pivot.station_id')->toArray();
            $pickupIndex  = array_search($pickupStationId, $stations);
            $dropoffIndex = array_search($dropoffStationId, $stations);

            if ($pickupIndex === false || $dropoffIndex === false || $pickupIndex >= $dropoffIndex) {
                continue; // Skip ride if stations are not in correct order
            }

            // ✅ Check seat availability for this segment
            $availableSeats = RideSeatSegment::checkAvailableSeats($ride->id, $pickupStationId, $dropoffStationId);

            if ($availableSeats < $requiredSeats) {
                continue; // Skip if not enough seats
            }
            // 🗺️ Get distance between stations
            $pickupStation  = $ride->ride_stations()->where('station_id', $pickupStationId)->first();
            $dropoffStation = $ride->ride_stations()->where('station_id', $dropoffStationId)->first();
            // Ensure both stations exist before calling getDistanceByRoad()
            if (! $pickupStation || ! $dropoffStation) {
                return response()->json([
                    'message' => 'Invalid pickup or drop-off station.',
                ], 400);
            }

            // Extract latitude & longitude
            $pickupLocation = [
                'latitude'  => $pickupStation->latitude ?? null,
                'longitude' => $pickupStation->longitute ?? null,
            ];

            $dropoffLocation = [
                'latitude'  => $dropoffStation->latitude ?? null,
                'longitude' => $dropoffStation->longitute ?? null,
            ];

            // Validate if coordinates exist
            if (! $pickupLocation['latitude'] || ! $pickupLocation['longitude'] || ! $dropoffLocation['latitude'] || ! $dropoffLocation['longitude']) {
                return response()->json([
                    'message' => 'Missing location data for pickup or drop-off station.',
                ], 400);
            }

            // Call function with valid coordinates
            $distanceData = getDistanceByRoad($pickupLocation, $dropoffLocation);
            if (! $distanceData) {
                continue; // Skip if unable to fetch distance
            }

            $distance = $distanceData['distance_km'];
            $time     = $distanceData['duration_in_minute'];

            // 💰 Calculate fare for the requested seats
            $farePerSeat = FareCalculator::calculateFare($distance, $time, $ride->car->vehicle_type);
            $totalFare   = $farePerSeat * $requiredSeats;

            // 📋 Prepare ride details
            $availableRides[] = [
                'ride_id'         => $ride->id,
                'driver'          => $ride->driver->name,
                'car'             => $ride->car->model,
                'available_seats' => $availableSeats,
                'distance'        => $distance . ' km',
                'estimated_time'  => $time . ' mins',
                'fare_per_seat'   => round($farePerSeat, 2),
                'total_fare'      => round($totalFare, 2),
            ];
        }

        if (empty($availableRides)) {
            return response()->json(['message' => 'No available rides found.'], 404);
        }

        return response()->json([
            'message' => 'Available rides found.',
            'rides'   => $availableRides,
        ], 200);
    }

    public function apply_booking(Request $request)
    {
        $request->validate([
            'ride_id'            => 'required|exists:rides,id',
            'pickup_station_id'  => 'required|exists:ride_stations,id',
            'dropoff_station_id' => 'required|exists:ride_stations,id',
            'seats'              => 'required|integer|min:1',
            'payment_method'     => 'required|string',
        ]);

        $ride = Ride::findOrFail($request->ride_id);

        // 🔍 Check seat availability
        $availableSeats = RideSeatSegment::checkAvailableSeats(
            $ride->id,
            $request->pickup_station_id,
            $request->dropoff_station_id
        );

        if ($availableSeats < $request->seats) {
            return response()->json(['message' => 'Insufficient seats available.'], 422);
        }

        // 📍 Get station details
        $pickupStation  = RideStations::where('ride_id', $ride->id)->where('station_id', $request->pickup_station_id)->first();
        $dropoffStation = RideStations::where('ride_id', $ride->id)->where('station_id', $request->dropoff_station_id)->first();

        if (! $pickupStation || ! $dropoffStation) {
            return response()->json(['message' => 'Invalid pickup or dropoff station.'], 400);
        }

        // 🗺️ Calculate distance & duration using Google Maps API
        $distanceData = getDistanceByRoad($pickupStation->station->location, $dropoffStation->station->location);

        if (! $distanceData) {
            return response()->json(['message' => 'Unable to calculate distance.'], 500);
        }

        $distance = floatval(str_replace(' km', '', $distanceData['distance'])); // Extract numeric value
        $time     = intval(str_replace(' mins', '', $distanceData['duration'])); // Extract numeric value

        // 🧮 Calculate fare using `FareCalculator`
        $farePerSeat = FareCalculator::calculateFare($distance, $time, $ride->car->vehicle_type);
        $totalFare   = $farePerSeat * $request->seats;

        // 🎟️ Create booking
        $booking = Booking::create([
            'user_id'            => auth()->id(),
            'ride_id'            => $ride->id,
            'pickup_station_id'  => $request->pickup_station_id,
            'dropoff_station_id' => $request->dropoff_station_id,
            'total_distance'     => $distance,
            'fare_amount'        => $totalFare,
            'seats'              => $request->seats,
            'status'             => 'pending',
        ]);

        // 💰 Create transaction
        $transaction = Transaction::create([
            'booking_id'     => $booking->id,
            'user_id'        => auth()->id(),
            'amount'         => $totalFare,
            'payment_method' => $request->payment_method,
            'status'         => 'pending',
        ]);

        return response()->json([
            'message'     => 'Booking created. Proceed with payment.',
            'booking'     => $booking,
            'transaction' => $transaction,
        ], 201);
    }

    public function confirm_booking(Request $request)
    {
        $request->validate([
            'booking_id'           => 'required|exists:bookings,id',
            'transaction_id'       => 'required|exists:transactions,id',
            'payment_status'       => 'required|in:success,failed,pending',
            'transaction_response' => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            $booking = Booking::where('id', $request->booking_id)->where('status', 'pending')->first();

            if (! $booking) {
                return response()->json(['message' => 'Booking not found or already processed.'], 404);
            }

            // 🔍 Handle Success, Failed, or Pending
            if ($request->payment_status === 'success') {
                $availableSeats = RideSeatSegment::checkAvailableSeats(
                    $booking->ride_id,
                    $booking->pickup_station_id,
                    $booking->dropoff_station_id
                );

                if ($availableSeats < $booking->seats) {
                    $booking->update(['status' => 'cancelled']);
                    return response()->json(['message' => 'Seats unavailable. Booking canceled.'], 422);
                }

                // 🚀 Allot Requested Seats
                RideSeatSegment::updateSeatSegments(
                    $booking->ride_id,
                    $booking->pickup_station_id,
                    $booking->dropoff_station_id,
                    $booking->seats
                );

                $booking->update(['status' => 'confirmed']);
                $transactionStatus = 'success';

            } elseif ($request->payment_status === 'failed') {
                $booking->update(['status' => 'cancelled']);
                $transactionStatus = 'failed';

            } else {
                $booking->update(['status' => 'pending']);
                $transactionStatus = 'pending';
            }

            // 💰 Update Transaction
            Transaction::where('id', $request->transaction_id)
                ->where('booking_id', $booking->id)
                ->firstOrFail()
                ->update([
                    'status'   => $transactionStatus,
                    'response' => json_encode($request->transaction_response),
                ]);

            DB::commit();

            return response()->json([
                'message'        => $transactionStatus === 'success' ? 'Booking confirmed successfully!' : 'Booking could not be confirmed.',
                'booking_code'   => $transactionStatus === 'success' ? 'BOOK-' . $booking->id : null,
                'payment_status' => $transactionStatus,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Failed to process booking!', 'error' => $e->getMessage()], 500);
        }
    }

}
