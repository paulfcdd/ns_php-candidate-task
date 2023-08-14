<?php

declare(strict_types=1);

namespace App\Service\CityBike\DistanceService;

class DistanceCalculationService
{
    const EARTH_RADIUS = 6371.0;

    public function getDistance(float $lat1, float $lng1, float $lat2, float $lng2, $unit = 'K'): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = self::EARTH_RADIUS * $c;

        switch ($unit) {
            case 'M':  // Miles
                return $distance * 0.621371;
            case 'N':  // Nautical Miles
                return $distance * 0.539957;
            case 'K':  // Kilometers
            default:
                return round($distance, 2);
        }
    }
}
