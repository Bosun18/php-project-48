<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

/**
 * @throws \Exception
 */
function getRealPath(string $pathToFile): string
{
    $fullPath = realpath($pathToFile);
    if ($fullPath === false) {
        throw new \Exception("File does not exists");
    }
    return $fullPath;
}

function getFormat(mixed $data): array
{
    return array_map(function ($value) {
        return match ($value) {
            false => 'false',
            true => 'true',
            null => 'null',
            default => is_array($value) ? getFormat($value) : $value
        };
    }, $data);
}

/**
 * @throws \Exception
 */
function parse(string $pathToFile): array
{
    $fullPath = getRealPath($pathToFile);
    $data = file_get_contents($fullPath);
    $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
    $array = match ($extension) {
        'yaml', 'yml' => Yaml::parse($data),
        'json' => json_decode($data, true),
        default => []
    };
    $result = getFormat($array);
    return $result;
}
