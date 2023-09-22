<?php

namespace Differ\Differ;

use function Differ\Parsers\parse;
use function Differ\Formatter\getFormatter;

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
function getRealPath(string $pathToFile): string
{
    $fullPath = realpath($pathToFile);
    if ($fullPath === false) {
        throw new \Exception("File does not exists");
    }
    return $fullPath;
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
function getParseData($pathToFile): array
{
    $extension = getExtension($pathToFile);
    $data = getData($pathToFile);
    return getResultValue(parse($data, $extension));
}

/**
 * @throws \Exception
 */
function getExtension($pathToFile): array|string
{
    $fullPath = getRealPath($pathToFile);
    return pathinfo($fullPath, PATHINFO_EXTENSION);
}

/**
 * @throws \Exception
 */
function getData(string $pathToFile): string|bool
{
    $fullPath = getRealPath($pathToFile);
    return file_get_contents($fullPath);
}

/**
 * @throws \Exception
 */
function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string
{
    $data1 = getParseData($pathToFile1);
    $data2 = getParseData($pathToFile2);
    $diff = getTree($data1, $data2);

    return getFormatter($diff, $format) . "\n";
}
