<?php

namespace Differ\Formatter;

use function Differ\Formatters\Stylish\getFormat as getStylish;
use function Differ\Formatters\Plain\getFormat as getPlain;
use function Differ\Formatters\Json\getFormat as getJson;

/**
 * @throws \Exception
 */
function format(array $diff, string $format): string
{
    return match ($format) {
        'stylish' => getStylish($diff),
        'plain' => getPlain($diff),
        'json' => getJson($diff),
        default => throw new \Exception('Unknown format ' . $format),
    };
}
