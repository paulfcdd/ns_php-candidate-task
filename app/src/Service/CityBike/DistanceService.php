<?php

declare(strict_types=1);

namespace App\Service\CityBike;

use App\DTO\BikerDTO;
use App\DTO\StationDTO;
use App\Service\Parser\ParserFactory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\SerializerInterface;

class DistanceService
{
    const EARTH_RADIUS = 6371.0;
    private string $filePath;

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ParameterBagInterface $parameterBag,
    )
    {
        $this->filePath = $this->parameterBag->get('bikers_csv_path');
    }

    public function getClothesStations(array $networkStations): array
    {
        return $this->getShortestDistances($networkStations);
    }

    private function getShortestDistances(array $stations): array
    {
        $stationsDTOs = array_map(fn($station) => $this->serializer->denormalize($station, StationDTO::class), $stations);
        $bikers = $this->getBikersData();
        $shortestDistances = [];
        $uniqueIds = [];

        foreach ($bikers as $biker) {
            $closestStation = $this->prepareStationData($biker, $stationsDTOs);

            if (in_array($closestStation->id, $uniqueIds)) {
                continue;
            }

            $uniqueIds[] = $closestStation->id;
            $shortestDistances[] = [
                "name" => $closestStation->name,
                "distance" => $this->getDistance(
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

    private function prepareStationData(BikerDTO $biker, array $stations): StationDTO
    {
        usort($stations, fn($a, $b) =>
            $this->getDistance($a->latitude, $a->longitude, $biker->latitude, $biker->longitude) <=>
            $this->getDistance($b->latitude, $b->longitude, $biker->latitude, $biker->longitude)
        );

        return $stations[0];
    }

    private function getBikersData(): array
    {
        $filesystem = new Filesystem();

        if (!$filesystem->exists($this->filePath)) {
            throw new \RuntimeException("File not found: {$this->filePath}");
        }

        $extension = pathinfo($this->filePath, PATHINFO_EXTENSION);
        $parser = ParserFactory::create($extension, $this->serializer);

        return $parser->parse($this->filePath);
    }

    private function getDistance(float $latitude1, float $longitude1, float $latitude2, float $longitude2): float
    {
        $deltaLatitude = deg2rad($latitude2 - $latitude1);
        $deltaLongitude = deg2rad($longitude2 - $longitude1);

        $a = sin($deltaLatitude / 2)**2
            + cos(deg2rad($latitude1))
            * cos(deg2rad($latitude2))
            * sin($deltaLongitude / 2)**2;

        $distance = 2 * self::EARTH_RADIUS * asin(sqrt($a));

        return round($distance, 2);
    }
}
