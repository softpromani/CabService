<?php
namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\City;
use App\Models\Passenger;
use App\Models\Ride;
use App\Models\RideSeatSegment;
use App\Models\RideStations;
use App\Models\RouteStation;
use App\Models\Transaction;
use App\Services\FareCalculator;
use Carbon\Carbon;
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
        $points = RouteStation::with('route')->whereIn('city_id', $cities)
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
            'travel_date'        => 'required|date_format:Y-m-d', // User only provides start date
        ], [
            'pickup_station_id.exists'  => 'No Ride available for selected pickup station',
            'dropoff_station_id.exists' => 'No Ride available for selected destination station',
        ]);

        $pickupStationId  = $request->pickup_station_id;
        $dropoffStationId = $request->dropoff_station_id;
        $requiredSeats    = $request->seats;
        $travelDate       = Carbon::parse($request->travel_date)->toDateString(); // Convert to Y-m-d

        // 🚕 Get rides where the pickup station has an arrival on the selected date
        $rides = Ride::where('status', 'schedule')
            ->whereHas('ride_stations', function ($query) use ($pickupStationId, $travelDate) {
                $query->where('station_id', $pickupStationId)
                    ->whereDate('arrival', $travelDate);
            })
            ->whereHas('ride_stations', function ($query) use ($dropoffStationId) {
                $query->where('station_id', $dropoffStationId);
            })
            ->with(['ride_stations' => function ($query) use ($pickupStationId, $dropoffStationId) {
                $query->whereIn('station_id', [$pickupStationId, $dropoffStationId])
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

            if (! $pickupStation || ! $dropoffStation) {
                return response()->json([
                    'message' => 'No Ride found for selected stations.',
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
            $car         = $ride->car;
            // 📋 Prepare ride details
            $availableRides[] = [
                'ride_id'         => $ride->id,
                'driver'          => $ride->driver->full_name,
                'car'             => [
                    'model'               => $car->model->model_name,
                    'brand'               => $car->brand->brand_name,
                    'registration_number' => $car->registration_number,
                    'images'              => getFileUrl(json_decode($car->car_images ?? [])),
                ],
                'ride_travel'     => $ride->ride_origin_destination,
                'available_seats' => $availableSeats,
                'distance'        => $distance . ' km',
                'estimated_time'  => $time,
                'fare_per_seat'   => round($farePerSeat, 2),
                'total_fare'      => round($totalFare, 2),
            ];
        }

        if (empty($availableRides)) {
            return response()->json(['message' => 'No available rides found for the selected date.'], 404);
        }

        return response()->json([
            'message' => 'Available rides found.',
            'rides'   => $availableRides,
        ], 200);
    }

    public function apply_booking(Request $request)
    {
        $request->validate([
            'ride_id'             => 'required|exists:rides,id',
            'pickup_station_id'   => 'required|exists:ride_stations,id',
            'dropoff_station_id'  => 'required|exists:ride_stations,id',
            'seats'               => 'required|integer|min:1',
            'payment_method'      => 'required|string',
            'passengers'          => 'required|array|min:1', // Passengers array
            'passengers.*.name'   => 'required|string|max:255',
            'passengers.*.age'    => 'required|integer|min:1|max:120',
            'passengers.*.gender' => 'required|in:male,female,other',
        ]);

        DB::beginTransaction(); // 🔄 Use transaction to ensure atomicity

        try {
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
            $distance = floatval(str_replace(' km', '', $distanceData['distance_km']));        // Extract numeric value
            $time     = intval(str_replace(' mins', '', $distanceData['duration_in_minute'])); // Extract numeric value

            // 🧮 Calculate fare using `FareCalculator`
            $farePerSeat = FareCalculator::calculateFare($distance, $time, $ride->car->vehicle_type);
            $totalFare   = $farePerSeat * $request->seats;

            // 🎟️ Create booking
            $booking = Booking::create([
                'user_id'            => auth()->id(),
                'ride_id'            => $ride->id,
                'rider_id'           => $ride->driver_id,
                'pickup_station_id'  => $request->pickup_station_id,
                'dropoff_station_id' => $request->dropoff_station_id,
                'total_distance'     => $distance,
                'fare_amount'        => $totalFare,
                'seats'              => $request->seats,
                'status'             => 'pending',
            ]);

            // 🏷️ Save passengers
            foreach ($request->passengers as $passenger) {
                Passenger::create([
                    'booking_id' => $booking->id,
                    'name'       => $passenger['name'],
                    'age'        => $passenger['age'],
                    'gender'     => $passenger['gender'],
                ]);
            }

            // 💰 Create transaction
            $transaction = Transaction::create([
                'booking_id'     => $booking->id,
                'user_id'        => auth()->id(),
                'amount'         => $totalFare,
                'payment_method' => $request->payment_method,
                'status'         => 'pending',
            ]);

            DB::commit(); // ✅ Commit transaction if everything is successful

            return response()->json([
                'message'     => 'Booking created. Proceed with payment.',
                'booking'     => $booking,
                'passengers'  => $booking->passengers, // Return passenger details
                'transaction' => $transaction,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // ❌ Rollback transaction in case of error
            return response()->json(['message' => 'Booking failed.', 'error' => $e->getMessage()], 500);
        }
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

    public function my_booking()
    {
        try {
            $bookings = Booking::withCount('passengers')->where('user_id', auth()->id())->paginate(10);
            return response()->json(['data' => $bookings, 'message' => 'Booking Fetch successfully']);
        } catch (\Exception $ex) {
            return response()->json(['message' => 'something went wrong'], 500);
        }
    }
    public function booking_detail($id)
    {
        $booking                 = Booking::with(['rider', 'ride'=>function($q){return $q->with(['ride_stations']);}, 'passengers'])->findOrFail($id);
        $booking->route_stations = $booking->ride->ride_stations;
        return response()->json(
            ['data' => $booking]
        );
    }
}
