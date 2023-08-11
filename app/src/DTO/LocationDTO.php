<?php

declare(strict_types=1);

namespace App\DTO;

class LocationDTO
{
    public ?string $city;
    public ?string $country;
    public ?float $latitude;
    public ?float $longitude;
}
