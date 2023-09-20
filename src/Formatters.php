<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\getFormat as getFormatStylish;
use function Differ\Formatters\Json\getFormat as getFormatJson;

/**
 * @throws \Exception
 */
function getFormatter(mixed $diff, string $format): string
{
    return match ($format) {
        'stylish' => getFormatStylish($diff),
        'json' => getFormatJson($diff),
        default => throw new \Exception('Unknown format ' . $format),
    };
}
