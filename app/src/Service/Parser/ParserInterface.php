<?php

declare(strict_types=1);

namespace App\Service\Parser;

interface ParserInterface
{
    public function parse(string $filePath): array;
}