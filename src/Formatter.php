<?php

namespace Differ\Formatter;

use function Differ\Formatters\Stylish\getStrings;
use function Differ\Formatters\Plain\getChange;

function getTree(mixed $array1, mixed $array2): array
{
    $keys = array_unique(array_merge(array_keys($array1), array_keys($array2)));
    sort($keys, SORT_REGULAR);

    $result = array_map(
        function ($key) use ($array1, $array2) {
            if (
                array_key_exists($key, $array1) && array_key_exists($key, $array2)
                && is_array($array1[$key]) && is_array($array2[$key])
            ) {
                $nestedCompare = getTree($array1[$key], $array2[$key]);
                return ['key' => $key, 'type' => 'nested', 'value1' => $nestedCompare, 'value2' => $nestedCompare,];
            } elseif (!array_key_exists($key, $array2)) {
                return ['key' => $key, 'type' => 'deleted', 'value1' => $array1[$key], 'value2' => null,];
            } elseif (!array_key_exists($key, $array1)) {
                return  ['key' => $key, 'type' => 'added', 'value1' => null, 'value2' => $array2[$key],];
            } elseif ($array1[$key] !== $array2[$key]) {
                return  ['key' => $key, 'type' => 'updated', 'value1' => $array1[$key], 'value2' => $array2[$key],];
            } else {
                return ['key' => $key, 'type' => 'immutable', 'value1' => $array1[$key], 'value2' => $array2[$key],];
            }
        },
        $keys
    );
    return $result;
}

function getFormatter(mixed $data1, mixed $data2, string $format): string
{
    $diff = getTree($data1, $data2);
    $result = match ($format) {
        'stylish' => getStrings($diff, ' ', 4),
        'plain' => getChange($diff),
        default => '',
    };
    return $result;
}
