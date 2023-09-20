<?php

namespace Differ\Differ;

use function Differ\Parsers\getParseCode;
use function Differ\Stylish\getStringsTree;

function getArrayComparisonTree(mixed $array1, mixed $array2): mixed
{
    $result = [];

    $keys = array_unique(array_merge(array_keys($array1), array_keys($array2)));

    sort($keys, SORT_REGULAR);

    foreach ($keys as $key) {
        if (
            array_key_exists($key, $array1) && array_key_exists($key, $array2)
            && is_array($array1[$key]) && is_array($array2[$key])
        ) {
            $nestedComparison = getArrayComparisonTree($array1[$key], $array2[$key]);

            $result[] = [
                'key' => $key,
                'type' => 'immutable',
                'value' => $nestedComparison,
            ];
        } elseif (!array_key_exists($key, $array2)) {
            $result[] = [
                'key' => $key,
                'type' => 'deleted',
                'value' => $array1[$key],
            ];
        } elseif (!array_key_exists($key, $array1)) {
            $result[] = [
                'key' => $key,
                'type' => 'added',
                'value' => $array2[$key],
            ];
        } elseif ($array1[$key] !== $array2[$key]) {
            $result[] = [
                'key' => $key,
                'type' => 'deleted',
                'value' => $array1[$key],
            ];
            $result[] = [
                'key' => $key,
                'type' => 'added',
                'value' => $array2[$key],
            ];
        } else {
            $result[] = [
                'key' => $key,
                'type' => 'immutable',
                'value' => $array1[$key],
            ];
        }
    }

    return $result;
}

function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string
{

    $dataArray1 = getParseCode($pathToFile1);
    $dataArray2 = getParseCode($pathToFile2);

    $diffArray = getArrayComparisonTree($dataArray1, $dataArray2);

    $result = '';

    if ($format === 'stylish') {
        $result = getStringsTree($diffArray, $replacer = ' ', $spaceCount = 4);
    }

    return $result . "\n";
}
