<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class Store extends Model
{
    use HasFactory;

    protected $table = 'stores';

    public static function validateRequestData(Request $request): bool
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'status' => 'required|max:255',
            'store_type' => 'required|max:255',
            'max_distance' => 'required|numeric|between:0,99999',
            'latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
        ]);
        if ($validator->fails()) {
            $errorMsg = [];
            foreach ($validator->errors()->getMessages() as $msgs) {
                foreach ($msgs as $msg) {
                    $errorMsg[] = $msg;
                }
            }
            throw new \Exception(implode(', ', $errorMsg));
            return false;
        }
        return true;
    }

    public static function getNearStoresByLongLat($latitude, $longitude)
    {
        $nearbyPostcodes = self::select('*', DB::raw("
                (6371 * acos(cos(radians($latitude))
                * cos(radians(latitude))
                * cos(radians(longitude) - radians($longitude))
                + sin(radians($latitude))
                * sin(radians(latitude)))) AS distance_km
            "))
            ->having('distance_km', '<=', DB::raw('(max_distance * 1.6)')) // converting miles to kms
            ->orderBy('distance_km', 'asc')
            ->get();

        return $nearbyPostcodes;
    }
}
