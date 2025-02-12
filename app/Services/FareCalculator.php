<?php
namespace App\Services;

class FareCalculator
{
    public static function calculateFare($distance, $time, $vehicleType)
    {
        // Fetch all applicable slabs for the vehicle type in ascending order of min_km
        $fareSlabs = Fare::where('vehicle_type', $vehicleType)
            ->orderBy('min_km', 'asc')
            ->get();

        if ($fareSlabs->isEmpty()) {
            return response()->json(['error' => 'No fare slabs found for this vehicle type'], 404);
        }

        $totalFare         = 0;
        $remainingDistance = $distance;

        foreach ($fareSlabs as $slab) {
            if ($remainingDistance <= 0) {
                break;
            }

            $slabStart = $slab->min_km;
            $slabEnd   = $slab->max_km;
            $perKmRate = $slab->per_km_rate;

            // Determine applicable distance in this slab
            if ($remainingDistance > $slabEnd) {
                $distanceInSlab = $slabEnd - $slabStart;
            } else {
                $distanceInSlab = $remainingDistance - $slabStart;
            }

            // Ensure valid distance calculation
            $distanceInSlab = max(0, $distanceInSlab);

            // Apply the fare for this slab
            $totalFare += $distanceInSlab * $perKmRate;

            // Reduce remaining distance
            $remainingDistance -= $distanceInSlab;
        }

        // Add base fare and time-based charges
        $baseFare        = $fareSlabs->first()->base_fare ?? 0;
        $perMinuteRate   = $fareSlabs->first()->per_minute_rate ?? 0;
        $surgeMultiplier = $fareSlabs->first()->surge_multiplier ?? 1;

        $totalFare = ($baseFare + $totalFare + ($time * $perMinuteRate)) * $surgeMultiplier;

        return round($totalFare, 2);
    }

}
