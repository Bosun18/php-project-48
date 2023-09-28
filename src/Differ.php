<?php

namespace Differ\Differ;

use function Functional\sort;
use function Differ\Parsers\parse;
use function Differ\Formatter\getFormatter;

function buildTree(mixed $data1, mixed $data2): array
{
    $keys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
    $sortKeys = sort($keys, fn ($left, $right) => strcmp($left, $right));

    return array_map(
        function ($key) use ($data1, $data2) {
            if (
                array_key_exists($key, $data1) && array_key_exists($key, $data2)
                && is_array($data1[$key]) && is_array($data2[$key])
            ) {
                return [
                    'key' => $key,
                    'type' => 'nested',
                    'value1' => buildTree($data1[$key], $data2[$key]),
                    'value2' => buildTree($data1[$key], $data2[$key]),
                ];
            } elseif (!array_key_exists($key, $data2)) {
                return [
                    'key' => $key,
                    'type' => 'deleted',
                    'value1' => $data1[$key],
                    'value2' => null,
                ];
            } elseif (!array_key_exists($key, $data1)) {
                return  [
                    'key' => $key,
                    'type' => 'added',
                    'value1' => null,
                    'value2' => $data2[$key],
                ];
            } elseif ($data1[$key] !== $data2[$key]) {
                return  [
                    'key' => $key,
                    'type' => 'updated',
                    'value1' => $data1[$key],
                    'value2' => $data2[$key],
                ];
            } else {
                return [
                    'key' => $key,
                    'type' => 'immutable',
                    'value1' => $data1[$key],
                    'value2' => $data2[$key],
                ];
            }
        },
        $sortKeys
    );
}

function getResultValue(mixed $data): array
{
    return array_map(function ($value) {
        return match ($value) {
            false => 'false',
            true => 'true',
            null => 'null',
            default => is_array($value) ? getResultValue($value) : $value
        };
    }, $data);
}

/**
 * @throws \Exception
 */
function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string
{
//    $data1 = getResultValue(parse($pathToFile1));
//    $data2 = getResultValue(parse($pathToFile2));

    $data1 = parse($pathToFile1);
    $data2 = parse($pathToFile2);

    $diff = buildTree($data1, $data2);

    return getFormatter($diff, $format) . "\n";
}
