<?php

declare(strict_types=1);

namespace App\Service\CityBike;

use App\DTO\NetworkDTO;
use App\Repository\NetworkRepository;
use Doctrine\DBAL\Connection;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NetworkService
{
    private ?string $apiUrl;

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly ParameterBagInterface $parameterBag,
        private readonly SerializerInterface $serializer,
        private readonly NetworkRepository $networkRepository,
        private readonly Connection $connection,
        private readonly DistanceService $distanceService,
    )
    {
        $this->apiUrl = $this->parameterBag->get('city_bike_api_url');
    }

    public function getNetworkData(array $networks): array
    {
        $stations = [];

        foreach ($networks as $network) {
            $url = sprintf('%s/%s/%s', $this->apiUrl, 'networks', $network);
            $response = $this->client->request(Request::METHOD_GET, $url);
            $responseDecoded = json_decode($response->getContent(), true);
            $network = $responseDecoded['network'];
            $stations = array_merge($stations, $network['stations']);
        }

        return $this->distanceService->getClothesStations($stations);
    }

    public function syncNetworks(): void
    {
        $url = sprintf('%s/%s', $this->apiUrl, 'networks');
        $response = $this->client->request(Request::METHOD_GET, $url);
        $data = json_decode($response->getContent(), true);

        $values = [];
        $params = [];

        foreach ($data['networks'] as $network) {
            /** @var NetworkDTO $networkDTO */
            $networkDTO = $this->serializer->deserialize(json_encode($network), NetworkDTO::class, 'json');

            if ($networkDTO->company) {
                if (!$this->networkRepository->isNetworkSynced($networkDTO->id)) {
                    $values[] = '(?, ?, ?, ?, ?, ?, ?)';
                    $params = array_merge($params, [
                        is_array($networkDTO->company) ? $networkDTO->company[0]: $networkDTO->company,
                        $networkDTO->id,
                        $networkDTO->name,
                        $networkDTO->location->city,
                        $networkDTO->location->country,
                        $networkDTO->location->latitude,
                        $networkDTO->location->longitude
                    ]);
                }
            }
        }

        if ($values) {
            $sql = '
            INSERT INTO `network` (company_name, network_id, name, city, country, latitude, longitude)
            VALUES ' . implode(', ', $values);

            $this->connection->executeStatement($sql, $params);
        }
    }
}
