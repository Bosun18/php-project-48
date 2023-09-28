<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

/**
 * @throws \Exception
 */
function getFileData(string $pathToFile): string
{
    $data = file_get_contents($pathToFile);
    if (file_exists($pathToFile)) {
        return $data;
    }
    throw new \Exception("File not found", 1);
}

/**
 * @throws \Exception
 */
function parse(string $pathToFile): array
{
    $data = getFileData($pathToFile);
    $extension = pathinfo($pathToFile, PATHINFO_EXTENSION);
    $result = match ($extension) {
        'yaml', 'yml' => Yaml::parse($data),
        'json' => json_decode($data, true),
        default => throw new \Exception('Unknown extension ' . $extension)
    };
    return $result;
}
