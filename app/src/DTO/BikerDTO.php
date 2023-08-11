<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Serializer\Annotation\Groups;

class BikerDTO {
    #[Groups(["biker"])]
    public string $count;

    #[Groups(["biker"])]
    public float $latitude;

    #[Groups(["biker"])]
    public float $longitude;
}
