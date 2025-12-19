<?php
namespace App\Helper;

class HelperFunction
{
    const EARTH_RADIUS_KM = 6371;
    public static function generateRandNumber()
    {
        $length = config('constants.ORDER_NUMBER_LENGTH',6);
        $allowLeadingZeros = config('constants.ORDER_NUMBER_ALLOW_LEADING_ZEROS', true);
        if ($allowLeadingZeros) {
            return str_pad(random_int(0, (10 ** $length) - 1), $length, '0', STR_PAD_LEFT);
        } else {
            $min = 10 ** ($length - 1);
            $max = (10 ** $length) - 1;
            return (string) random_int($min, $max);
        }
    }
    
    public static function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        $dLat = $lat2Rad - $lat1Rad;
        $dLon = $lon2Rad - $lon1Rad;

        $a = sin($dLat / 2) ** 2 +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_KM * $c;
    }
}
