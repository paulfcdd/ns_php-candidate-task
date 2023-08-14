<?php

declare(strict_types=1);

namespace App\Service\CityBike;

use App\DTO\StationDTO;
use App\Service\CityBike\DistanceService\DistanceCalculationService;
use App\Service\CityBike\DistanceService\FileReaderService;
use App\Service\CityBike\DistanceService\StationSorterService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\SerializerInterface;

class DistanceService
{
    private string $filePath;

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ParameterBagInterface $parameterBag,
        private readonly FileReaderService $fileReaderService,
        private readonly StationSorterService $stationSorterService,
        private readonly DistanceCalculationService $distanceCalculationService,
    )
    {
        $this->filePath = $this->parameterBag->get('bikers_csv_path');
    }

    public function getClothesStations(array $stations): array
    {
        $stationsDTOs = array_map(fn($station) => $this->serializer->denormalize($station, StationDTO::class), $stations);
        $bikers = $this->fileReaderService->readFile($this->filePath);
        $shortestDistances = [];
        $uniqueIds = [];

        foreach ($bikers as $biker) {
            $closestStation = $this->stationSorterService->sortStationsByProximity($biker, $stationsDTOs);

            if (in_array($closestStation->id, $uniqueIds)) {
                continue;
            }

            $uniqueIds[] = $closestStation->id;
            $shortestDistances[] = [
                "name" => $closestStation->name,
                "distance" => $this->distanceCalculationService->getDistance(
                    $closestStation->latitude,
                    $closestStation->longitude,
                    $biker->latitude,
                    $biker->longitude
                ),
                "free_bike_count" => $closestStation->free_bikes,
                "biker_count" => $biker->count,
                "latitude" => $closestStation->latitude,
                "longitude" => $closestStation->longitude,
                "id" => $closestStation->id,
            ];
        }

        return $shortestDistances;
    }
}
