<?php

declare(strict_types=1);

namespace App\Service\Parser;

class ParserFactory
{
    public const CSV = 'csv';
    public const XML = 'xml';

    public static function create(string $extension, $serializer): ParserInterface
    {
        return match ($extension) {
            self::CSV => new CsvParser($serializer),
            self::XML => new XmlParser($serializer),
            default => throw new \RuntimeException("Unsupported file type: {$extension}")
        };
    }
}
