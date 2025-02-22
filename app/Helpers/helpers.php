<?php
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Log;

if (! function_exists('greet')) {
    function greet($name)
    {
        return "Hello, " . ucfirst($name) . "!";
    }
}

if (! function_exists('isJson')) {
    /**
     * Check if a given string is a valid JSON.
     *
     * @param mixed $string
     * @return bool
     */
    function isJson($string): bool
    {
        if (! is_string($string)) {
            return false;
        }

        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}

if (! function_exists('getBusinessSetting')) {
    /**
     * Retrieve a business setting by key.
     * If the stored value is JSON, it will be decoded as an object.
     *
     * @param string $key
     * @return mixed|null
     */
    function getBusinessSetting(string $key)
    {
        $setting = BusinessSetting::where('type', $key)->first();

        if ($setting) {
            $value = $setting->value;

            // Check if the value is JSON and decode it as an object
            return isJson($value) ? json_decode($value) : $value;
        }

        return null;
    }
}

if (! function_exists('updateBusinessSetting')) {
    function updateBusinessSetting($type, $value)
    {
        try {
            BusinessSetting::updateOrCreate(
                ['type' => $type], // Condition to check
                ['value' => $value]// Values to update or create
            );

            return true; // Operation was successful
        } catch (\Exception $e) {
            Log::info('error ' . $e->getMessage());
            return false; // Operation failed
        }
    }
}

if (! function_exists('distance_calculator')) {
    function getDistanceByRoad($origin, $destination)
    {
        $apiKey   = env('GOOGLE_MAPS_API_KEY');
        $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            'origins'      => $origin,
            'destinations' => $destination,
            'mode'         => 'driving',
            'key'          => 'AIzaSyBhXmXSE2JxpvyCwPct8nfZK2yJYH605kk',
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if (! empty($data['rows'][0]['elements'][0]['distance'])) {
                $distance = $data['rows'][0]['elements'][0]['distance']['text'];
                $duration = $data['rows'][0]['elements'][0]['duration']['text'];
                return [
                    'distance' => $distance,
                    'duration' => $duration,
                ];
            }
        }

        return null;
    }
}
