<?php

declare(strict_types=1);

namespace App\Service\CityBike;

use App\DTO\NetworkDTO;
use App\Repository\NetworkRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
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
        private readonly LoggerInterface $logger
    )
    {
        $this->apiUrl = $this->parameterBag->get('city_bike_api_url');
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getNetworkData(array $networks): array
    {
        $stations = [];

        foreach ($networks as $network) {
            try {
                $url = sprintf('%s/%s/%s', $this->apiUrl, 'networks', $network);
                $response = $this->client->request(Request::METHOD_GET, $url);
                $responseDecoded = json_decode($response->getContent(), true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \RuntimeException("Error decoding JSON: " . json_last_error_msg());
                }

                $network = $responseDecoded['network'] ?? [];
                $stations = array_merge($stations, $network['stations'] ?? []);

            } catch (\Exception $e) {
                throw new HttpException(Response::HTTP_BAD_REQUEST, "Error fetching or decoding network data: " . $e->getMessage());
            }
        }

        return $this->distanceService->getClothesStations($stations);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function syncNetworks(): void
    {
        $url = sprintf('%s/%s', $this->apiUrl, 'networks');

        try {
            $response = $this->client->request(Request::METHOD_GET, $url);
        } catch (\Exception $e) {
            $this->logger->error('API request failed.', ['exception' => $e]);
            return;
        }

        $data = json_decode($response->getContent(), true);
        if (!isset($data['networks']) || !is_array($data['networks'])) {
            $this->logger->error('Invalid API response structure.');
            return;
        }

        $values = [];
        $params = [];

        foreach ($data['networks'] as $network) {
            try {
                /** @var NetworkDTO $networkDTO */
                $networkDTO = $this->serializer->deserialize(json_encode($network), NetworkDTO::class, 'json');
            } catch (\Exception $e) {
                $this->logger->warning('Failed to deserialize network data.', ['exception' => $e]);
                continue;
            }

            if ($networkDTO->company && !$this->networkRepository->isNetworkSynced($networkDTO->id)) {
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

        if ($values) {
            $sql = '
        INSERT INTO `network` (company_name, network_id, name, city, country, latitude, longitude)
        VALUES ' . implode(', ', $values);

            try {
                $this->connection->beginTransaction();
                $this->connection->executeStatement($sql, $params);
                $this->connection->commit();
            } catch (\Exception $e) {
                $this->connection->rollBack();
                $this->logger->error('Database operation failed.', ['exception' => $e]);
            }
        }
    }
}
