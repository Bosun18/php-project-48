<?php

namespace Differ\Differ;

use function Differ\Parsers\parse;
use function Differ\Formatter\getFormatter;

/**
 * @throws \Exception
 */
function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string
{
    $dataArray1 = parse($pathToFile1);
    $dataArray2 = parse($pathToFile2);

    $result = getFormatter($dataArray1, $dataArray2, $format);
    return $result . "\n";
}
