<?php

declare(strict_types=1);

namespace App\Tests\Service\CityBike\DistanceService;

use App\Service\CityBike\DistanceService\DistanceCalculationService;
use PHPUnit\Framework\TestCase;

class DistanceCalculationServiceTest extends TestCase
{
    private DistanceCalculationService $distanceCalculationService;

    protected function setUp(): void
    {
        $this->distanceCalculationService = new DistanceCalculationService();
    }

    public function testGetDistance(): void
    {
        $result = $this->distanceCalculationService->getDistance(52.5200, 13.4050, 48.8566, 2.3522);

        $expectedDistance = 877.46;

        $this->assertEquals($expectedDistance, $result);
    }
}

