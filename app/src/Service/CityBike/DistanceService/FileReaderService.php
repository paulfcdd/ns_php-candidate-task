<?php

declare(strict_types=1);

namespace App\Service\CityBike\DistanceService;

use App\Service\Parser\ParserFactory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\SerializerInterface;

class FileReaderService
{
    private Filesystem $filesystem;

    public function __construct(private readonly SerializerInterface $serializer)
    {
        $this->filesystem = new Filesystem();
    }

    public function readFile(string $filePath): array
    {
        if (!$this->filesystem->exists($filePath)) {
            throw new \RuntimeException("File not found: {$filePath}");
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $parser = ParserFactory::create($extension, $this->serializer);

        return $parser->parse($filePath);
    }
}
