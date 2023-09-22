<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

/**
 * @throws \Exception
 */
function parse(string $data, $extension): array
{
    $result = match ($extension) {
        'yaml', 'yml' => Yaml::parse($data),
        'json' => json_decode($data, true),
        default => throw new \Exception('Unknown extension ' . $extension)
    };
    return $result;
}
