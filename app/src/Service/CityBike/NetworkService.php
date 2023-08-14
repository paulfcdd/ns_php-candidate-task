<?php

declare(strict_types=1);

namespace App\Service\CityBike;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
    public function getStationByNetwork(array $networks): array
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

        return $stations;
    }
}
