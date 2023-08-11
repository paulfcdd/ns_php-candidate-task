<?php

declare(strict_types=1);

namespace App\Service\Parser;

use App\DTO\BikerDTO;
use Symfony\Component\Serializer\SerializerInterface;

class CsvParser implements ParserInterface
{
    public function __construct(private readonly SerializerInterface $serializer)
    {}

    public function parse(string $filePath): array
    {
        $data = explode("\n", file_get_contents($filePath));
        array_shift($data);

        return array_map(function($row) {
            $info = explode(',', $row);
            $data = [
                'count' => $info[0] ?? '',
                'latitude' => floatval($info[1] ?? 0),
                'longitude' => floatval($info[2] ?? 0)
            ];

            return $this->serializer->denormalize($data, BikerDTO::class, null, ['groups' => 'biker']);
        }, $data);
    }
}
