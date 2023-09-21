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
                $nestedComparison = getTree($array1[$key], $array2[$key]);

                return [
                    'key' => $key,
                    'type' => 'nested',
                    'value1' => $nestedComparison,
                    'value2' => $nestedComparison,
                ];
            } elseif (!array_key_exists($key, $array2)) {
                return [
                    'key' => $key,
                    'type' => 'deleted',
                    'value1' => $array1[$key],
                    'value2' => null,
                ];
            } elseif (!array_key_exists($key, $array1)) {
                return  [
                    'key' => $key,
                    'type' => 'added',
                    'value1' => null,
                    'value2' => $array2[$key],
                ];
            } elseif ($array1[$key] !== $array2[$key]) {
                return  [
                    'key' => $key,
                    'type' => 'updated',
                    'value1' => $array1[$key],
                    'value2' => $array2[$key],
                ];
            } else {
                return [
                    'key' => $key,
                    'type' => 'immutable',
                    'value1' => $array1[$key],
                    'value2' => $array2[$key],
                ];
            }
        },
        $keys
    );

    return $result;
}


/**
 * @throws \Exception
 */
function getFormatter(mixed $dataArray1, mixed $dataArray2, string $format): string
{
    $result = '';
    $diffArray = getTree($dataArray1, $dataArray2);
    if ($format === 'stylish') {
        $result = getStrings($diffArray, ' ', 4);
    }
    if ($format === 'plain') {
        $result = getChange($diffArray);
    }
    return $result;
}
