<?php

declare(strict_types=1);

namespace App\DTO;

class NetworkDTO
{
    public null|array|string $company;
    public ?string $href;
    public ?string $id;
    public ?LocationDTO $location;
    public ?string $name;
}
