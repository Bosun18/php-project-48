<?php

namespace Differ\Parsers;

use Exception;
use Symfony\Component\Yaml\Yaml;

/**
 * @throws Exception
 */
function parse(mixed $data, string $extension): array
{
    return match ($extension) {
        'yaml', 'yml' => Yaml::parse($data),
        'json' => json_decode($data, true),
        default => throw new Exception('Unknown extension ' . $extension)
    };
}
