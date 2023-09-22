<?php

namespace Differ\Formatter;

use function Differ\Formatters\Stylish\getStylish;
use function Differ\Formatters\Plain\getPlain;
use function Differ\Formatters\Json\getJson;
use function Differ\Differ\getTree;

/**
 * @throws \Exception
 */
function getFormatter(mixed $data1, mixed $data2, string $format): string
{
    $diff = getTree($data1, $data2);
    $result = match ($format) {
        'stylish' => getStylish($diff, ' ', 4),
        'plain' => getPlain($diff),
        'json' => getJson($diff),
        default => throw new \Exception('Unknown format ' . $format),
    };
    return $result;
}
