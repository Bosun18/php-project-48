<?php

namespace Differ\Formatter;

use function Differ\Formatters\Stylish\getStylish;
use function Differ\Formatters\Plain\getPlain;
use function Differ\Formatters\Json\getJson;

/**
 * @throws \Exception
 */
function getFormatter(mixed $diff, string $format): string
{
    $result = match ($format) {
        'stylish' => getStylish($diff),
        'plain' => getPlain($diff),
        'json' => getJson($diff),
        default => throw new \Exception('Unknown format ' . $format),
    };
    return $result;
}
