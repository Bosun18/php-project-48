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
            $value = $data1[$key];
            $value2 = $data2[$key];
            if (
                array_key_exists($key, $data1) && array_key_exists($key, $data2)
                && is_array($value) && is_array($value2)
            ) {
                return [
                    'key' => $key,
                    'type' => 'nested',
                    'value' => buildTree($value, $value2),
                ];
            } elseif (!array_key_exists($key, $data2)) {
                return [
                    'key' => $key,
                    'type' => 'deleted',
                    'value' => $value,
                ];
            } elseif (!array_key_exists($key, $data1)) {
                return  [
                    'key' => $key,
                    'type' => 'added',
                    'value' => $value2,
                ];
            } elseif ($value !== $value2) {
                return  [
                    'key' => $key,
                    'type' => 'updated',
                    'value' => $value,
                    'value2' => $value2,
                ];
            } else {
                return [
                    'key' => $key,
                    'type' => 'immutable',
                    'value' => $value
                ];
            }
        },
        $sortKeys
    );
}

/**
 * @throws \Exception
 */
function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string
{
    $data1 = parse($pathToFile1);
    $data2 = parse($pathToFile2);

    $diff = buildTree($data1, $data2);

    return getFormatter($diff, $format);
}
