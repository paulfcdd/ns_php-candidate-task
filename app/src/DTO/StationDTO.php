<?php

declare(strict_types=1);

namespace App\DTO;

class StationDTO
{
    public string $id;
    public string $name;
    public float $latitude;
    public float $longitude;
    public int $free_bikes;
}
