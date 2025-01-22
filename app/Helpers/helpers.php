<?php
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Log;

if (!function_exists('greet')) {
    function greet($name)
    {
        return "Hello, " . ucfirst($name) . "!";
    }
}

if (!function_exists('getBusinessSetting')) {
    function getBusinessSetting($key)
    {
        $setting = BusinessSetting::where('type', $key)->first();
        if($setting){
            return $setting->value;
        } else {
            return Null;
        }
    }
}

if (!function_exists('updateBusinessSetting')) {
    function updateBusinessSetting($type, $value)
    {
        try {
            BusinessSetting::updateOrCreate(
                ['type' => $type], // Condition to check
                ['value' => $value] // Values to update or create
            );

            return true; // Operation was successful
        } catch (\Exception $e) {
            Log::info('error '.$e->getMessage());
            return false; // Operation failed
        }
    }
}
