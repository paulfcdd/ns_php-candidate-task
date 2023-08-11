<?php

declare(strict_types=1);

namespace App\Service\Parser;

use App\DTO\BikerDTO;
use Symfony\Component\Serializer\SerializerInterface;

class XmlParser implements ParserInterface
{
    public function __construct(private readonly SerializerInterface $serializer)
    {}

    public function parse(string $filePath): array
    {
        $xml = simplexml_load_file($filePath);
        $result = [];
        foreach ($xml->biker as $bikerNode) {
            $data = [
                'count' => (string) $bikerNode->count,
                'latitude' => floatval($bikerNode->latitude),
                'longitude' => floatval($bikerNode->longitude)
            ];
            $result[] = $this->serializer->denormalize($data, BikerDTO::class, null, ['groups' => 'biker']);
        }
        return $result;
    }
}
