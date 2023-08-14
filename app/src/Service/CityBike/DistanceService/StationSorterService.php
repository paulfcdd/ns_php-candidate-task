<?php

declare(strict_types=1);

namespace App\Service\CityBike\DistanceService;

use App\DTO\BikerDTO;
use App\DTO\StationDTO;

class StationSorterService
{
    public function __construct(private readonly DistanceCalculationService $distanceService)
    {}

    public function sortStationsByProximity(BikerDTO $biker, array $stations): StationDTO
    {
        usort($stations, fn($a, $b) =>
            $this->distanceService->getDistance($a->latitude, $a->longitude, $biker->latitude, $biker->longitude) <=>
            $this->distanceService->getDistance($b->latitude, $b->longitude, $biker->latitude, $biker->longitude)
        );

        return $stations[0];
    }
}
