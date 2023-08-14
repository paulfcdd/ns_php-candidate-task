<?php

declare(strict_types=1);

namespace App\Tests\Service\CityBike;

use App\Service\CityBike\NetworkService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class NetworkServiceTest extends TestCase
{
    private HttpClientInterface $client;
    private ParameterBagInterface $parameterBag;
    private NetworkService $networkService;

    protected function setUp(): void
    {
        $this->client = $this->createMock(HttpClientInterface::class);
        $this->parameterBag = new ParameterBag(['city_bike_api_url' => 'http://api.example.com']);
        $this->networkService = new NetworkService($this->client, $this->parameterBag);
    }

    public function testGetStationByNetwork(): void
    {
        $mockedNetworks = ['network1'];
        $mockedResponseData = [
            'network' => [
                'stations' => [
                    ['id' => 1, 'name' => 'Station 1'],
                    ['id' => 2, 'name' => 'Station 2']
                ]
            ]
        ];

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('getContent')
            ->willReturn(json_encode($mockedResponseData));

        $this->client->expects($this->once())
            ->method('request')
            ->with($this->anything(), 'http://api.example.com/networks/network1')
            ->willReturn($response);

        $stations = $this->networkService->getStationByNetwork($mockedNetworks);

        $this->assertCount(2, $stations);
        $this->assertEquals('Station 1', $stations[0]['name']);
        $this->assertEquals('Station 2', $stations[1]['name']);
    }
}
