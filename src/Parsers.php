<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

/**
 * @throws \Exception
 */
function getData(string $path): string
{
    $data = file_get_contents($path);
    if ($data !== false) {
        return $data;
    }
    throw new \Exception("File not found", 1);
}

/**
 * @throws \Exception
 */
function parse(string $path): array
{
    $data = getData($path);
    $extension = pathinfo($path, PATHINFO_EXTENSION);
    return match ($extension) {
        'json' => json_decode($data, true),
        'yml', 'yaml' => Yaml::parse($data),
        default => throw new \Exception("Неизвестный формат", 1),
    };
}
