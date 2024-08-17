<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Postcode extends Model
{
    use HasFactory;

    protected $table = 'postcodes';

    public static function postcodesNearByLongLatAndRadius($latitude, $longitude, $radius_km)
    {
        $nearbyPostcodes = self::select('postcode', 'latitude', 'longitude', DB::raw("
                (6371 * acos(cos(radians($latitude))
                * cos(radians(latitude))
                * cos(radians(longitude) - radians($longitude))
                + sin(radians($latitude))
                * sin(radians(latitude)))) AS distance_km
            "))
            ->having('distance_km', '<=', $radius_km)
            ->orderBy('distance_km', 'asc')
            ->get();

        return $nearbyPostcodes;
    }

}
