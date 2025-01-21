<?php
namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\State;

class CountryStateCityController extends Controller
{

    public function getCountries()
    {
        $countries = Country::all();

        return response()->json([
            'status' => 'success',
            'data'   => $countries,
        ]);
    }

    public function getStates($country_id)
    {
        $states = State::where('country_id', $country_id)->get();

        if ($states->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No states found for this country.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $states,
        ]);
    }

    public function getCities($state_id)
    {
        $cities = City::where('state_id', $state_id)->get();

        if ($cities->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No cities found for this state.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $cities,
        ]);
    }
}
