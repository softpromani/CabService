<?php
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

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

if (! function_exists('getDistanceByRoad')) {
    function getDistanceByRoad(array $origin, array $destination, array $stops = [])
    {
        $intermediates = null;
        foreach ($stops as $stop) {
            $intermediates[]['latlng'] = $stop;
        }
        $pointdata = [
            'origins'           =>
            [
                'waypoint' => [
                    'location' => [
                        'latLng' => [
                            'latitude'  => $origin['latitude'],
                            'longitude' => $origin['longitude'],
                        ],
                    ],
                ],
            ],
            'destinations'      => [
                'waypoint' => [
                    'location' => [
                        'latLng' => [
                            'latitude'  => $destination['latitude'],
                            'longitude' => $destination['longitude'],
                        ],
                    ],
                ],
            ],
            'travelMode'        => 'DRIVE',
            'routingPreference' => 'TRAFFIC_AWARE',
        ];
        if ($intermediates != null) {
            $pointdata['intermediates'] = $intermediates;
        }
        $response = Http::withHeaders([
            'Content-Type'     => 'application/json',
            'X-Goog-Api-Key'   => getBusinessSetting('google-map')?->google_api_key,
            'X-Goog-FieldMask' => 'originIndex,destinationIndex,duration,distanceMeters,status',
        ])->post('https://routes.googleapis.com/distanceMatrix/v2:computeRouteMatrix', $pointdata);
        $data = $response->json();
        // Process distances and durations
        $results = [];
        foreach ($data as $route) {
            if (isset($route['distanceMeters']) && isset($route['duration'])) {
                $distanceKm  = $route['distanceMeters'] / 1000; // Convert meters to km
                $durationSec = $route['duration'];              // Duration in seconds
                                                                // Extract numeric seconds from duration (e.g., "677s" -> 677)
                preg_match('/(\d+)s/', $route['duration'], $matches);
                $durationSec = isset($matches[1]) ? (int) $matches[1] : 0;
                $durationMin = (isset($matches[1]) ? (int) $matches[1] : 0) / 60;
                // Convert duration to HH:MM:SS format
                $hours   = floor($durationSec / 3600);
                $minutes = floor(($durationSec % 3600) / 60);
                $seconds = $durationSec % 60;

                $formattedTime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

                $results[] = [
                    'originIndex'        => $route['originIndex'],
                    'destinationIndex'   => $route['destinationIndex'],
                    'distance_km'        => round($distanceKm, 2),
                    'duration'           => $formattedTime,
                    'duration_in_minute' => $durationMin,
                    'status'             => $route['status'],
                    'data'               => $data,
                ];
            }
        }
        return $results[0] ?? null;
    }
}
if (! function_exists('uploadImage')) {
    function uploadImage($image, $path = 'images')
    {
        if (! $image || ! $image->isValid()) {
            return false;
        }

        $extension = $image->getClientOriginalExtension();
        $filename  = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $filename  = $filename . '_' . time();

        try {
            // ✅ Create ImageManager with GD driver (Mandatory in v3)
            $manager = new ImageManager(new Driver());

            if ($extension === 'gif') {
                // Store GIF as is
                $storedPath = $image->storeAs($path, $filename . '.gif', 'local');
            } else {
                // Convert to WebP
                // $webpPath = storage_path('app/' . $path . '/' . $filename . '.webp');

                // // ✅ Use `read` instead of `make` in v3
                // $manager->read($image)
                //     ->encode('webp', 90)
                //     ->save($webpPath);

                // $storedPath = $path . '/' . $filename . '.webp';
                $storedPath = $image->storeAs($path, $filename . '.' . $extension, 'local');
            }

            return $storedPath;
        } catch (\Exception $e) {
            \Log::error('Image upload failed: ' . $e->getMessage());
            return false;
        }
    }
    function getFileUrl($files)
    {
        $baseUrl = asset('storage'); // Adjust if needed

        if (is_array($files)) {
            return array_map(fn($file) => $file ? "$baseUrl/$file" : null, $files);
        }

        return $files ? "$baseUrl/$files" : null;
    }
}
